<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $firebaseAuth;

    public function __construct()
    {
        try {
            $credentials = config('services.firebase.credentials');

            // Support both absolute paths and relative paths from base_path
            if ($credentials && !str_starts_with($credentials, '/') && !str_contains($credentials, ':\\') && !str_starts_with($credentials, '{')) {
                $credentials = base_path($credentials);
            }

            $factory = (new Factory)->withServiceAccount($credentials);
            $this->firebaseAuth = $factory->createAuth();
        } catch (\Exception $e) {
            $this->firebaseAuth = null;
            Log::error('Firebase initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Return error if Firebase is not initialized.
     */
    private function firebaseNotAvailable(): bool
    {
        return $this->firebaseAuth === null;
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
        if ($this->firebaseNotAvailable()) {
            return $this->errorResponse('Firebase service is not configured correctly.', null, 503);
        }

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
        if ($this->firebaseNotAvailable()) {
            return $this->errorResponse('Firebase service is not configured correctly.', null, 503);
        }

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
     * Register with Firebase Email/Password Authentication
     *
     * FLOW:
     * 1. Frontend: User input email + password
     * 2. Frontend: Firebase createUserWithEmailAndPassword() → dapat ID Token
     * 3. Frontend: Kirim ID Token + data user ke endpoint ini
     * 4. Backend: Verify ID Token → extract UID + email → register user
     */
    public function registerWithEmailPassword(Request $request)
    {
        if ($this->firebaseNotAvailable()) {
            return $this->errorResponse('Firebase service is not configured correctly.', null, 503);
        }

        $validator = Validator::make($request->all(), [
            'id_token'     => 'required|string',
            'name'         => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|unique:users,phone_number',
            'address'      => 'nullable|string',
            'fcm_token'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);

            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email       = $verifiedIdToken->claims()->get('email');

            if (!$email) {
                return $this->errorResponse('Email not found in Firebase token.', null, 422);
            }

            // Check if user already exists
            $existingUser = User::where('firebase_uid', $firebaseUid)
                ->orWhere('email', $email)
                ->first();

            if ($existingUser) {
                return $this->errorResponse('User already registered. Please login instead.', null, 409);
            }

            // Create new user
            $user = User::create([
                'firebase_uid' => $firebaseUid,
                'email'        => $email,
                'name'         => $request->name,
                'phone_number' => $request->phone_number,
                'address'      => $request->address,
                'fcm_token'    => $request->fcm_token,
                'role'         => 'user',
            ]);

            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return $this->successResponse([
                'user'  => $user,
                'token' => $token,
            ], 'Registration successful', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Firebase authentication failed: ' . $e->getMessage(), null, 401);
        }
    }

    /**
     * Login with Firebase Email/Password Authentication
     *
     * FLOW:
     * 1. Frontend: User input email + password
     * 2. Frontend: Firebase signInWithEmailAndPassword() → dapat ID Token
     * 3. Frontend: Kirim ID Token ke endpoint ini
     * 4. Backend: Verify ID Token → extract UID → login user
     */
    public function loginWithEmailPassword(Request $request)
    {
        if ($this->firebaseNotAvailable()) {
            return $this->errorResponse('Firebase service is not configured correctly.', null, 503);
        }

        $validator = Validator::make($request->all(), [
            'id_token'  => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);

            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            $user = User::where('firebase_uid', $firebaseUid)->first();

            if (!$user) {
                return $this->errorResponse('User not found. Please register first.', null, 404);
            }

            if ($request->has('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return $this->successResponse([
                'user'  => $user,
                'token' => $token,
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Firebase authentication failed: ' . $e->getMessage(), null, 401);
        }
    }

    /**
     * Login or Register with Google Sign-In (Firebase)
     *
     * FLOW:
     * 1. Frontend: User tap "Sign in with Google"
     * 2. Frontend: Firebase signInWithCredential(GoogleAuthProvider) → dapat ID Token
     * 3. Frontend: Kirim ID Token ke endpoint ini
     * 4. Backend: Verify ID Token → extract UID + email + name → auto register atau login
     *
     * NOTE: Google sign-in menggabungkan register & login dalam satu endpoint
     * karena Google sudah verify email, jadi aman untuk auto-create user.
     */
    public function loginWithGoogle(Request $request)
    {
        if ($this->firebaseNotAvailable()) {
            return $this->errorResponse('Firebase service is not configured correctly.', null, 503);
        }

        $validator = Validator::make($request->all(), [
            'id_token'  => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);

            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email       = $verifiedIdToken->claims()->get('email');
            $name        = $verifiedIdToken->claims()->get('name');

            if (!$email) {
                return $this->errorResponse('Email not found in Google token.', null, 422);
            }

            // Find existing user by firebase_uid or email
            $user = User::where('firebase_uid', $firebaseUid)
                ->orWhere('email', $email)
                ->first();

            $isNewUser = false;

            if (!$user) {
                // Auto-register: Google sudah verify email
                $user = User::create([
                    'firebase_uid' => $firebaseUid,
                    'email'        => $email,
                    'name'         => $name,
                    'fcm_token'    => $request->fcm_token,
                    'role'         => 'user',
                ]);
                $isNewUser = true;
            } else {
                // Update firebase_uid jika user sebelumnya daftar via phone/email-password
                $updates = ['firebase_uid' => $firebaseUid];
                if ($request->has('fcm_token')) {
                    $updates['fcm_token'] = $request->fcm_token;
                }
                $user->update($updates);
            }

            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return $this->successResponse([
                'user'       => $user,
                'token'      => $token,
                'is_new_user' => $isNewUser,
            ], $isNewUser ? 'Registration successful' : 'Login successful', $isNewUser ? 201 : 200);
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
