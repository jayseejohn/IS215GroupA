<?php

include('functions.php');

loadEnv(__DIR__ . '/.env');

$apiKey = getenv('OPENAI_API_KEY');
$url = getenv('OPENAI_URL');

$messages = $_POST['messages'];
$labels = $_POST['labels'];
$fileName = $_POST['file_name'];


$data = json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => $messages
]);

$result = httpPostToOpenAi($url, $data, $apiKey);
exit($result);
