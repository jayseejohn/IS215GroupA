<?php

include('functions.php');

loadEnv(__DIR__ . '/.env');

/**
 * Validate image file
 */
$file = $_FILES['file'];
$fileName = $file['name'] ?? '';
$fileSize = $file['size'] ?? '';

$fileExceeedsLimit = ($fileSize / 1048576) > 5; // Boolean, verifies if image size is > 5MB in size
$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
$allowedExtensions = ['png', 'jpg', 'gif', 'webp'];
$invalidFile = false;

if (!in_array($fileExtension, $allowedExtensions) || $fileExceeedsLimit || empty($fileName)) {
    $invalidFile = true;
}

if ($invalidFile) {
    $extensions = json_encode($allowedExtensions);
    $response = [
      'code' => 401,
      'message' => "Invalid uploaded file. <br /><br /> Image must be {$extensions}. <br /><br /> You must upload a valid image and must not exceed 5MB in size."
    ];

    $response = json_encode($response);

    exit($response); 
}

/**
 * Build request to AWS Lambda
 */
$tmpName = $file['tmp_name'];
$headers = apache_request_headers();
$mimeType = mime_content_type($tmpName);
$curlFile = new CURLFile($tmpName, $mimeType, $fileName);

$url = getenv('AWS_LAMBDA_API'); // AWS Lambda Custom API Endpoint
$bucketName = getenv('AWS_BUCKET_NAME'); // AWS S3 Bucket Name for validation purposes in AWS Lambda

$data = [
  'file' => $curlFile,
  'filename' => $fileName,
  'contentType' => $mimeType,
  'bucketName' => $bucketName
];

$result = httpPostToLambda($url, $data, $headers);

exit($result);
