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
     * Register with Firebase Phone Authentication
     *
     * FLOW:
     * 1. Frontend: User input nomor telepon
     * 2. Frontend: Firebase kirim OTP ke nomor telepon
     * 3. Frontend: User input kode OTP
     * 4. Frontend: Firebase verify OTP → dapat ID Token
     * 5. Frontend: Kirim ID Token + data user ke endpoint ini
     * 6. Backend: Verify ID Token → extract UID → register user
     */
    public function registerWithFirebase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string', // ID Token dari Firebase Phone Auth
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
            // Verify Firebase ID Token (dari hasil OTP verification)
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);

            // Extract Firebase UID from verified token
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            // Check if user already exists
            $existingUser = User::where('firebase_uid', $firebaseUid)->first();
            if ($existingUser) {
                return $this->errorResponse('User already registered. Please login instead.', null, 409);
            }

            // Create new user
            $user = User::create([
                'firebase_uid' => $firebaseUid,
                'phone_number' => $request->phone_number,
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'fcm_token' => $request->fcm_token,
                'role' => 'user',
            ]);

            // Create Sanctum token
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
     * Login with Firebase Phone Authentication
     *
     * FLOW:
     * 1. Frontend: User input nomor telepon
     * 2. Frontend: Firebase kirim OTP ke nomor telepon
     * 3. Frontend: User input kode OTP
     * 4. Frontend: Firebase verify OTP → dapat ID Token
     * 5. Frontend: Kirim ID Token ke endpoint ini
     * 6. Backend: Verify ID Token → extract UID → login user
     */
    public function loginWithFirebase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string', // ID Token dari Firebase Phone Auth
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token (dari hasil OTP verification)
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);

            // Extract Firebase UID from verified token
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            // Find user by Firebase UID
            $user = User::where('firebase_uid', $firebaseUid)->first();

            if (!$user) {
                return $this->errorResponse('User not found. Please register first.', null, 404);
            }

            // Update FCM token if provided
            if ($request->has('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            // Create Sanctum token
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
