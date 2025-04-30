
<?php include('header.php'); ?>

<section class="blog_section layout_padding">
  <div class="row p-0 m-0 w-100">
    <div class="container">
      <div class="heading_container">
        <h2>
          Upload a New Image
        </h2>
      </div>

      <div class="row">
        <p>
          <a role="button" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            How articles are generated using this site?
          </a>
        </p>
        <div class="collapse" id="collapseExample">
          <div class="alert alert-info">
            This site utilizes different Amazon Services such as
            <a href="https://aws.amazon.com/pm/serv-s3/" target="_blank">Amazon S3</a>,
            <a href="https://aws.amazon.com/pm/lambda/" target="_blank">AWS Lambda</a>,
            and
            <a href="https://aws.amazon.com/rekognition/" target="_blank">Amazon Rekognition</a>
            as well as <a href="https://openai.com/" target="_blank">OpenAI</a>
            to generate the articles based from the uploaded image.

            <div class="alert alert-light my-2">
              <strong>NOTE:</strong>
              <ul class="m-0">
                <li>
                  Contents are moderated using 
                  <a href="https://aws.amazon.com/rekognition/content-moderation/" target="_blank">Amazon Rekognition Content Moderation</a>.
                </li>
                <li>
                  To ensure that the generated articles are accurate as much as possible, 
                  the minimum <b>confidence score</b> for label detection is set to <b>90%</b>.
                </li>
              </ul>
            </div>

            <p class="mt-4">
              Click <a href="#facts-section">here</a> to read more.
            </p>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="container border p-4 rounded shadow-sm bg-light" id="form-container">
          <form method="POST" enctype="multipart/form-data" id="form">
              <div class="mb-3">
                  <label for="image" class="form-label">Select Image</label>
                  <input class="form-control" type="file" id="file" name="file" accept="image/*" required>
              </div>
              <button type="submit" class="btn btn-primary" id="upload-btn">Upload</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-5">
    <div class="container my-5" id="facts-section">
      <h3 class="text-center">
        <b>How articles are generated using this site?</b>
      </h3>
      <div class="mt-4" id="faqAccordion" role="tablist" aria-multiselectable="true">
        <div class="card">
          <div class="card-header" role="tab" id="faq1">
            <h5 class="mb-0">
              <a data-toggle="collapse" href="#faq-collapse-1" aria-expanded="true" aria-controls="faq-collapse-1">
                What happens when I upload an image?
              </a>
            </h5>
          </div>
          <div id="faq-collapse-1" class="collapse show" role="tabpanel" aria-labelledby="faq1" data-parent="#faqAccordion">
            <div class="card-body">
              When an image is uploaded, it first goes through Amazon Rekognition's content moderation feature to check if the image is safe. If it's safe, the image is stored in Amazon S3.
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" role="tab" id="faq2">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#faq-collapse-2" aria-expanded="false" aria-controls="faq-collapse-2">
                How is content safety checked?
              </a>
            </h5>
          </div>
          <div id="faq-collapse-2" class="collapse" role="tabpanel" aria-labelledby="faq2" data-parent="#faqAccordion">
            <div class="card-body">
              Amazon Rekognition's content moderation feature is used to analyze the image for unsafe content such as nudity, violence, or offensive material. Only content-safe images are processed further.
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" role="tab" id="faq3">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#faq-collapse-3" aria-expanded="false" aria-controls="faq-collapse-3">
                What happens after the image is stored in S3?
              </a>
            </h5>
          </div>
          <div id="faq-collapse-3" class="collapse" role="tabpanel" aria-labelledby="faq3" data-parent="#faqAccordion">
            <div class="card-body">
              Once the image is stored in Amazon S3, it's sent back to Amazon Rekognition for label detection. 
              The labels describe the content of the image (like "dog", "car", "beach", etc.).
              The minimum <b>confidence score</b> is set to 90% for better accuracy.
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" role="tab" id="faq4">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#faq-collapse-4" aria-expanded="false" aria-controls="faq-collapse-4">
                What is confidence score?
              </a>
            </h5>
          </div>
          <div id="faq-collapse-4" class="collapse" role="tabpanel" aria-labelledby="faq3" data-parent="#faqAccordion">
            <div class="card-body">
              When AWS Rekognition analyzes an image and detects labels (like "person", "dog", or "car"), 
              it doesn't just say what it sees — it also tells you how sure it is about each label. 
              This "how sure" is called the confidence score.
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" role="tab" id="faq5">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#faq-collapse-5" aria-expanded="false" aria-controls="faq-collapse-5">
                How are the labels used?
              </a>
            </h5>
          </div>
          <div id="faq-collapse-5" class="collapse" role="tabpanel" aria-labelledby="faq5" data-parent="#faqAccordion">
            <div class="card-body">
              The detected labels are used to create a prompt that is sent to OpenAI. This prompt helps generate a relevant article based on the content of the uploaded image.
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" role="tab" id="faq6">
            <h5 class="mb-0">
              <a class="collapsed" data-toggle="collapse" href="#faq-collapse-6" aria-expanded="false" aria-controls="faq-collapse-6">
                What technologies are involved in this process?
              </a>
            </h5>
          </div>
          <div id="faq-collapse-6" class="collapse" role="tabpanel" aria-labelledby="faq6" data-parent="#faqAccordion">
            <div class="card-body">
              This process uses Amazon Rekognition for content moderation and label detection, Amazon S3 for image storage, AWS Lambda for orchestrating the workflow, and OpenAI to generate text-based articles from the image data.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include('footer.php') ?>