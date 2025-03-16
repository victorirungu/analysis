<?php
session_start();
$title = "Roles";
include 'resources/includes/head.php'; ?>
<div class="page-loader">
    
    <img src="https://i.pinimg.com/originals/78/e8/26/78e826ca1b9351214dfdd5e47f7e2024.gif">
</div>
<div class="page-body-wrapper">
    <style>
        .editStyle {
            margin: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- Page Sidebar Start-->
    <?php include 'resources/includes/sidebar.php'; ?>
    <!-- Page Sidebar Ends-->

    <!-- Container-fluid starts-->
    <div class="page-body">
        <!-- All Role Table Start -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="title-header option-title">
                                <h5>All Roles</h5>
                                <form class="d-inline-flex">
                                    <a href="add-role" class="align-items-center btn btn-theme d-flex">
                                        <i data-feather="plus"></i>Add New
                                    </a>
                                </form>
                            </div>

                            <div class="table-responsive table-product">
                                <table class="table all-package theme-table" id="table_id">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Accessibility</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php
                                        include 'database/dbconnect.php';

                                        $sql = "SELECT * FROM roles ORDER BY id DESC";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            $roles = array();
                                            while ($row = $result->fetch_assoc()) {
                                                $roles[] = $row;
                                            }
                                            foreach ($roles as $rolesInstance) {
                                                $id = $rolesInstance["id"];
                                                $name = $rolesInstance["name"];
                                                $accessibility = $rolesInstance["accessibility"];
                                                ?>

                                                <tr>
                                                    <td>
                                                        <?php echo $name ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $accessibility ?>
                                                    </td>

                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <a onclick="editRole1('<?php echo $id; ?>','<?php echo $name; ?>','<?php echo htmlspecialchars($accessibility, ENT_QUOTES); ?>');"
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#editModalToggle">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="deleteRole1('<?php echo $id; ?>','<?php echo $name; ?>');"
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#deleteModalToggle">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            <?php }
                                        }
                                        $conn->close();
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Role Table Ends-->
        <?php include 'resources/includes/footer.php'; ?>
    </div>
    <!-- Container-fluid end -->
</div>
<!-- Page Body End -->

<!-- Modal Start -->
<?php include 'resources/includes/logout.php'; ?>
<!-- Modal End -->
</div>

<!-- Delete Modal Box Start -->
<div class="modal fade theme-modal remove-coupon" id="deleteModalToggle" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100" id="deleteModalLabel">Are You Sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="remove-box">
                    <p>Confirm you want to remove Role:
                    Please note that this will not be possible if you have users assigned to this role
                    <p id="RoleName">
                    </p>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">No</button>

                <form id="deleteRole" onsubmit="deleteRole(event)">
                    <input type="hidden" id="id_value1" name="id" />
                    <button id="deleteButton" type="submit" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal Box Start -->
<div class="modal fade theme-modal remove-coupon" id="editModalToggle" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Increased size for better visibility -->
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100" id="editModalLabel">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="editStyle">
                <form id="editRole" onsubmit="editRole(event)" enctype="multipart/form-data"
                      class="theme-form theme-form-2 mega-form">
                    <div class="card-header-1">
                        <h5>Role Information</h5>
                    </div>

                    <div class="row">
                        <!-- Role Name -->
                        <div class="mb-4 row align-items-center">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Role Name</label>
                            <div class="col-md-9 col-lg-10">
                                <input class="form-control" type="text" id="RoleEditName" name="role_name" required>
                                <input class="form-control" type="hidden" id="editRoleId" name="id" required>
                            </div>
                        </div>

                        <!-- Accessibility Options -->
                        <div class="card-header-1">
                            <h5>Accessibility</h5>
                        </div>

                        <!-- Super Admin -->
                        <div class="mb-4 row align-items-center">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Super Admin</label>
                            <div class="col-md-9 col-lg-10">
                                <input type="checkbox" id="super_admin_edit" name="super_admin" value="1"> Has access to all features
                            </div>
                        </div>

                        <!-- Administrative Users -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Administrative Users</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Administrative Users</strong><br>
                                <input type="checkbox" name="access[]" value="all_roles"> All Roles<br>
                                <input type="checkbox" name="access[]" value="add_new_role"> Add new role<br>
                                <input type="checkbox" name="access[]" value="all_users"> All Users<br>
                                <input type="checkbox" name="access[]" value="add_new_user"> Add new user<br>
                            </div>
                        </div>

                        <!-- Buyers -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Buyers</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Buyers</strong><br>
                                <input type="checkbox" name="access[]" value="all_buyers"> All Buyers<br>
                                <input type="checkbox" name="access[]" value="pending_approvals_buyers"> Pending Approvals<br>
                                <input type="checkbox" name="access[]" value="buyer_analysis"> Buyer Analysis<br>
                            </div>
                        </div>

                        <!-- Vendors -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Vendors</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Vendors</strong><br>
                                <input type="checkbox" name="access[]" value="all_vendors"> All Vendors<br>
                                <input type="checkbox" name="access[]" value="pending_approvals_vendors"> Pending Approvals<br>
                                <input type="checkbox" name="access[]" value="vendor_analysis"> Vendor Analysis<br>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Categories</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Categories</strong><br>
                                <input type="checkbox" name="access[]" value="all_categories"> All Categories<br>
                                <input type="checkbox" name="access[]" value="add_new_category"> Add new Category<br>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Products</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Products</strong><br>
                                <input type="checkbox" name="access[]" value="all_products"> All Products<br>
                                <input type="checkbox" name="access[]" value="add_new_product"> Add new Product<br>
                            </div>
                        </div>

                        <!-- Orders -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Orders</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Orders</strong><br>
                                <input type="checkbox" name="access[]" value="orders"> Orders<br>
                            </div>
                        </div>

                        <!-- Finance -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Finance</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Finance</strong><br>
                                <input type="checkbox" name="access[]" value="all_payments"> All Payments<br>
                                <input type="checkbox" name="access[]" value="commissions"> Commissions<br>
                                <input type="checkbox" name="access[]" value="financial_summaries"> Financial summaries<br>
                            </div>
                        </div>

                        <!-- Inventory Management -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Inventory Management</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Inventory Management</strong><br>
                                <input type="checkbox" name="access[]" value="stocks_levels"> Stocks & Levels<br>
                                <input type="checkbox" name="access[]" value="fast_moving_products"> Fast Moving Products<br>
                            </div>
                        </div>

                        <!-- Audit Logs -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Audit Logs</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Audit Logs</strong><br>
                                <input type="checkbox" name="access[]" value="activity_logs"> Activity Logs<br>
                                <input type="checkbox" name="access[]" value="user_logs"> User Logs<br>
                                <input type="checkbox" name="access[]" value="vendor_logs"> Vendor Logs<br>
                                <input type="checkbox" name="access[]" value="buyer_logs"> Buyer Logs<br>
                                <input type="checkbox" name="access[]" value="payment_logs"> Payment Logs<br>
                            </div>
                        </div>

                        <!-- Reports -->
                        <div class="mb-4 row align-items-start">
                            <label class="col-lg-2 col-md-3 col-form-label form-label-title">Reports</label>
                            <div class="col-md-9 col-lg-10">
                                <strong>Reports</strong><br>
                                <input type="checkbox" name="access[]" value="reports"> Reports<br>
                            </div>
                        </div>

                        <div class="col-12">
                            <button id="editButton" class="btn btn-animation w-100 justify-content-center"
                                    data-bs-dismiss="modal" type="submit"> Update Role</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Functions -->
<script>
function editRole1(id, name, accessibilityJson) {
    document.getElementById("editRoleId").value = id;
    document.getElementById("RoleEditName").value = name;

    // Parse accessibility JSON
    var accessibility = JSON.parse(accessibilityJson);

    // Set Super Admin checkbox
    var superAdminCheckbox = document.getElementById('super_admin_edit');
    if (accessibility.super_admin) {
        superAdminCheckbox.checked = true;
    } else {
        superAdminCheckbox.checked = false;
    }

    // Set other accessibility checkboxes
    var checkboxes = document.querySelectorAll('#editRole input[name="access[]"]');
    for (var checkbox of checkboxes) {
        var value = checkbox.value;
        checkbox.checked = accessibility[value] ? true : false;
        checkbox.disabled = superAdminCheckbox.checked; // Disable if super admin
    }
}

// Handle Super Admin checkbox in edit form
document.getElementById('super_admin_edit').addEventListener('change', function () {
    var checkboxes = document.querySelectorAll('#editRole input[name="access[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
        checkbox.disabled = this.checked;
    }
});
</script>

<!-- JavaScript Functions -->
<script>
function deleteRole1(id, name) {
    document.getElementById('RoleName').innerHTML = name;
    document.getElementById('id_value1').value = id;
}



</script>

<!-- Include your existing scripts -->
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
