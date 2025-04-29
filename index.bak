
<?php
include('header.php');
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Dotenv\Dotenv;
use GuzzleHttp\Client;

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    if (
        empty($_ENV['AWS_ACCESS_KEY_ID']) ||
        empty($_ENV['AWS_SECRET_ACCESS_KEY']) ||
        empty($_ENV['AWS_SESSION_TOKEN']) ||
        empty($_ENV['AWS_DEFAULT_REGION']) ||
        empty($_ENV['AWS_BUCKET']) ||
        empty($_ENV['CHATGPT_ENDPOINT']) ||
        empty($_ENV['CHATGPT_API_KEY'])
    ) {
        throw new Exception('<div style="margin: 100px;" class="alert alert-warning">One or more required environment variables are missing.</div>');
    }

    $s3 = new S3Client([
        'version'     => 'latest',
        'region'      => $_ENV['AWS_DEFAULT_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            'token'  => $_ENV['AWS_SESSION_TOKEN'],
        ]
    ]);

    $bucket = $_ENV['AWS_BUCKET'];
    $images = $s3->listObjectsV2(['Bucket' => $bucket]);

    $client = new Client();

} catch (AwsException $e) {
    die('<div style="margin: 100px;" class="alert alert-warning"> AWS Error '.$e->getAwsErrorMessage().'<br/><br/>AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_SESSION_TOKEN expires, temporary security credentials is required.</div>');
} catch (Exception $e) {
    die('<div style="margin: 100px;" class="alert alert-warning">Error '.$e->getMessage().'</div>');
}
?>
  <section class="blog_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Latest Articles</h2>
      </div>
      <div class="row">
        <?php if (!empty($images['Contents'])): ?>
          <?php $i = 0; foreach ($images['Contents'] as $obj): ?>
            <?php
              $imageKey = $obj['Key'];

              
              if (strpos($imageKey, 'labels/') === 0) continue;

              $imageUrl = $s3->getObjectUrl($bucket, $imageKey);
              $labelKey = 'labels/' . pathinfo($imageKey, PATHINFO_FILENAME) . '.json';

              try {
                $labelObj = $s3->getObject(['Bucket' => $bucket, 'Key' => $labelKey]);
                $labelsData = json_decode((string) $labelObj['Body'], true);
                $labels = array_map(fn($label) => $label['Name'], $labelsData);
              } catch (AwsException $e) {
                $labels = ['Unknown'];
              }

              $prompt = "Write a short fictional news article about a photo that includes the following elements:  " . implode(", ", $labels) . ". Make it interesting and creative.";

              $response = $client->post($_ENV['CHATGPT_ENDPOINT'], [
                'headers' => [
                  'Authorization' => 'Bearer ' . $_ENV['CHATGPT_API_KEY'],
                  'Content-Type' => 'application/json',
                ],
                'json' => [
                  'model' => 'gpt-3.5-turbo',
                  'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                  ],
                ]
              ]);

              $chatResponse = json_decode($response->getBody(), true);
              $article = $chatResponse['choices'][0]['message']['content'];
              $modalId = 'modal' . $i++;
            ?>
            <div class="col-md-6 col-lg-4 mx-auto">
              <div class="box">
                <div class="img-box">
                  <img src="<?= htmlspecialchars($imageUrl) ?>" class="card-img-top" alt="Image" style="height: 200px; object-fit: cover;" loading="lazy">
                </div>
                <div class="detail-box">
                  <p><strong>Labels:</strong> <?= implode(', ', $labels) ?></p>
                  <button type="button" class="btn btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                    Read Article
                  </button>

                  <!-- Modal -->
                  <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="<?= $modalId ?>Label">News Article</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid mb-3" alt="Image">
                          <p><?= nl2br(htmlspecialchars($article)) ?></p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="margin: 100px;" class="alert alert-warning">No images found.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>

<?php include('footer.php') ?>