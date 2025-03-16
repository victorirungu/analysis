<?php
session_start();
if(!isset($_SESSION['user']['accessToken'])){
     header("Location: login");
}
$id = $_SESSION['user']['id'] ;
$token = $_SESSION['user']['accessToken'];
$email = $_SESSION['user']['email'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/logo/title.jpeg" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/logo/title.jpeg" type="image/x-icon">
    <title>Change Password</title>

    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <!-- fontawesome css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/font-awesome.css">

    <!-- Themify icon css-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/themify.css">

    <!-- ratio css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/ratio.css">

    <!-- remixicon css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/remixicon.css">

    <!-- Feather icon css-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/feather-icon.css">

    <!-- Plugins css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/scrollbar.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/animate.css">

     <!-- Bootstrap css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/bootstrap.css">

    <!-- vector map css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/vector-map.css">

    <!-- Slick Slider Css -->
    <link rel="stylesheet" href="../assets/css/vendors/slick.css">

    <!-- App css -->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
     <link rel="stylesheet" href="../assets/css/linearicon.css">
</head>

<body>

    <!-- reset section start -->
    <section class="log-in-section section-b-space">
        <!--<a href="" class="logo-login"><img src="../assets/images/logo/title.jpeg" class="img-fluid"></a>-->
        <div class="container w-100">
            <div class="row">

                <div style="margin: 0 auto;" class="col-xl-5 col-lg-6 me-auto">
                    <div class="log-in-box">
                        <a
                                        href="../index">
                                        <i data-feather="arrow-left-circle"></i>
                                        
                                    </a>
                                    <br>
                        <div class="log-in-title center">
                            <h3>Change Password</h3>
                        </div>

                        <div class="input-box">
                            <form id="changePassword" onsubmit="changePassword(event)" class="row g-4">
                              

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input name="email" type="email" class="form-control" id="email" value="<?php echo $email; ?>"  required readonly/>
                                        <label for="email">Email</label>
                                         <input name="access_token" type="hidden" class="form-control" id="token" value="<?php echo $token; ?>"  required/>
                                         <input name="id" type="hidden" class="form-control" id="id" value="<?php echo $id; ?>"  required/>
                                    </div>
                                </div>
                              
                               <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input name="oldpassword" type="password" class="form-control" id="opassword" placeholder="Password" required/>
                                        <label for="password">Old Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input name="password" type="password" class="form-control" id="password" placeholder="Password" required/>
                                        <label for="password">New Password</label>
                                    </div>
                                </div>
                            
                                 <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input name="cnfpassword" type="password" class="form-control" id="cnfpassword" placeholder="Confirm Password" required/>
                                        <label for="cnfpassword">Confirm New Password</label>
                                    </div>
                                </div>
                                <script>
                                const passwordInput = document.getElementById("password");
                                const confirmpasswordInput = document.getElementById("cnfpassword");
                                confirmpasswordInput.addEventListener("input", function () {
                                if (confirmpasswordInput.value !== passwordInput.value) {
                                confirmpasswordInput.setCustomValidity("Passwords do not match.");
                                } else {
                                confirmpasswordInput.setCustomValidity("");
                                }
                                });
                                </script>
                                <div class="col-12">
                                    <button  id="toDisable" class="btn btn-animation w-100 justify-content-center" type="submit">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- reset section end -->

</body>
 <script src="../assets/js/icons/feather-icon/feather.min.js"></script>
       <script src="../assets/js/icons/feather-icon/feather-icon.js"></script>

       <script src="../assets/js/bootstrap/bootstrap.bundle.min.js"></script>

       <!-- scrollbar simplebar js -->
       <script src="../assets/js/scrollbar/simplebar.js"></script>
       <script src="../assets/js/scrollbar/custom.js"></script>

    
       <script src="../assets/js/notify/bootstrap-notify.min.js"></script>
       <script src="../operations/operations.js"></script>



       <!-- Theme js -->
       <script src="../assets/js/script.js"></script>

   <script src="../assets/js/jquery.dataTables.js"></script>
       <script src="../assets/js/custom-data-table.js"></script>
   
</html>