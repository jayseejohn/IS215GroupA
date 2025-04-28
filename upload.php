<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <link rel="icon" href="assets/images/group_a.png" type="image/gif" />
  <title>Group A</title>
  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />
  <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
  <link href="assets/css/responsive.css" rel="stylesheet" />
<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Lambda\LambdaClient;
use Dotenv\Dotenv;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$uploadMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    if ($file['error'] === 0) {
        $tmpPath = $file['tmp_name'];
        $fileName = basename($file['name']);

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                'token'  => $_ENV['AWS_SESSION_TOKEN'],
            ]
        ]);
        
        $lambda = new LambdaClient([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                'token'  => $_ENV['AWS_SESSION_TOKEN'],
            ]
        ]);

        try {
            
            $s3->putObject([
                'Bucket' => $_ENV['AWS_BUCKET'],
                'Key'    => $fileName,
                'SourceFile' => $tmpPath,
                'ContentType' => mime_content_type($tmpPath)
            ]);

            // Invoke Lambda directly
            $result = $lambda->invoke([
                'FunctionName' => $_ENV['AWS_LAMBDA_FUNCTION_NAME'],
                'InvocationType' => 'Event',
                'Payload' => json_encode([
                    'bucket' => $_ENV['AWS_BUCKET'],
                    'key'    => $fileName
                ]),
            ]);

            $uploadMessage = '<div class="alert alert-success">Upload successful! Lambda processing started. <a href="index.php" class="alert-link">View article</a></div>';

        } catch (Exception $e) {
            $uploadMessage = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $uploadMessage = '<div class="alert alert-warning">Upload failed with error code: ' . $file['error'] . '</div>';
    }
}
?>

</head>
<body>
  <div class="content_area">
    <header class="header_section long_section px-0">
      <nav class="navbar navbar-expand-lg custom_nav-container ">
        <a class="navbar-brand" href="our-team.php">
          <span>
            Group A
          </span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class=""> </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
            <ul class="navbar-nav  ">
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">BACANTO</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">CORDOVA</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">DELFIN</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">GULAN</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">MANGIBIN</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">MIRABETE</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="our-team.php">PINGKIAN</a>
              </li>
            </ul>
          </div>
          <div class="quote_btn-container">
            <a href="index.php">
              <span>
                View Articles
              </span>
              <i class="fa fa-home" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </nav>
    </header>
      <section class="blog_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Upload a New Image
        </h2>
      </div>
      <div class="row">
        <?= $uploadMessage ?>

        <form method="POST" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-white">
            <div class="mb-3">
                <label for="image" class="form-label">Select Image</label>
                <input class="form-control" type="file" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="index.php" class="btn btn-outline-secondary ms-2">Back to Articles</a>
        </form>
      </div>
    </div>
  </section>
  <!-- jQery -->
  <script src="assets/js/jquery-3.4.1.min.js"></script>
  <!-- bootstrap js -->
  <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>