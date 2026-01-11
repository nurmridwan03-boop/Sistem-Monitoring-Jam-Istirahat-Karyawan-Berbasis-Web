<?php
/**
 * Helper function untuk mengirim FCM notification
 * Compatible dengan InfinityFree hosting
 */

function sendFCMNotification($user_id, $title, $body, $data = []) {
    include 'db.php';
    
    // Ambil FCM tokens untuk user
    $tokens_query = mysqli_query($conn, 
        "SELECT token FROM fcm_tokens WHERE user_id = " . intval($user_id)
    );
    
    if (mysqli_num_rows($tokens_query) == 0) {
        return ['success' => false, 'message' => 'No FCM tokens found for user'];
    }
    
    $tokens = [];
    while ($row = mysqli_fetch_assoc($tokens_query)) {
        $tokens[] = $row['token'];
    }
    
    // Ambil server key dari config (harus diset di fcm_config.php)
    require_once 'fcm_config.php';
    
    if (empty($FCM_SERVER_KEY)) {
        return ['success' => false, 'message' => 'FCM Server Key not configured'];
    }
    
    $success_count = 0;
    $failure_count = 0;
    $invalid_tokens = [];
    
    // Kirim ke setiap token
    foreach ($tokens as $token) {
        $result = sendToFCM($token, $title, $body, $data, $FCM_SERVER_KEY);
        
        if ($result['success']) {
            $success_count++;
        } else {
            $failure_count++;
            // Jika token invalid, hapus dari database
            if (isset($result['error']) && 
                (strpos($result['error'], 'InvalidRegistration') !== false || 
                 strpos($result['error'], 'NotRegistered') !== false)) {
                mysqli_query($conn, "DELETE FROM fcm_tokens WHERE token = '" . mysqli_real_escape_string($conn, $token) . "'");
                $invalid_tokens[] = $token;
            }
        }
    }
    
    return [
        'success' => $success_count > 0,
        'success_count' => $success_count,
        'failure_count' => $failure_count,
        'invalid_tokens' => $invalid_tokens
    ];
}

/**
 * Mengirim notification ke satu token
 */
function sendToFCM($token, $title, $body, $data = [], $server_key) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    
    $notification = [
        'title' => $title,
        'body' => $body,
        'icon' => '/assets/favicon.ico',
        'badge' => '/assets/favicon.ico',
        'sound' => 'default'
    ];
    
    $fields = [
        'to' => $token,
        'notification' => $notification,
        'data' => array_merge($data, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']),
        'priority' => 'high',
        'webpush' => [
            'notification' => [
                'require_interaction' => false,
                'silent' => false
            ]
        ]
    ];
    
    $headers = [
        'Authorization: key=' . $server_key,
        'Content-Type: application/json'
    ];
    
    // Gunakan cURL jika tersedia, jika tidak gunakan file_get_contents
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Untuk InfinityFree
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            return ['success' => false, 'error' => $curl_error];
        }
    } else {
        // Fallback menggunakan file_get_contents
        $options = [
            'http' => [
                'header' => implode("\r\n", $headers) . "\r\n",
                'method' => 'POST',
                'content' => json_encode($fields),
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        $http_code = isset($http_response_header) ? (int)substr($http_response_header[0], 9, 3) : 0;
    }
    
    $result = json_decode($response, true);
    
    if ($http_code == 200 && isset($result['success']) && $result['success'] == 1) {
        return ['success' => true, 'message_id' => $result['results'][0]['message_id'] ?? null];
    } else {
        $error = isset($result['results'][0]['error']) ? $result['results'][0]['error'] : 'Unknown error';
        return ['success' => false, 'error' => $error, 'response' => $result];
    }
}
?>

