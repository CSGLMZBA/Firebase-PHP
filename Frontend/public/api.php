<?php
session_start();

define('API_BASE', 'http://localhost:8888/Firebase-PHP/backend/public/index.php');

function apiRequest(string $method, string $endpoint, ?array $body = null, bool $withAuth = true): array
{
    $url = rtrim(API_BASE, '/') . '/' . ltrim($endpoint, '/');
    $ch = curl_init($url);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    if ($withAuth && !empty($_SESSION['token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];
    }

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ];

    if ($body !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($response === false || $curlError) {
        return [
            'status' => 0,
            'data' => [],
            'raw' => '',
            'error' => 'cURL error: ' . $curlError,
            'content_type' => null,
        ];
    }

    $decoded = json_decode($response, true);
    $jsonError = json_last_error();

    if ($httpCode === 401 && $withAuth) {
        session_destroy();
    }

    return [
        'status' => $httpCode,
        'data' => is_array($decoded) ? $decoded : [],
        'raw' => $response,
        'error' => $jsonError === JSON_ERROR_NONE ? null : json_last_error_msg(),
        'content_type' => $contentType,
    ];
}