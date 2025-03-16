<?php
$title = "Add User";
include 'resources/includes/head.php'; ?>
<div class="page-loader">
    
    <img src="https://i.pinimg.com/originals/78/e8/26/78e826ca1b9351214dfdd5e47f7e2024.gif">
</div>
<div class="page-body-wrapper">
    <!-- Page Sidebar Start-->
    <?php include 'resources/includes/sidebar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
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
                                        <h5>Add New User</h5>
                                    </div>
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-home" type="button">Account</button>
                                        </li>

                                    </ul>

                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel">
                                            <form id="newUser" onsubmit="newUser(event)"
                                                class="theme-form theme-form-2 mega-form">
                                                <div class="card-header-1">
                                                    <h5>User Information</h5>
                                                </div>

                                                <div class="row">
                                                    <!-- Name -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Name</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input class="form-control" type="text" name="name"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <!-- Phone -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Phone</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input class="form-control" type="text" name="phone"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <!-- Email Address -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Email
                                                            Address</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input class="form-control" type="email" name="email"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <!-- Role Selection -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label class="col-lg-2 col-md-3 col-form-label form-label-title">Role</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <select class="form-control" id="userRole" name="role" required>
                                                                <option disabled selected>Select Role</option>
                                                                <?php
                                                                require_once 'database/dbconnect.php';
                                                                $sql = "SELECT * FROM roles ORDER BY id ASC";
                                                                $result = $conn->query($sql);
                                                                if ($result->num_rows > 0) {
                                                                    while ($row = $result->fetch_assoc()) {
                                                                        $roleId = $row["id"];
                                                                        $roleName = htmlspecialchars($row["name"]);
                                                                        ?>
                                                                        <option value="<?php echo $roleId; ?>" id="role_<?php echo $roleId; ?>">
                                                                            <?php echo $roleName; ?>
                                                                        </option>
                                                                        <?php
                                                                    }
                                                                }
                                                                $conn->close();
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Submit Button -->
                                                    <div class="col-12">
                                                        <button id="toDisable" class="btn btn-animation w-100 justify-content-center"
                                                            type="submit">Create User</button>
                                                    </div>
                                                </div>
                                            </form>
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

<!-- Include necessary scripts -->

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
