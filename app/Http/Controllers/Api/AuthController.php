<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
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

            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            // Create new user
            $user = User::create([
                'firebase_uid' => $firebaseUid,
                'phone_number' => $request->phone_number,
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'fcm_token' => $request->fcm_token,
                'profile_photo' => $profilePhotoPath,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'auth_provider' => 'phone',
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
     * Login or Register with Firebase Phone OTP (combined endpoint)
     *
     * FLOW:
     * 1. Frontend: User input nomor telepon
     * 2. Frontend: Firebase kirim OTP ke nomor telepon
     * 3. Frontend: User input kode OTP
     * 4. Frontend: Firebase verify OTP → dapat ID Token
     * 5. Frontend: Kirim ID Token ke endpoint ini
     * 6. Backend: Verify ID Token → cari user by UID
     *    - Jika ada → login (is_new_user: false) → navigasi ke home_screen
     *    - Jika tidak ada → auto-register (is_new_user: true) → navigasi ke profile_screen
     *
     * NOTE: Digunakan oleh register_screen.dart setelah OTP berhasil diverifikasi.
     */
    public function loginOrRegisterWithPhone(Request $request)
    {
        if ($this->firebaseNotAvailable()) {
            return $this->errorResponse('Firebase service is not configured correctly.', null, 503);
        }

        $validator = Validator::make($request->all(), [
            'id_token'     => 'required|string',
            'phone_number' => 'nullable|string',
            'fcm_token'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Verify Firebase ID Token (dari hasil OTP verification)
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);

            // Extract Firebase UID from verified token
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            // Extract phone number from token or request
            $phoneFromToken = $verifiedIdToken->claims()->get('phone_number');
            $phone = $phoneFromToken ?? $request->phone_number;

            // Find existing user by Firebase UID
            $user = User::where('firebase_uid', $firebaseUid)->first();

            $isNewUser = false;

            if (!$user) {
                // Auto-register baru — profil akan diisi di profile_screen
                $user = User::create([
                    'firebase_uid' => $firebaseUid,
                    'phone_number' => $phone,
                    'fcm_token'    => $request->fcm_token,
                    'auth_provider' => 'phone',
                    'role'         => 'user',
                ]);
                $isNewUser = true;
            } else {
                // Update FCM token & pastikan phone number ter-sync
                $updates = [];
                if ($request->has('fcm_token')) {
                    $updates['fcm_token'] = $request->fcm_token;
                }
                if ($phone && !$user->phone_number) {
                    $updates['phone_number'] = $phone;
                }
                if (!empty($updates)) {
                    $user->update($updates);
                }
            }

            // Create Sanctum token
            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return $this->successResponse([
                'user'        => $user,
                'token'       => $token,
                'is_new_user' => $isNewUser,
            ], $isNewUser ? 'Registration successful' : 'Login successful', $isNewUser ? 201 : 200);
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
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender'       => 'nullable|in:male,female,other',
            'birth_date'   => 'nullable|date',
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

            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            // Create new user
            $user = User::create([
                'firebase_uid' => $firebaseUid,
                'email'        => $email,
                'name'         => $request->name,
                'phone_number' => $request->phone_number,
                'address'      => $request->address,
                'fcm_token'    => $request->fcm_token,
                'profile_photo' => $profilePhotoPath,
                'gender'       => $request->gender,
                'birth_date'   => $request->birth_date,
                'auth_provider' => 'email',
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
                    'auth_provider' => 'google',
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
        $user = $request->user();

        $rules = [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string',
            'fcm_token' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
        ];

        // Hanya boleh update phone_number jika provider bukan phone
        if ($user->auth_provider !== 'phone') {
            $rules['phone_number'] = 'nullable|string|unique:users,phone_number,' . $user->id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $profilePhotoPath;
        }

        $fieldsToUpdate = ['name', 'email', 'address', 'fcm_token', 'gender', 'birth_date'];

        if ($user->auth_provider !== 'phone' && $request->has('phone_number')) {
            $fieldsToUpdate[] = 'phone_number';
        }

        $user->update($request->only($fieldsToUpdate));

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
