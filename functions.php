<?php

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    header('location: index.html');
}

/**
 * Checks if request is AJAX or POST to prevent manual URL input vulnerability
 */
$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
if (!$isAjax) {
    $response = [
      'code' => 401,
      'message' => 'Unauthorized request!'
    ];

    $response = json_encode($response);

    exit($response);  
}

function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception(".env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {

        if (str_starts_with(trim($line), '#')) {
            continue; // skip comments
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remove optional quotes
        $value = trim($value, "\"'");

        // Set to environment
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

function httpPostToLambda($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Accept: */*"
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

function httpPostToOpenAi($url, $data, $apiKey)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$apiKey}",
        "Content-Type: application/json"
    ]);
    
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}
