<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'password' => 'required|string|confirmed',
            'role' => 'required|in:user,admin,dealer',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            User::create($validated);

            return redirect()->route('users.index')->with('notify', [
                'type' => 'success',
                'title' => __('users.notify_created_title'),
                'message' => __('users.notify_created_message'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());

            // Show stack trace only in local environment
            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->withInput()->with('notify', [
                'type' => 'error',
                'title' => __('users.notify_create_error_title'),
                'message' => __('users.notify_create_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:user,admin,dealer',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'address' => 'nullable|string',
            'password' => 'nullable|string|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            $user->update($validated);

            return redirect()->route('users.index')->with('notify', [
                'type' => 'success',
                'title' => __('users.notify_updated_title'),
                'message' => __('users.notify_updated_message'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->withInput()->with('notify', [
                'type' => 'error',
                'title' => __('users.notify_update_error_title'),
                'message' => __('users.notify_update_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }

    public function destroy(User $user)
    {
        try {
            // Delete profile photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->delete();

            return redirect()->route('users.index')->with('notify', [
                'type' => 'success',
                'title' => __('users.notify_deleted_title'),
                'message' => __('users.notify_deleted_message'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->with('notify', [
                'type' => 'error',
                'title' => __('users.notify_delete_error_title'),
                'message' => __('users.notify_delete_error_message'),
                'details' => $errorDetails,
            ]);
        }
    }
}
