<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppraisalRequest;
use App\Models\AppraisalPhoto;
use App\Services\FcmService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AppraisalRequestController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all user's appraisal requests
     */
    public function index(Request $request)
    {
        // 1. Mulai Query dari user yang sedang login
        $query = $request->user()->appraisalRequests()->with('photos');

        // 2. SEARCH (Mencari di Brand, Model, atau Deskripsi)
        // Contoh: ?search=honda jazz
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('vehicle_brand', 'like', "%{$searchTerm}%")
                    ->orWhere('vehicle_model', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // 3. FILTERING
        // Filter Status (Contoh: ?status=draft)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter Tahun (Contoh: ?year_min=2020&year_max=2025)
        if ($request->has('year_min')) {
            $query->where('year_manufacture', '>=', $request->year_min);
        }
        if ($request->has('year_max')) {
            $query->where('year_manufacture', '<=', $request->year_max);
        }

        // 4. SORTING
        // Default: created_at desc (Terbaru diatas)
        // Contoh: ?sort_by=year_manufacture&sort_dir=asc
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_dir', 'desc');

        // Whitelist kolom biar user gak aneh-aneh sort-nya
        $allowedSorts = ['created_at', 'updated_at', 'year_manufacture', 'vehicle_brand', 'final_price'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // 5. CURSOR PAGINATION
        // Default 10 item per load
        $perPage = $request->input('per_page', 10);

        // PENTING: Cursor pagination butuh order by yang unik, jadi kita tambahin id sebagai tie-breaker
        $appraisals = $query->orderBy('id', 'desc')->cursorPaginate($perPage);

        return $this->cursorPaginatedResponse($appraisals, 'Appraisal requests retrieved successfully');
    }

    /**
     * Get the latest appraisal request for the authenticated user.
     * Used by the home screen to display the active appraisal status card.
     */
    public function latest(Request $request)
    {
        $appraisal = $request->user()->appraisalRequests()
            ->with('photos')
            ->latest()
            ->first();

        if (!$appraisal) {
            return $this->notFoundResponse('No appraisal request found');
        }

        return $this->successResponse($appraisal, 'Latest appraisal request retrieved successfully');
    }

    /**
     * Create new draft appraisal request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_brand'    => 'required|string|max:255',
            'vehicle_model'    => 'required|string|max:255',
            'year_manufacture' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'description'      => 'nullable|string',
            'license_plate'    => 'nullable|string|max:20',
            'mileage'          => 'nullable|integer|min:0',
            'photos'           => 'nullable|array',
            'photos.*'         => 'image|mimes:jpeg,png,jpg|max:5120',
            'photo_labels'     => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            DB::beginTransaction();

            $appraisal = $request->user()->appraisalRequests()->create([
                'vehicle_brand'    => $request->vehicle_brand,
                'vehicle_model'    => $request->vehicle_model,
                'year_manufacture' => $request->year_manufacture,
                'description'      => $request->description,
                'license_plate'    => $request->license_plate,
                'mileage'          => $request->mileage,
                'status'           => 'draft',
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $path = $photo->store('appraisal_photos/' . $appraisal->id, 'public');
                    $label = $request->photo_labels[$index] ?? 'Photo ' . ($index + 1);

                    $appraisal->photos()->create([
                        'category_name' => $label,
                        'image_path'    => $path,
                    ]);
                }
            }

            DB::commit();

            $appraisal->load('photos');

            return $this->successResponse($appraisal, 'Appraisal request created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create appraisal request: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get single appraisal request detail
     */
    public function show(Request $request, $id)
    {
        $appraisal = $request->user()->appraisalRequests()
            ->with('photos')
            ->find($id);

        if (!$appraisal) {
            return $this->notFoundResponse('Appraisal request not found');
        }

        return $this->successResponse($appraisal, 'Appraisal request retrieved successfully');
    }

    /**
     * Update appraisal request (only draft status)
     */
    public function update(Request $request, $id)
    {
        $appraisal = $request->user()->appraisalRequests()->find($id);

        if (!$appraisal) {
            return $this->notFoundResponse('Appraisal request not found');
        }

        if ($appraisal->status !== 'draft') {
            return $this->errorResponse('Cannot update submitted appraisal request', null, 403);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_brand'    => 'sometimes|string|max:255',
            'vehicle_model'    => 'sometimes|string|max:255',
            'year_manufacture' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'description'      => 'nullable|string',
            'license_plate'    => 'nullable|string|max:20',
            'mileage'          => 'nullable|integer|min:0',
            'new_photos'       => 'nullable|array',
            'new_photos.*'     => 'image|mimes:jpeg,png,jpg|max:5120',
            'new_photo_labels' => 'nullable|array',
            'delete_photos'    => 'nullable|array',
            'delete_photos.*'  => 'exists:appraisal_photos,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            DB::beginTransaction();

            $appraisal->update($request->only(['vehicle_brand', 'vehicle_model', 'year_manufacture', 'description', 'license_plate', 'mileage']));

            if (!empty($request->delete_photos)) {
                $photosToDelete = $appraisal->photos()->whereIn('id', $request->delete_photos)->get();
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->image_path);
                    $photo->delete();
                }
            }

            if ($request->hasFile('new_photos')) {
                foreach ($request->file('new_photos') as $index => $photo) {
                    $path = $photo->store('appraisal_photos/' . $appraisal->id, 'public');
                    $label = $request->new_photo_labels[$index] ?? 'Photo ' . ($index + 1);

                    $appraisal->photos()->create([
                        'category_name' => $label,
                        'image_path'    => $path,
                    ]);
                }
            }

            DB::commit();

            $appraisal->load('photos');

            return $this->successResponse($appraisal, 'Appraisal request updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update appraisal request: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Submit appraisal request for review
     */
    public function submit(Request $request, $id)
    {
        $appraisal = $request->user()->appraisalRequests()
            ->with('photos')
            ->find($id);

        if (!$appraisal) {
            return $this->notFoundResponse('Appraisal request not found');
        }

        if ($appraisal->status !== 'draft') {
            return $this->errorResponse('Appraisal already submitted', null, 400);
        }

        if ($appraisal->photos->count() === 0) {
            return $this->errorResponse('Please upload at least one photo before submitting', null, 400);
        }

        $appraisal->update(['status' => 'submitted']);

        // Kirim Notifikasi ke Admin
        $admins = User::where('role', 'admin')->get();
        $adminTokens = [];

        $user = $request->user();
        $title = 'New Appraisal Submitted';
        $body = "A new appraisal request for {$appraisal->vehicle_brand} {$appraisal->vehicle_model} has been submitted by {$user->name}.";
        $data = [
            'type' => 'appraisal_submitted',
            'appraisal_id' => (string) $appraisal->id,
        ];

        foreach ($admins as $admin) {
            // Simpan ke database untuk setiap admin
            $admin->notifications()->create([
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);

            if ($admin->fcm_token) {
                $adminTokens[] = $admin->fcm_token;
            }
        }

        if (!empty($adminTokens)) {
            FcmService::sendToTokens($adminTokens, $title, $body, $data);
        }

        return $this->successResponse($appraisal, 'Appraisal submitted successfully');
    }

    /**
     * Delete appraisal request (only draft)
     */
    public function destroy(Request $request, $id)
    {
        $appraisal = $request->user()->appraisalRequests()->find($id);

        if (!$appraisal) {
            return $this->notFoundResponse('Appraisal request not found');
        }

        if ($appraisal->status !== 'draft') {
            return $this->errorResponse('Cannot delete submitted appraisal request', null, 403);
        }

        foreach ($appraisal->photos as $photo) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $appraisal->delete();

        return $this->successResponse(null, 'Appraisal request deleted successfully');
    }
}
