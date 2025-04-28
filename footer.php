</div>

<!-- Modal -->
<div class="modal fade" id="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div id="loader-container" class="p-5">
        <center>
        <img src="assets/images/spinner.svg" alt="Spinner" />
        </center>
        <div id="loader-prompt"></div>
      </div>

      <div id="articles-container">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">News Article</h5>
          <button type="button" class="btn btn-close close-modal" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div id="articles-modal-container"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>



<script src="assets/js/jquery.slim.min.js"></script>
<script src="assets/js/jquery-3.4.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>
