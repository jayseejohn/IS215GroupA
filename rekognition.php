<?php
use Aws\Rekognition\RekognitionClient;

function getImageLabels($filename) {
    $client = new RekognitionClient([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key' => $_ENV['AWS_ACCESS_KEY'],
            'secret' => $_ENV['AWS_SECRET_KEY'],
        ]
    ]);

    $result = $client->detectLabels([
        'Image' => [
            'S3Object' => [
                'Bucket' => $_ENV['AWS_BUCKET'],
                'Name' => $filename,
            ],
        ],
        'MaxLabels' => 10,
    ]);

    return array_column($result['Labels'], 'Name');
}
