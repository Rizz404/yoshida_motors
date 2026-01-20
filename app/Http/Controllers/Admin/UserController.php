<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'address' => 'nullable|string',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            User::create($validated);

            return redirect()->route('users.index')->with('notify', [
                'type' => 'success',
                'title' => 'Success',
                'message' => 'The user has been created successfully.',
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
                'title' => 'Operation Failed',
                'message' => 'An error occurred while creating the user. Please try again.',
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
            'role' => 'required|in:user,admin',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('users.index')->with('notify', [
                'type' => 'success',
                'title' => 'Success',
                'message' => 'User details have been updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->withInput()->with('notify', [
                'type' => 'error',
                'title' => 'Update Failed',
                'message' => 'Unable to update user details due to a system error.',
                'details' => $errorDetails,
            ]);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            return redirect()->route('users.index')->with('notify', [
                'type' => 'success',
                'title' => 'Deleted',
                'message' => 'The user has been removed from the system.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            $errorDetails = null;
            if (app()->environment('local')) {
                $errorDetails = $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
            }

            return back()->with('notify', [
                'type' => 'error',
                'title' => 'Deletion Failed',
                'message' => 'Unable to delete the user. Please check system logs.',
                'details' => $errorDetails,
            ]);
        }
    }
}
