<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" href="https://helahub.co/assets/imgs/brands/01.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://helahub.co/assets/imgs/brands/01.png" type="image/x-icon">
    <title>Analysis - log In</title>

    <!-- Google font-->
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
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

    <!-- login section start -->
   
    <section class="log-in-section section-b-space">
          
        <div class="container w-100">
             <!--<div  style="position:absolute;margin-top:0;" ><a  href="" class="logo-login"><img src="../assets/images/logo/1.png" class="img-fluid"></a></div>-->
            <div class="row">

                <div style="margin: 0 auto;" class="col-xl-5 col-lg-6 me-auto">
                    
                    <div class="log-in-box">
                        <div class="log-in-title">
                            <h3>Welcome Back To Analysis</h3>
                            <h4>Log In To Your Admin Account</h4>
                        </div>

                        <div class="input-box">
                            <form id="login" onsubmit="userLogin(event)" class="row g-4">


                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Email Address" required />
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Password" required />
                                        <label for="password">Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="forgot-box">

                                        <a href="forgot" class="forgot-password">Forgot Password?</a>
                                    </div>
                                   
                                </div>
                                    
                                <div class="col-12">
                                    <button id="toDisable" class="btn btn-animation w-100 justify-content-center" type="submit">Log
                                        In</button>
                                </div>
                            </form>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- login section end -->

</body>


       <script src="../assets/js/bootstrap/bootstrap.bundle.min.js"></script>

       <!-- scrollbar simplebar js -->
       <script src="../assets/js/scrollbar/simplebar.js"></script>
       <script src="../assets/js/scrollbar/custom.js"></script>

    
       <script src="../assets/js/notify/bootstrap-notify.min.js"></script>
       <script src="../assets/js/notify/index.js"></script>
       <script src="../operations/operations.js"></script>




       <!-- Theme js -->
       <script src="../assets/js/script.js"></script>

   <script src="../assets/js/jquery.dataTables.js"></script>
       <script src="../assets/js/custom-data-table.js"></script>
   
</html>