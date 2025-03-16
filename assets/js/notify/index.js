// myToast("Loading Data");
// function myToast(message){
//     var notify = $.notify('<i class="fas fa-bell"></i></i><strong>' + message +'</strong>', {
//     type: 'theme',
//     allow_dismiss: true,
//     delay: 3000,
//     showProgressbar: true,
//     timer: 300,
//     animate: {
//         enter: 'animated fadeInDown',
//         exit: 'animated fadeOutUp'
//     }
//  });
// // }
// // //user
// // function userLogin(event){
// //   event.preventDefault();
// //   const form = document.querySelector('#login');
// //   const loginData = new FormData(form);
// //   const inputs = form.querySelectorAll('input[required]');
// //   let formIsValid = true;

// //     for (const input of inputs) {
// //       if (!input.value.trim()) {
// //         input.reportValidity(); 
// //         formIsValid = false;
// //         break;
// //       }
// //     }
// //     if (!formIsValid) {
// //       return; 
// //     }
// //     myToast("Verifying Data...");
// //     $.ajax({
// //     type: 'POST',
// //     url: 'https://api.pharmchem.co.ke/api/login',
// //     data: loginData,
// //     success: function (response) {
// //         alert(response);
// //       var res = JSON.parse(response);
// //       var message = '';
// //       if (res.success) {
// //         clearFormInputs('#login');
// //         message = "Successfully logged in, redirecting...";
// //         setTimeout( window.location = 'admin.pharmchem.co.ke', 2000);
// //       } else {
// //         message = res.message;
// //       }
// //     myToast(message);
// //     },
// //     error: function (xhr, status, error) {
// //       myToast("Error occurred.Try again Later");
// //     }
// //   });
// // }


// // function newUser(event){
// //   event.preventDefault();
// //   const form2 = document.querySelector('#new-user');
// //   const userData = new FormData(form2);
// //   const inputs2 = form2.querySelectorAll('input[required]');
// //   let formIsValid = true;
// //     for (const input of inputs2) {
// //       if (!input.value.trim()) {
// //         input.reportValidity(); 
// //         formIsValid = false;
// //         break;
// //       }
// //     }
// //     if (!formIsValid) {
// //       return; 
// //     }
// //     myToast("Creating User...");
// //     $.ajax({
// //     type: 'POST',
// //     url: 'assets/appointment.php',
// //     data: resetData,
// //     success: function (response) {
// //       var res = JSON.parse(response);
// //       var message = '';
// //       if (res.success) {
// //         clearFormInputs('#reset');
// //         message = "Successfully created user, redirecting...";
// //         setTimeout( window.location = 'admin.pharmchem.co.ke/users', 2000);
// //       } else {
// //         message = res.message;
// //       }
// //     myToast(message);
// //     },
// //     error: function (xhr, status, error) {
// //       myToast("Error occurred.Try again Later");
// //     }
// //   });
// // }


// // function resetPassword(event){
// //   event.preventDefault();
// //   const form1 = document.querySelector('#reset');
// //   const resetData = new FormData(form1);
// //   const inputs1 = form1.querySelectorAll('input[required]');
// //   let formIsValid = true;
// //     for (const input of inputs1) {
// //       if (!input.value.trim()) {
// //         input.reportValidity(); 
// //         formIsValid = false;
// //         break;
// //       }
// //     }
// //     if (!formIsValid) {
// //       return; 
// //     }
// //     myToast("Verifying Data...");
// //     $.ajax({
// //     type: 'POST',
// //     url: 'assets/appointment.php',
// //     data: resetData,
// //     success: function (response) {
// //       var res = JSON.parse(response);
// //       var message = '';
// //       if (res.success) {
// //         clearFormInputs('#reset');
// //         message = "Successfully updated password, redirecting...";
// //         setTimeout( window.location = 'admin.pharmchem.co.ke', 2000);
// //       } else {
// //         message = res.message;
// //       }
// //     myToast(message);
// //     },
// //     error: function (xhr, status, error) {
// //         alert(error);
// //       myToast("Error occurred.Try again Later");
// //     }
// //   });
// // }








// // //categories

// // function newCategory(event){
 
// //   event.preventDefault();
// //   const form3 = document.querySelector('#newCategory');
// //   const categoryData = new FormData(form3);
// //   const inputs3 = form3.querySelectorAll('input[required]');
// //   let formIsValid = true;
// //     for (const input of inputs3) {
// //       if (!input.value.trim()) {
// //         input.reportValidity(); 
// //         formIsValid = false;
// //         break;
// //       }
// //     }
// //     if (!formIsValid) {
// //       return; 
// //     }
    
// //     myToast("Adding New Category...");
// //     alert("processing200");
// //     if (typeof jQuery !== 'undefined') {
// //   // jQuery is loaded, display a message
// //   alert('jQuery is loaded and available!');
// // } else {
// //   // jQuery is not loaded, display an error message
// //   alert('jQuery is not loaded or not available!');
// // }

// //     $.ajax({
// //     type: 'POST',
// //     url: 'category.php',
// //     data: categoryData,
// //     success: function (response) {
// //         alert(response);
// //       var res = JSON.parse(response);
// //       var messages = '';
// //       if (res.success) {
// //         clearFormInputs('#newCategory');
// //         messages = "Category added successfully, updating...";
// // //       setTimeout(function() {
// // //   window.location = 'https://admin.pharmchem.co.ke/category';
// // // }, 1000);
// //       } else {
// //         messages = res.message;
// //       }
// //     myToast(messages);
// //     },
// //     error: function (xhr, status, error) {
// //         alert(error);
// //       myToast("Error occurred.Try again Later");
// //     }
// //   });
// // }




// //  function clearFormInputs(id) {
// //     const form = document.querySelector(id);
// //     form.reset();
// //   }