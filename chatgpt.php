<?php

function generateArticleFromLabels($labels) {
    $prompt = "Write a short fictional news article about a photo that includes the following elements:  " . implode(", ", $labels) . ". Make it interesting and creative.";

    $ch = curl_init($_ENV['CHATGPT_ENDPOINT']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$_ENV['CHATGPT_API_KEY']}",
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            "prompt" => $prompt,
            "temperature" => 0.8
        ])
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($response, true);

    return $json['article'] ?? 'Failed to generate article.';
}
