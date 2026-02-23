<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\Log;

class FcmService
{
  /**
   * Send a push notification to a specific FCM token.
   *
   * @param string $token
   * @param string $title
   * @param string $body
   * @param array $data
   * @return bool
   */
  public static function sendToToken(string $token, string $title, string $body, array $data = []): bool
  {
    if (empty($token)) {
      return false;
    }

    try {
      $messaging = Firebase::messaging();

      $message = CloudMessage::fromArray([
        'token' => $token,
        'notification' => [
          'title' => $title,
          'body' => $body,
        ],
        'data' => $data,
      ]);

      $messaging->send($message);
      return true;
    } catch (\Exception $e) {
      Log::error('FCM Send Error: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Send a push notification to multiple FCM tokens.
   *
   * @param array $tokens
   * @param string $title
   * @param string $body
   * @param array $data
   * @return bool
   */
  public static function sendToTokens(array $tokens, string $title, string $body, array $data = []): bool
  {
    $validTokens = array_filter($tokens);
    if (empty($validTokens)) {
      return false;
    }

    try {
      $messaging = Firebase::messaging();

      $message = CloudMessage::fromArray([
        'notification' => [
          'title' => $title,
          'body' => $body,
        ],
        'data' => $data,
      ]);

      $messaging->sendMulticast($message, $validTokens);
      return true;
    } catch (\Exception $e) {
      Log::error('FCM Multicast Send Error: ' . $e->getMessage());
      return false;
    }
  }
}
