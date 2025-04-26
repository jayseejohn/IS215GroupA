<?php
use Aws\S3\S3Client;

function uploadToS3($file, $filename) {
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key' => $_ENV['AWS_ACCESS_KEY'],
            'secret' => $_ENV['AWS_SECRET_KEY'],
        ]
    ]);

    $result = $s3->putObject([
        'Bucket' => $_ENV['AWS_BUCKET'],
        'Key' => $filename,
        'SourceFile' => $file,
        
    ]);

    return $result['ObjectURL'];
}
