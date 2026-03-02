<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppraisalRequest;
use App\Models\AppraisalPhoto;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Buat Transaction biar aman
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Buat hapus/simpan file

class AppraisalRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kita load relasi user & photos biar nggak N+1 query ya Kak [web:1]
        // Sembunyikan status 'draft' karena masih dikerjakan oleh user dan belum dikirim
        $requests = AppraisalRequest::with(['user', 'photos'])
            ->where('status', '!=', 'draft')
            ->latest()
            ->paginate(10);

        return view('admin.appraisals.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Butuh data user buat dropdown 'Select User'
        $users = User::orderBy('name')->get();
        return view('admin.appraisals.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi yang ketat biar datanya cantik! ✨
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_brand' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'year_manufacture' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'status' => 'required|in:draft,submitted,under_review,completed,rejected',
            'final_price' => 'nullable|numeric|min:0',

            // Validasi Foto: Array of files, max 2MB per foto (opsional)
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            // Kalau mau kasih label per foto, bisa tambah input array 'photo_labels'
            'photo_labels' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi database

            // 1. Buat Request-nya dulu
            $appraisalRequest = AppraisalRequest::create([
                'user_id' => $validated['user_id'],
                'vehicle_brand' => $validated['vehicle_brand'],
                'vehicle_model' => $validated['vehicle_model'],
                'year_manufacture' => $validated['year_manufacture'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'final_price' => $validated['final_price'] ?? null,
                // 'admin_note' bisa ditambahin kalau ada inputnya
            ]);

            // 2. Handle Upload Foto (Kalau ada)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    // Simpan file ke storage (public/appraisal_photos)
                    $path = $photo->store('appraisal_photos', 'public');

                    // Ambil label kalau ada, kalau nggak default 'General'
                    $label = $request->input("photo_labels.$index") ?? 'General';

                    // Simpan ke database appraisal_photos
                    AppraisalPhoto::create([
                        'appraisal_request_id' => $appraisalRequest->id,
                        'category_name' => $label,
                        'image_path' => $path,
                    ]);
                }
            }

            DB::commit(); // Simpan permanen kalau semua lancar

            // Kirim Notifikasi ke User
            /** @var \App\Models\User $user */
            $user = User::find($validated['user_id']);
            if ($user) {
                $title = __('appraisals.fcm_created_title');
                $body = __('appraisals.fcm_created_body', ['brand' => $appraisalRequest->vehicle_brand, 'model' => $appraisalRequest->vehicle_model]);
                $data = [
                    'type' => 'appraisal_created',
                    'appraisal_id' => (string) $appraisalRequest->id,
                ];

                // Simpan ke database
                $user->notifications()->create([
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                ]);

                // Kirim push notif jika ada token
                if ($user->fcm_token) {
                    FcmService::sendToToken($user->fcm_token, $title, $body, $data);
                }
            }

            return redirect()->route('appraisals.index')->with('notify', [
                'type' => 'success',
                'title' => __('appraisals.notify_created_title'),
                'message' => __('appraisals.notify_created_message'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua kalau ada error
            Log::error('Error creating appraisal: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->withInput()->with('notify', [
                'type' => 'error',
                'title' => __('appraisals.notify_create_error_title'),
                'message' => __('appraisals.notify_create_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppraisalRequest $appraisal) // Route model binding
    {
        // Load data relasi biar di view bisa nampilin foto lama
        $appraisal->load(['photos', 'user']);

        $users = User::orderBy('name')->get();
        return view('admin.appraisals.edit', compact('appraisal', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppraisalRequest $appraisal)
    {
        $validated = $request->validate([
            'vehicle_brand' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'year_manufacture' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,submitted,under_review,completed,rejected',
            'final_price' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string',
            'price_valid_until' => 'nullable|date',

            // Validasi foto baru (jika ada upload tambahan)
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',

            // Validasi hapus foto lama (array ID foto)
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'exists:appraisal_photos,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update Data Utama
            $appraisal->update([
                'vehicle_brand' => $validated['vehicle_brand'],
                'vehicle_model' => $validated['vehicle_model'],
                'year_manufacture' => $validated['year_manufacture'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'final_price' => $validated['final_price'] ?? null,
                'admin_note' => $validated['admin_note'] ?? null,
                'price_valid_until' => $validated['price_valid_until'] ?? null,
            ]);

            // 2. Hapus Foto Lama (Jika dipilih user)
            if (!empty($validated['delete_photos'])) {
                $photosToDelete = AppraisalPhoto::whereIn('id', $validated['delete_photos'])
                    ->where('appraisal_request_id', $appraisal->id) // Security check
                    ->get();

                foreach ($photosToDelete as $photo) {
                    // Hapus file fisik dulu
                    if (Storage::disk('public')->exists($photo->image_path)) {
                        Storage::disk('public')->delete($photo->image_path);
                    }
                    // Hapus record DB
                    $photo->delete();
                }
            }

            // 3. Upload Foto Baru (Jika ada)
            if ($request->hasFile('new_photos')) {
                foreach ($request->file('new_photos') as $index => $photo) {
                    $path = $photo->store('appraisal_photos', 'public');
                    // Label default 'Additional Photo' atau ambil dari input lain
                    $label = $request->input("new_photo_labels.$index") ?? 'Additional Photo';

                    AppraisalPhoto::create([
                        'appraisal_request_id' => $appraisal->id,
                        'category_name' => $label,
                        'image_path' => $path,
                    ]);
                }
            }

            DB::commit();

            // Kirim Notifikasi ke User
            $user = $appraisal->user;
            if ($user) {
                if ($appraisal->status === 'rejected') {
                    $title = __('appraisals.fcm_rejected_title');
                    $body = __('appraisals.fcm_rejected_body', ['brand' => $appraisal->vehicle_brand, 'model' => $appraisal->vehicle_model]);
                    if ($appraisal->admin_note) {
                        $body .= __('appraisals.fcm_rejected_reason', ['reason' => $appraisal->admin_note]);
                    }
                } else {
                    $title = __('appraisals.fcm_updated_title');
                    $body = __('appraisals.fcm_updated_body', ['brand' => $appraisal->vehicle_brand, 'model' => $appraisal->vehicle_model, 'status' => $appraisal->status]);

                    if ($appraisal->final_price) {
                        $formattedPrice = number_format($appraisal->final_price, 0, '.', ',');
                        $body .= __('appraisals.fcm_updated_price', ['price' => $formattedPrice]);
                    }
                }

                $data = [
                    'type' => 'appraisal_updated',
                    'appraisal_id' => (string) $appraisal->id,
                    'status' => $appraisal->status,
                ];

                // Simpan ke database
                $user->notifications()->create([
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                ]);

                // Kirim push notif jika ada token
                if ($user->fcm_token) {
                    FcmService::sendToToken($user->fcm_token, $title, $body, $data);
                }
            }

            return redirect()->route('appraisals.index')->with('notify', [
                'type' => 'success',
                'title' => __('appraisals.notify_updated_title'),
                'message' => __('appraisals.notify_updated_message'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating appraisal: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->withInput()->with('notify', [
                'type' => 'error',
                'title' => __('appraisals.notify_update_error_title'),
                'message' => __('appraisals.notify_update_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppraisalRequest $appraisal)
    {
        try {
            DB::beginTransaction();

            // Hapus file fisik foto-fotonya dulu biar nggak nyampah di server [code:1]
            foreach ($appraisal->photos as $photo) {
                if (Storage::disk('public')->exists($photo->image_path)) {
                    Storage::disk('public')->delete($photo->image_path);
                }
            }

            // Delete record (Photos ikut terhapus karena on delete cascade di migration, tapi file fisik harus manual)
            $appraisal->delete();

            DB::commit();

            return redirect()->route('appraisals.index')->with('notify', [
                'type' => 'success',
                'title' => __('appraisals.notify_deleted_title'),
                'message' => __('appraisals.notify_deleted_message'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting appraisal: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->with('notify', [
                'type' => 'error',
                'title' => __('appraisals.notify_delete_error_title'),
                'message' => __('appraisals.notify_delete_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }
}
