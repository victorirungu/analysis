<?php
session_start();
if(!isset($_SESSION['user']['accessToken']) && !isset($_SESSION['user']['role_id'])){
     header("Location: user/login");
}

    $access_token = $_SESSION['user']['accessToken'];
    $email = $_SESSION['user']['email'];
    // $id120 = $_SESSION['user']['id'];
   date_default_timezone_set('Africa/Nairobi');

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="https://helahub.co/assets/imgs/about/1.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://helahub.co/assets/imgs/about/1.png" type="image/x-icon">
    <title>Analysis -
        <?php echo $title ?>
    </title>

    <!-- Google font-->
    <link
        href="../../../fonts.googleapis.com/css25af9.css?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
        rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.notification/1.0.2/jquery.notification.min.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <!-- Linear Icon css -->
    <link rel="stylesheet" href="assets/css/linearicon.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- fontawesome css -->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/font-awesome.css">

    <!-- Themify icon css-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/themify.css">

    <!-- ratio css -->
    <link rel="stylesheet" type="text/css" href="assets/css/ratio.css">

    <!-- remixicon css -->
    <link rel="stylesheet" type="text/css" href="assets/css/remixicon.css">

    <!-- Feather icon css-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/feather-icon.css">

    <!-- Plugins css -->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/scrollbar.css">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/animate.css">

    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/bootstrap.css">

    <!-- vector map css -->
    <link rel="stylesheet" type="text/css" href="assets/css/vector-map.css">

    <!-- Slick Slider Css -->
    <link rel="stylesheet" href="assets/css/vendors/slick.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- App css -->

    <!-- <link rel="preload" href="path/to/critical.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="path/to/critical.css">
    </noscript> -->
</head>

<body>

    <!-- tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end -->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <div class="page-header">
            <div class="header-wrapper m-0">
                <div class="header-logo-wrapper p-0">
                    <div class="logo-wrapper">
                        <a>
                            <!--<img class="img-fluid main-logo" src="assets/images/logo/1.png" alt="logo">-->
                            <!--<img class="img-fluid white-logo" src="assets/images/logo/1-white.png" alt="logo">-->
                        </a>
                    </div>
                    <div class="toggle-sidebar">
                        <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
                        <a >
                            <!--<img src="assets/images/logo/1.png" class="img-fluid" alt="">-->
                        </a>
                    </div>
                </div>


                <div class="nav-right col-6 pull-right right-header p-0">
                    <ul class="nav-menus">
                      

                        <li>
                            <div class="mode">
                                <i class="ri-moon-line"></i>
                            </div>
                        </li>
                        <li class="profile-nav onhover-dropdown pe-0 me-0">
                            <div class="media profile-media">
                                <img class="user-profile rounded-circle" src="assets/images/users/z1.jpg" alt="">
                                <div class="user-name-hide media-body">

                                    <p class="mb-0 font-roboto">Admin<i class="middle ri-arrow-down-s-line"></i></p>
                                </div>
                            </div>
                            <ul class="profile-dropdown onhover-show-div">

                                <!--<li>-->
                                <!--    <a-->
                                <!--        href="user/password">-->
                                <!--        <i data-feather="lock"></i>-->
                                <!--        <span>Change password</span>-->
                                <!--    </a>-->
                                <!--</li>-->
                                <li>
                                    <a data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                                        href="javascript:void(0)">
                                        <i data-feather="log-out"></i>
                                        <span>Log out</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
