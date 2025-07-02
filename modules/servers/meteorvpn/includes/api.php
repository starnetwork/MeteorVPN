<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Perform an HTTP request to Defguard.
 *
 * @param string $method     HTTP verb: GET, POST, PUT, DELETE
 * @param string $endpoint   Path beginning with '/'. Example: '/api/v1/user'
 * @param mixed  $payload    PHP array to be JSON‑encoded (or NULL for none)
 * @param array  $params     Full $params passed by WHMCS to the provision fn
 *
 * @return array|mixed       Decoded JSON payload on success
 * @throws Exception         On transport or API error (non‑2xx response)
 */
function meteorvpn_callAPI(string $method, string $endpoint, $payload, array $params)
{
    // ── Normalise base URL
    $baseUrl = trim($params['configoption1']);
    // Remove trailing slash and any stray /api suffix supplied by admin.
    $baseUrl = preg_replace('#(/api)?/?$#i', '', $baseUrl);

    // Ensure endpoint starts with exactly one '/'
    $endpoint = '/' . ltrim($endpoint, '/');
    $url      = $baseUrl . $endpoint;

    $token     = $params['configoption2'];
    $verifyTls = $params['configoption3'] === 'on';
    $debug     = $params['configoption4'] === 'on';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'User-Agent: WHMCS-MeteorVPN-Module/1.1',
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => $verifyTls,
        CURLOPT_SSL_VERIFYHOST => $verifyTls ? 2 : 0,
    ]);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $body       = curl_exec($ch);
    $curlErrNo  = curl_errno($ch);
    $curlErrMsg = curl_error($ch);
    $status     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Debug logging (never throws)
    if ($debug) {
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0750, true);
        }
        $logFile = $logDir . '/meteorvpn_module.log';
        file_put_contents(
            $logFile,
            sprintf(
                "[%s] %s %s %d\nReq: %s\nRes: %s\n\n",
                date('c'),
                strtoupper($method),
                $url,
                $status,
                $payload ? json_encode($payload, JSON_UNESCAPED_SLASHES) : '-',
                $body
            ),
            FILE_APPEND
        );
    }

    if ($curlErrNo) {
        throw new Exception("cURL error ({$curlErrNo}): {$curlErrMsg}");
    }

    $decoded = json_decode($body, true);

    if ($status < 200 || $status >= 300) {
        // Better hint for 405 – usually bad base URL or wrong HTTP verb
        if ($status == 405) {
            $hint = 'Method Not Allowed';
        } elseif ($status == 400) {
            $hint = 'Bad Request';
        } else {
            $hint = is_array($decoded) && isset($decoded['msg']) ? $decoded['msg'] : 'Unknown error';
        }
        //var_dump($payload);die;
        throw new Exception("API responded {$status}: {$hint}");
    }

    return $decoded;
}

/* -----------------------------------------------------------------------------
 |  Validation helpers (basic client‑side sanity only)
 |-----------------------------------------------------------------------------*/
function meteorvpn_validateUserData(array $data)
{
    $required = ['username', 'first_name', 'last_name', 'email'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
}
