<?php
$title = "Add Role";
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
        <!-- New Role start -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-sm-8 m-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>Add New Role</h5>
                                    </div>
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-home" type="button">Role</button>
                                        </li>

                                    </ul>

                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel">
                                            <form id="newRole" onsubmit="newRole(event)"
                                                class="theme-form theme-form-2 mega-form">
                                                <div class="card-header-1">
                                                    <h5>Role Information</h5>
                                                </div>

                                                <div class="row">
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Role Name</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input class="form-control" type="text" name="role_name"
                                                                required>
                                                        </div>
                                                    </div>

                                                    <div class="card-header-1">
                                                        <h5>Accessibility</h5>
                                                    </div>

                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Super Admin</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input type="checkbox" id="super_admin" name="super_admin" value="1"> Has access to all features
                                                        </div>
                                                    </div>

                                                    <!-- Accessibility checkboxes -->
                                                    <!-- Administrative Users -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Administrative Users</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input type="checkbox" name="access[]" value="all_roles"> All Roles<br>
                                                            <input type="checkbox" name="access[]" value="add_new_role"> Add new role<br>
                                                            <input type="checkbox" name="access[]" value="all_users"> All Users<br>
                                                            <input type="checkbox" name="access[]" value="add_new_user"> Add new user<br>
                                                        </div>
                                                    </div>

                                                    <!-- Inputs -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Inputs</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input type="checkbox" name="access[]" value="all_inputs"> All Inputs<br>
                                                            <input type="checkbox" name="access[]" value="add_expenses"> Add Expenses<br>
                                                            <input type="checkbox" name="access[]" value="add_revenue"> Add Revenue<br>
                                                        </div>
                                                    </div>
                                                    
                                                    

                                                    <!-- Analysis -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Analysis</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input type="checkbox" name="access[]" value="all_analysis"> All Analysis<br>
                                                            <input type="checkbox" name="access[]" value="expenses"> Expenses Analysis<br>
                                                            <input type="checkbox" name="access[]" value="revenue"> Revenue Analysis<br>
                                                            <input type="checkbox" name="access[]" value="profit-loss"> Profit-Loss Analysis<br>
                                                        </div>
                                                    </div>

                                               

                                                    <!-- Reports -->
                                                    <div class="mb-4 row align-items-center">
                                                        <label
                                                            class="col-lg-2 col-md-3 col-form-label form-label-title">Reports</label>
                                                        <div class="col-md-9 col-lg-10">
                                                            <input type="checkbox" name="access[]" value="reports"> Reports<br>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <button id="toDisable" class="btn btn-animation w-100 justify-content-center"
                                                            type="submit">Create Role</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <script>
                                                document.getElementById('super_admin').addEventListener('change', function () {
                                                    var checkboxes = document.querySelectorAll('input[name="access[]"]');
                                                    for (var checkbox of checkboxes) {
                                                        checkbox.checked = this.checked;
                                                        checkbox.disabled = this.checked;
                                                    }
                                                });
                                            </script>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- New Role End -->

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


<!--Dropzon js -->
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

       <!-- Apexchar js -->
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