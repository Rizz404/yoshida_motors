<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $firebaseAuth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));
        $this->firebaseAuth = $factory->createAuth();
    }

    /**
     * Register with Firebase
     * Mendaftarkan user baru dengan Firebase UID
     */
    public function registerWithFirebase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'phone_number' => 'required|string|unique:users,phone_number',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'address' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->firebase_uid);

            // Check if user already exists
            $existingUser = User::where('firebase_uid', $request->firebase_uid)->first();
            if ($existingUser) {
                return $this->errorResponse('User already registered. Please login instead.', null, 409);
            }

            // Create new user
            $user = User::create([
                'firebase_uid' => $request->firebase_uid,
                'phone_number' => $request->phone_number,
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'fcm_token' => $request->fcm_token,
                'role' => 'user',
            ]);

            // Create token
            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Registration successful', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Firebase authentication failed: ' . $e->getMessage(), null, 401);
        }
    }

    /**
     * Login with Firebase
     * Login untuk user yang sudah terdaftar
     */
    public function loginWithFirebase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->firebase_uid);

            // Find user by Firebase UID
            $user = User::where('firebase_uid', $request->firebase_uid)->first();

            if (!$user) {
                return $this->errorResponse('User not found. Please register first.', null, 404);
            }

            // Update FCM token if provided
            if ($request->has('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            // Create token
            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Firebase authentication failed: ' . $e->getMessage(), null, 401);
        }
    }

    /**
     * Update User Profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
            'address' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $user->update($request->only(['name', 'email', 'address', 'fcm_token']));

        return $this->successResponse($user, 'Profile updated successfully');
    }

    /**
     * Get Current User Profile
     */
    public function profile(Request $request)
    {
        return $this->successResponse($request->user(), 'Profile retrieved successfully');
    }

    /**
     * Logout
     *
     * Revoke the current access token used for the request.
     * This is the correct way to logout in Sanctum API authentication.
     */
    public function logout(Request $request)
    {
        // Revoke current access token - this is the correct Sanctum way
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();

        return $this->successResponse(null, 'Logout successful');
    }
}
