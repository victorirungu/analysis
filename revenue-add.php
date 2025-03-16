<?php
$title = "Add Revenue";
include 'resources/includes/head.php'; ?>
    <div class="page-loader">
    
    <img src="https://i.pinimg.com/originals/78/e8/26/78e826ca1b9351214dfdd5e47f7e2024.gif">
</div>
<div class="page-body-wrapper">
    <!-- Page Sidebar Start-->
    <?php include 'resources/includes/sidebar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
           <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/0rc94fln5wq8pyxhn089p770jghm8uf35dxx01dwdfij8gen/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    tinymce.init({
        selector: 'textarea'
    });
    </script>
    <!-- Page Sidebar Ends-->
    <!-- Page Sidebar Start -->

    <div class="page-body">
        <!-- New User start -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-sm-8 m-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>Add Revenue</h5>
                                    </div>
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-home" type="button">Excel File Allowed</button>
                                        </li>

                                    </ul>

                                 <div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel">
       <form id="excelUploadForm" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="excelFile" class="form-label">Upload Excel File</label>
        <input class="form-control" type="file" id="excelFile" name="excelFile" accept=".xls,.xlsx" required>
        <div class="form-text">Allowed formats: .xls, .xlsx</div>
    </div>
    <button type="submit" class="btn btn-primary" id="submitBtn">
        <span class="button-text">Process File</span>
        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
</form>
<div id="result" class="mt-3"></div>
    </div>
</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- New User End -->

        <!-- footer start -->
        <?php include 'resources/includes/footer.php'; ?>
        <!-- footer end -->
    </div>
    <!-- Page Sidebar End -->
</div>
</div>
<!-- page-wrapper End -->

<!-- Modal Start -->
<?php include 'resources/includes/logout.php'; ?>
<!-- Modal End -->
<script>
document.getElementById('excelUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('submitBtn');
    const buttonText = submitBtn.querySelector('.button-text');
    const spinner = submitBtn.querySelector('.spinner-border');
    
    // Disable button and show spinner
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = ' Processing...';

    const formData = new FormData();
    const fileInput = document.getElementById('excelFile');
    
    if (!fileInput.files[0]) {
        showAlert('Please select a file first!', 'danger');
        // Reset button state
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
        buttonText.textContent = 'Process File';
        return;
    }

    formData.append('excelFile', fileInput.files[0]);
    
    fetch('process_excel_revenue.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Successfully processed ${data.totalRecords} records!`, 'success');
            // Optional: Add download link
            const downloadLink = `<a href="${data.filePath}" download class="btn btn-success mt-2">Download JSON</a>`;
            document.getElementById('result').innerHTML = downloadLink;
        } else {
            showAlert(`Error: ${data.error}`, 'danger');
        }
    })
    .catch(error => {
        showAlert('An error occurred while processing the file', 'danger');
        console.error('Error:', error);
    })
    .finally(() => {
        // Re-enable button and hide spinner
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
        buttonText.textContent = 'Process File';
    });
});

function showAlert(message, type) {
    const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    document.getElementById('result').innerHTML = alert;
}
</script>
<!--Dropzone js -->
<script src="assets/js/dropzone/dropzone.js"></script>
<script src="assets/js/dropzone/dropzone-script.js"></script>

<script src="assets/js/bootstrap/bootstrap.bundle.min.js"></script>

<!-- feather icon js -->
<script src="assets/js/icons/feather-icon/feather.min.js"></script>
<script src="assets/js/icons/feather-icon/feather-icon.js"></script>

<!-- scrollbar simplebar js -->
<script src="assets/js/scrollbar/simplebar.js"></script>
<script src="assets/js/scrollbar/custom.js"></script>

<!-- Sidebar jquery -->
<script src="assets/js/config.js"></script>

<!-- tooltip init js -->
<script src="assets/js/tooltip-init.js"></script>

<!-- Plugins JS -->
<script src="assets/js/sidebar-menu.js"></script>
<script src="assets/js/bundle.min.js"></script>
<script src="assets/js/notify/bootstrap-notify.min.js"></script>
<script src="operations/operations.js"></script>

<!-- Apexchart js -->
<script src="assets/js/chart/apex-chart/apex-chart1.js"></script>
<script src="assets/js/chart/apex-chart/moment.min.js"></script>
<script src="assets/js/chart/apex-chart/apex-chart.js"></script>
<script src="assets/js/chart/apex-chart/stock-prices.js"></script>
<script src="assets/js/chart/apex-chart/chart-custom1.js"></script>

<!-- slick slider js -->
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/custom-slick.js"></script>

<!-- customizer js -->
<script src="assets/js/customizer.js"></script>

<!-- ratio js -->
<script src="assets/js/ratio.js"></script>

<!-- sidebar effect -->
<script src="assets/js/sidebareffect.js"></script>

<!-- Theme js -->
<script src="assets/js/script.js"></script>

<script src="assets/js/jquery.dataTables.js"></script>
<script src="assets/js/custom-data-table.js"></script>


</body>

</html>