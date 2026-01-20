<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthController extends Controller
{
    public function loginWithFirebase(Request $request)
    {
        // 1. Validasi Input dari Flutter
        $request->validate([
            'phone_number' => 'required|string', // Format +62...
            'firebase_id_token' => 'required|string', // Token panjang dari Flutter
        ]);

        $auth = Firebase::auth();

        try {
            // 2. Verifikasi Token ke Server Firebase
            // Ini mastiin tokennya asli dari Firebase, bukan token palsu buatan hacker
            $verifiedIdToken = $auth->verifyIdToken($request->firebase_id_token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $firebasePhone = $verifiedIdToken->claims()->get('phone_number');

            // Cek apakah nomor HP match (Double check security)
            if ($firebasePhone !== $request->phone_number) {
                return response()->json(['message' => 'Phone number mismatch'], 401);
            }

            // 3. Cari User atau Buat Baru (First or Create)
            $user = User::firstOrCreate(
                ['phone_number' => $request->phone_number], // Cari berdasarkan no hp
                [
                    'name' => 'User ' . substr($request->phone_number, -4), // Default name
                    'role' => 'user',
                    'firebase_uid' => $uid,
                ]
            );

            // Update UID kalau user lama tapi UID baru (misal install ulang)
            if ($user->firebase_uid !== $uid) {
                $user->update(['firebase_uid' => $uid]);
            }

            // 4. Terbitkan Token Sanctum (Karcis Masuk)
            // Hapus token lama biar bersih (opsional, biar 1 device 1 token aktif)
            // $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (FailedToVerifyToken $e) {
            return response()->json(['message' => 'Invalid Firebase Token: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
