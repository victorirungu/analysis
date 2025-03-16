<?php 
session_start();
$title = "Users";
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
                <!-- All User Table Start -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>All Users</h5>
                                        <form class="d-inline-flex">
                                            <a href="add-user" class="align-items-center btn btn-theme d-flex">
                                                <i data-feather="plus"></i>Add New
                                            </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive table-product">
                                        <table class="table all-package theme-table" id="table_id">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Role</th>
                                                    <th>Email</th>
                                                    <th>Option</th>
                                                </tr>
                                            </thead>

                               <tbody>
    <?php
    include 'database/dbconnect.php';

    // Fetch admin data with roles in a single query
    $sql = "SELECT admin.id, admin.name, admin.email, admin.role, roles.name AS role_name
            FROM admin
            LEFT JOIN roles ON admin.role = roles.id
            ORDER BY admin.id DESC";
    $result = $conn->query($sql);

    // Check for results
    if ($result && $result->num_rows > 0) {
        // Build table rows
        while ($row = $result->fetch_assoc()) {
            // Safely handle data, providing default values where necessary
            $id = htmlspecialchars($row['id']);
            $name = htmlspecialchars($row['name']);
            $email = htmlspecialchars($row['email']);
            $roleName = htmlspecialchars($row['role_name'] ?? 'No Role Assigned');
            $roleId = htmlspecialchars($row['role'] ?? 0); // Default role ID to 0 if NULL
            ?>
            <tr>
                <td><?php echo $name; ?></td>
                <td><?php echo $roleName; ?></td>
                <td><?php echo $email; ?></td>
                <td>
                    <ul>
                        <li>
                         
                            <?php if($id == $_SESSION['user']['id']){} else { ?>
                            <a onclick="editUser1('<?php echo $id; ?>', '<?php echo $name; ?>', '<?php echo $email; ?>', '<?php echo $roleId; ?>');"
                               href="#" data-bs-toggle="modal" data-bs-target="#editModalToggle">
                                <i class="ri-pencil-line"></i>
                            </a>
                            <?php } ?>
                            
                            
                            
                        </li>
                        <li>
                            <?php if($id == $_SESSION['user']['id']){} else { ?>
                            <a onclick="deleteUser1('<?php echo $id; ?>', '<?php echo $name; ?>');"
                               href="#" data-bs-toggle="modal" data-bs-target="#exampleModalToggle">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                            <?php } ?>
                        </li>
                    </ul>
                </td>
            </tr>
            <?php
        }
    } else {
        // No admins found
        echo '<tr><td colspan="4">No admins found.</td></tr>';
    }

    ?>
</tbody>


                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- All User Table Ends-->

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
   <div class="modal fade theme-modal remove-coupon" id="exampleModalToggle" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100" id="exampleModalLabel22">Are You Sure ?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="remove-box">
                    <p>Confirm you want to remove Admin:
                    <p id="UserName">
                    </p>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">No</button>

                <form id="deleteUser" onsubmit="deleteUser(event)">
                    <input type="hidden" id="id_value1" name="id" />
                    <button  id="deleteButton" type="submit" class="btn btn-animation btn-md fw-bold" data-bs-dismiss="modal">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>
com

<div class="modal fade theme-modal remove-coupon" id="editModalToggle" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-block text-center">
                <h5 class="modal-title w-100" id="exampleModalLabel22">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="editStyle">
                <form id="editAdmin" onsubmit="editAdmin(event)" enctype="multipart/form-data"
                    class="theme-form theme-form-2 mega-form">
                    <div class="mb-4 row align-items-center">
                        <label class="form-label-title col-sm-3 mb-0">User Name</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="userEditName" name="editUserName"
                                placeholder="User Name" required>
                            <input class="form-control" type="hidden" id="editUserId" name="editUserId" required>
                            
                        </div>
                    </div>

                    <div class="mb-4 row align-items-center">
                        <label class="form-label-title col-sm-3 mb-0">Email</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="userEditEmail" name="editUserEmail"
                                placeholder="Email" required readonly>
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-4 row align-items-center">
                        <label class="col-sm-3 col-form-label form-label-title">Role</label>
                        <div class="col-sm-9">
                            <select class="js-example-basic-single w-100" id="userEditRole" name="editUserRole" required>
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

                    <div class="col-12">
                        <button id="toDisable" class="btn btn-animation w-100 justify-content-center" data-bs-dismiss="modal"
                            type="submit">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to pre-fill modal data with current values
    function editUser1(userId, userName, userEmail, userRoleId) {
        document.getElementById('editUserId').value = userId;
        document.getElementById('userEditName').value = userName;
        document.getElementById('userEditEmail').value = userEmail;

        // Set the current role as selected in the dropdown
        const roleDropdown = document.getElementById('userEditRole');
        for (const option of roleDropdown.options) {
            option.selected = option.value === userRoleId;
        }
    }
</script>

    <div class="modal fade theme-modal remove-coupon" id="exampleModalToggle2" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel12">Done!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="remove-box text-center">
                        <div class="wrapper">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                            </svg>
                        </div>
                        <h4 class="text-content">It's Removed.</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal Box End -->
<script>
    function deleteUser1(id, name) {
    document.getElementById('UserName').innerHTML = name;
    document.getElementById('id_value1').value = id;

}

// function editUser1(id, name, phone, email) {
//     document.getElementById("editUserId").value = id;
//     document.getElementById("userEditName").value = name;
//     document.getElementById('editUserPhone').value = phone;
//     document.getElementById('userEditEmail').value = email;

// }
</script>



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