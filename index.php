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
</section>


<div class="container my-5 p-0">
  <div class="row justify-content-center" id="generated-articles">
  </div>
</div>

<?php include('footer.php') ?>