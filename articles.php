<?php include('header.php'); ?>

<section class="blog_section layout_padding">
  <div class="row p-0 m-0 w-100">
    <div class="container">
      <div class="heading_container">
        <h2>
          Generated Articles
        </h2>
      </div>
    </div>
  </div>
</section>

<div class="row">
  <div class="container">
    <div class="alert alert-info">
      <p class="m-0">
        <h5><b>Your articles are <b>private</b> and saved only on your device.</b></h5>
        <p class="m-0 alert alert-light text-justify">
          Except for images stored on Amazon S3, 
          the articles you see here — including pictures and texts — 
          are saved in your browser and cannot be seen by anyone else. 
          They will <b>automatically disappear one hour after they are uploaded</b>.
        </p>
      </p>
    </div>
  </div>
</div>

<div class="container my-5 p-0">
  <div class="row">
    <div class="container text-right">
      <a href="javascript:void(0)"
        class="clear-generated-articles d-none">
          Clear History
      </a>
    </div>
  </div>
  <div class="row" id="generated-articles">
    
  </div>
</div>

<?php include('footer.php') ?>