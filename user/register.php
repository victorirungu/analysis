<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/logo/title.jpeg" type="image/x-icon">
    
    <link rel="shortcut icon" href="../assets/images/logo/title.jpeg" type="image/x-icon">
    <title>Maisha Top - Sign Up</title>

<link rel="stylesheet" href="../assets/css/countrySelect.css">
<link rel="stylesheet" href="../assets/css/demo.css">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

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

    <!-- Country Picker JS Plugin -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>

    <!-- sign up section start -->
    <section class="log-in-section section-b-space">
        <div class="container w-100">
            <div class="row">
                <div style="margin: 0 auto;" class="col-xl-5 col-lg-6 me-auto">
                    <div class="log-in-box">
                        <div class="log-in-title">
                            <h3>Welcome to Maisha Top</h3>
                            <h4>Create Your Vendor Account</h4>
                        </div>

                        <div class="input-box">
                            <form id="signup" onsubmit="userSignUp(event)" class="row g-4">

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" required />
                                        <label for="companyName">Company Name</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required />
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" required />
                                        <label for="phoneNumber">Phone Number</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
				<input style="width: 375px; height: 55px " id="country_selector"  class="form-control" type="text">
				<label for="country_selector" style="display:none;">Select a country here...</label>

			<div class="form-item" style="display:none;">
				<input type="text" id="country_selector_code" name="country_selector_code" data-countrycodeinput="1" readonly="readonly" placeholder="Selected country code will appear here" />
				<label for="country_selector_code">...and the selected country code will be updated here</label>
			</div>
                                       
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="manufacturer">Manufacturer</option>
                                            <option value="supplier">Supplier</option>
                                        </select>
                                        <label for="type">Type</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="file" class="form-control" id="registrationCertificate" name="registrationCertificate" accept="application/pdf" required />
                                        <label for="registrationCertificate">Registration Certificate (PDF)</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
                                        <label for="password">Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating theme-form-floating log-in-form">
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required />
                                        <label for="confirmPassword">Confirm Password</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button id="toDisable" class="btn btn-animation w-100 justify-content-center" type="submit">Sign Up</button>
                                </div>

                                <div class="col-12">
                                    <div class="sign-up-box">
                                        <a href="login" class="forgot-password">Already have an account? Log In</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- sign up section end -->

</body>

<script src="../assets/js/bootstrap/bootstrap.bundle.min.js"></script>
<script src="../assets/js/scrollbar/simplebar.js"></script>
<script src="../assets/js/scrollbar/custom.js"></script>
<script src="../assets/js/notify/bootstrap-notify.min.js"></script>
<script src="../assets/js/notify/index.js"></script>
<script src="../operations/operations.js"></script>
<script src="../assets/js/script.js"></script>
<script src="../assets/js/jquery.dataTables.js"></script>
<script src="../assets/js/custom-data-table.js"></script>

	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
		<script src="../assets/js/countrySelect.js"></script>
		<script>
			$("#country_selector").countrySelect({
				defaultCountry: "ke",
				// onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
				// responsiveDropdown: true,
				preferredCountries: ['ke', 'gb', 'us']
			});
		</script>


</html>
