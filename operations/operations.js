// myToast("Loading Data", 2000, true);
function myToast(message, timer, dismiss) {
  var notify = $.notify(
    '<i class="fas fa-bell"></i></i><strong>' + message + "</strong>",
    {
      type: "theme",
      allow_dismiss: dismiss,
      delay: timer,
      showProgressbar: true,
      timer: 150,
      animate: {
        enter: "animated fadeInDown",
        exit: "animated fadeOutUp",
      },
    }
  );
}



//AUTHENTICATION START //AUTHENTICATION START //AUTHENTICATION START 
//AUTHENTICATION START //AUTHENTICATION START //AUTHENTICATION START 
//AUTHENTICATION START //AUTHENTICATION START //AUTHENTICATION START 

//login
function userLogin(event) {
    event.preventDefault();

    var btn11 = document.querySelector("#toDisable"); // Button to disable during submission
    const form = document.querySelector("#login"); // Form element
    const formData = new FormData(form); // Create FormData object from the form

    const inputs = form.querySelectorAll("input[required], select[required]"); // Required inputs
    let formIsValid = true;

    // Validate all required inputs
    for (const input of inputs) {
        if (!input.value.trim()) {
            input.reportValidity(); // Show browser validation error
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return; // Stop if form is invalid
    }

    // Disable the submit button
    btn11.setAttribute('disabled', 'disabled');
    myToast("Logging in...");
    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/users/login.php", // PHP script to handle registration
        data: formData, // FormData object
        processData: false, // Do not process data (required for file upload)
        contentType: false, // Do not set content type header (required for FormData)
        success: function (response) {
            // The response is already a JSON object. No need to parse it.
            let messages = "";
            if (response.success) {
                clearFormInputs("#login"); // Clear form inputs if registration succeeds
                messages = "Login successful, redirecting...";
                myToast(messages);
                setTimeout(function () {
                     window.location = "../index";  // Redirect to login page
                }, 2000);
            } else {
                btn11.removeAttribute('disabled'); // Re-enable button if error occurs
                messages = response.message;
                myToast(messages);
            }
        },
        error: function (xhr, status, error) {
            btn11.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}

//create user

function newUser(event) {
    event.preventDefault();

    var btn = document.querySelector("#toDisable"); // Button to disable during submission
    const form = document.querySelector("#newUser"); // Form element
    const formData = new FormData(form); // Create FormData object from the form

    const inputs = form.querySelectorAll("input[required], select[required]"); // Required inputs
    let formIsValid = true;

    // Validate all required inputs
    for (const input of inputs) {
        if (!input.value.trim()) {
            input.reportValidity(); // Show browser validation error
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return; // Stop if form is invalid
    }

    // Disable the submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Creating user...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/users/create.php", // PHP script to handle user creation
        data: formData, // FormData object
        processData: false, // Do not process data (required for file upload)
        contentType: false, // Do not set content type header (required for FormData)
        success: function (response) {
            let messages = "";
            if (response.success) {
                clearFormInputs("#newUser"); // Clear form inputs if user creation succeeds
                messages = "User created successfully.";
                myToast(messages);
                setTimeout(function () {
                    window.location = "users";  // Redirect to users page
                }, 2000);
            } else {
                btn.removeAttribute('disabled'); // Re-enable button if error occurs
                messages = response.message;
                myToast(messages);
            }
        },
        error: function (xhr, status, error) {
            btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}




//verify OTP


function verifyEmail(event) {
    event.preventDefault();

    var btn11 = document.querySelector("#toDisable"); // Button to disable during submission
    const form = document.querySelector("#verifyEmail"); // Form element
    const formData = new FormData(form); // Create FormData object from the form

    const inputs = form.querySelectorAll("input[required], select[required]"); // Required inputs
    let formIsValid = true;

    // Validate all required inputs
    for (const input of inputs) {
        if (!input.value.trim()) {
            input.reportValidity(); // Show browser validation error
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return; // Stop if form is invalid
    }

    // Disable the submit button
    btn11.setAttribute('disabled', 'disabled');
    myToast("Verifying Email...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/users/email_verification.php", // PHP script to handle registration
        data: formData, // FormData object
        processData: false, // Do not process data (required for file upload)
        contentType: false, // Do not set content type header (required for FormData)
        success: function (response) {
            // The response is already a JSON object. No need to parse it.
            let messages = "";
            if (response.success) {
                clearFormInputs("#verifyEmail"); // Clear form inputs if registration succeeds
                messages = "Email Verification successful, redirecting...";
                myToast(messages);
                setTimeout(function () {
                    window.location = "../index"; // Redirect to home page
                }, 2000);
            } else {
                btn11.removeAttribute('disabled'); // Re-enable button if error occurs
                messages = response.message;
                myToast(messages);
            }
        },
        error: function (xhr, status, error) {
            btn11.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}

function verifyResetOtp(event) {
    event.preventDefault();

    var btn11 = document.querySelector("#toDisable"); // Button to disable during submission
    const form = document.querySelector("#verifyResetOtp"); // Form element
    const formData = new FormData(form); // Create FormData object from the form

    const inputs = form.querySelectorAll("input[required], select[required]"); // Required inputs
    let formIsValid = true;

    // Validate all required inputs
    for (const input of inputs) {
        if (!input.value.trim()) {
            input.reportValidity(); // Show browser validation error
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return; // Stop if form is invalid
    }

    // Disable the submit button
    btn11.setAttribute('disabled', 'disabled');
    myToast("Verifying Email...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/users/reset_email_verification.php", // PHP script to handle registration
        data: formData, // FormData object
        processData: false, // Do not process data (required for file upload)
        contentType: false, // Do not set content type header (required for FormData)
        success: function (response) {
            // The response is already a JSON object. No need to parse it.
            let messages = "";
            if (response.success) {
                clearFormInputs("#verifyResetOtp"); // Clear form inputs if registration succeeds
                messages = "Email Verification successful, redirecting...";
                myToast(messages);
                setTimeout(function () {
                    window.location = "reset"; // Redirect to home page
                }, 2000);
            } else {
                btn11.removeAttribute('disabled'); // Re-enable button if error occurs
                messages = response.message;
                myToast(messages);
            }
        },
        error: function (xhr, status, error) {
            btn11.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}







//reset password



function resetPassword(event) {
      var btn11 =  document.querySelector("#toDisable");
  event.preventDefault();
  const form1 = document.querySelector("#reset");
  const resetData = new FormData(form1);
  const inputs2 = form1.querySelectorAll("input[required]");
  let formIsValid = true;
  for (const input of inputs2) {
    if (!input.value.trim()) {
      input.reportValidity();
      formIsValid = false;
      break;
    }
  }
  if (!formIsValid) {
    return;
  }
btn11.setAttribute('disabled', 'disabled');
  var toast = myToast("Verifying data...");
  $.ajax({
    type: "post",
   url: "https://analysis.helahub.co/operations/users/reset.php", 
    data: resetData, // Use the FormData object directly, including file data
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content type
    success: function (response) {
      console.log(response);
      if (response.success) {
        clearFormInputs("#reset");
        messages = "Successfully updated password...";
        toast = myToast(messages, 3000, true);
        setTimeout(function () {
          window.location = "../index";
        }, 2000);
      } else if (!response.success) {
        btn11.removeAttribute('disabled');
        messages = response.message;
        toast = myToast(messages, 3000, true);
      }
    },
    error: function (xhr, status, error1) {
      btn11.removeAttribute('disabled');
      toast = myToast(xhr.responseJSON.message, 3000, true);
      
    },
  });
}

//edit admin


function editAdmin(event) {
      var btn11 =  document.querySelector("#toDisable");
  event.preventDefault();
  const form1 = document.querySelector("#editAdmin");
  const resetData = new FormData(form1);
  const inputs2 = form1.querySelectorAll("input[required]");
  let formIsValid = true;
  for (const input of inputs2) {
    if (!input.value.trim()) {
      input.reportValidity();
      formIsValid = false;
      break;
    }
  }
  if (!formIsValid) {
    return;
  }
btn11.setAttribute('disabled', 'disabled');
  var toast = myToast("Updating user data...");
  $.ajax({
    type: "post",
   url: "https://analysis.helahub.co/operations/users/update.php", 
    data: resetData, // Use the FormData object directly, including file data
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content type
    success: function (response) {
        let messages = "";
        var res = JSON.parse(response);
      if (res.success) {
        clearFormInputs("#editAdmin");
        messages = "Successfully updated user info...";
        toast = myToast(messages, 3000, true);
        setTimeout(function () {
          window.location = "users";
        }, 2000);
      } else if (!res.success) {
        btn11.removeAttribute('disabled');
        messages = response.message;
        toast = myToast(messages, 3000, true);
      }
    },
    error: function (xhr, status, error1) {
      btn11.removeAttribute('disabled');
      toast = myToast(xhr.responseJSON.message, 3000, true);
      
    },
  });
}





//AUTHENTICATION END//AUTHENTICATION END//AUTHENTICATION END//AUTHENTICATION END
//AUTHENTICATION END//AUTHENTICATION END//AUTHENTICATION END//AUTHENTICATION END
//AUTHENTICATION END//AUTHENTICATION END//AUTHENTICATION END//AUTHENTICATION END



//FINANCIER START//FINANCIER START//FINANCIER START//FINANCIER START//FINANCIER START
//FINANCIER START//FINANCIER START//FINANCIER START//FINANCIER START//FINANCIER START
//FINANCIER START//FINANCIER START//FINANCIER START//FINANCIER START//FINANCIER START

    
    
// FINANCIERS


function newFinancier(event) {
      var btn11 =  document.querySelector("#toDisable");
  event.preventDefault();
  const form3 = document.querySelector("#newFinancier");
  const createFinancierData = new FormData(form3);
  const inputs3 = form3.querySelectorAll("input[required]");
  let formIsValid = true;
  for (const input of inputs3) {
    if (!input.value.trim()) {
      input.reportValidity();
      formIsValid = false;
      break;
    }
  }
  if (!formIsValid) {
    return;
  }
  btn11.setAttribute('disabled', 'disabled');
  myToast("Adding New Financier...");
  $.ajax({
    type: "post",
    url: "https://analysis.helahub.co/operations/financiers/create.php",
    data: createFinancierData, 
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content type
    success: function (response) {
      var messages = "";
      if (response.success) {
        clearFormInputs("#newFinancier");
        messages = response.message ;
        myToast(messages);
        setTimeout(function () {
          window.location = "add-financier";
        }, 1000);
      } else {
        btn11.removeAttribute('disabled');
        messages = response.message;
        myToast(messages);
      }
    },
    error: function (xhr, status, error) {
      btn11.removeAttribute('disabled');
      myToast("Error occurred. Try again Later");
    },
  });
}


function updateFinancier(event) {
      var btn11 =  document.querySelector("#toDisable");
  event.preventDefault();
  const form3 = document.querySelector("#editFinancierForm");
  const createFinancierData = new FormData(form3);
  const inputs3 = form3.querySelectorAll("input[required]");
  let formIsValid = true;
  for (const input of inputs3) {
    if (!input.value.trim()) {
      input.reportValidity();
      formIsValid = false;
      break;
    }
  }
  if (!formIsValid) {
    return;
  }
  btn11.setAttribute('disabled', 'disabled');
  myToast("Updating Financier...");
  $.ajax({
    type: "post",
    url: "https://analysis.helahub.co/operations/financiers/update.php",
    data: createFinancierData, 
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content type
    success: function (response) {
      var messages = "";
      if (response.success) {
        clearFormInputs("#editFinancierForm");
        messages = response.message ;
        myToast(messages);
        setTimeout(function () {
          window.location = "financiers";
        }, 1000);
      } else {
        btn11.removeAttribute('disabled');
        messages = response.message;
        myToast(messages);
      }
    },
    error: function (xhr, status, error) {
      btn11.removeAttribute('disabled');
      myToast("Error occurred. Try again Later");
    },
  });
}

    
 function suspendFinancier(event) {
    event.preventDefault();

    var btn = document.querySelector("#toDisable"); // Button to disable during submission
    const form = document.querySelector("#suspendFinancier"); // Form element
    const formData = new FormData(form); // Create FormData object from the form

    const inputs = form.querySelectorAll("input[required], textarea[required], select[required]"); // Required inputs
    let formIsValid = true;

    // Validate all required inputs
    for (const input of inputs) {
        if (!input.value.trim()) {
            input.reportValidity(); // Show browser validation error
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return; // Stop if form is invalid
    }

    // Disable the submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Processing request...");

    // Send data via AJAX
    $.ajax({
        type: "POST",
        url: "https://analysis.helahub.co/operations/financiers/update_status.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            let messages = "";
            if (response.success) {
                clearFormInputs("#suspendFinancier"); // Clear form inputs on success if needed
                messages = response.message;
                myToast(messages);
                setTimeout(function () {
                    location.reload(); // Reload the page to reflect changes
                }, 2000);
            } else {
                btn.removeAttribute('disabled'); // Re-enable button if error occurs
                messages = response.message;
                myToast(messages);
            }
        },
        error: function (xhr, status, error) {
            btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}

//VENDORS END //VENDORS END //VENDORS END //VENDORS END //VENDORS END //VENDORS END 
//VENDORS END //VENDORS END //VENDORS END //VENDORS END //VENDORS END //VENDORS END









 
// PRODUCTS START// PRODUCTS START// PRODUCTS START// PRODUCTS START// PRODUCTS START
// PRODUCTS START// PRODUCTS START// PRODUCTS START// PRODUCTS START// PRODUCTS START

  function approveOrRejectProduct(event) {
        event.preventDefault();

        var btn = document.querySelector("#toDisable"); 
        const form = document.querySelector("#approveProductForm"); 
        const formData = new FormData(form); 

        const inputs = form.querySelectorAll("input[required], textarea[required], select[required]");
        let formIsValid = true;

        // Validate required inputs
        for (const input of inputs) {
            if (!input.value.trim()) {
                input.reportValidity();
                formIsValid = false;
                break;
            }
        }

        if (!formIsValid) {
            return; 
        }

        // Disable submit button
        btn.setAttribute('disabled', 'disabled');
        myToast("Processing request...");

        $.ajax({
            type: "post",
            url: "https://analysis.helahub.co/operations/products/approve_reject_product.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                let messages = "";
                if (response.success) {
                    messages = response.message;
                    myToast(messages);
                    setTimeout(function() {
                        location.reload(); 
                    }, 2000);
                } else {
                    btn.removeAttribute('disabled');
                    messages = response.message;
                    myToast(messages);
                }
            },
            error: function(xhr, status, error) {
                btn.removeAttribute('disabled');
                myToast("Error occurred. Please try again later.");
            },
        });
    }

    

function dropProduct(event) {
    event.preventDefault();
    const btn = document.querySelector("#toDisableDrop");
    const productId = document.getElementById('dropProductId').value;
    const reason = document.getElementById('dropReason').value.trim();

    // Validate the reason input
    if (!reason) {
        document.getElementById('dropReason').reportValidity(); // Show browser validation error
        return;
    }

    // Disable submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Processing request...");

    // AJAX request to drop_product.php
    $.ajax({
        type: "POST",
        url: "https://analysis.helahub.co/operations/products/drop_product.php",
        data: { productId: productId, reason: reason },
        dataType: "json",
        success: function(response) {
            let messages = "";
            if (response.success) {
                messages = response.message;
                myToast(messages);
                setTimeout(function() {
                    $('#dropModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                }, 2000);
            } else {
                btn.removeAttribute('disabled');
                messages = response.message;
                myToast(messages);
            }
        },
        error: function(xhr, status, error) {
            btn.removeAttribute('disabled');
            myToast("Error occurred. Please try again later.");
        }
    });
}



// PRODUCTS END// PRODUCTS END// PRODUCTS END// PRODUCTS END// PRODUCTS END// PRODUCTS END
// PRODUCTS END// PRODUCTS END// PRODUCTS END// PRODUCTS END// PRODUCTS END// PRODUCTS END















function editProduct2(event) {
    event.preventDefault();

    var form = document.getElementById('editProduct');
    var formData = new FormData(form);

    // Append images to delete
    imagesToDelete.forEach(function(image) {
        formData.append('imagesToDelete[]', image);
    });

    // Disable the submit button
    var btn11 = document.querySelector("#toDisable");
    btn11.setAttribute('disabled', 'disabled');
    myToast("Updating product...");

    // Log FormData contents
    for (var pair of formData.entries()) {
        console.log(pair[0]+ ', ' + pair[1]);
    }

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "operations/products/update.php",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                myToast("Product updated successfully.");
                $('#editModalToggle').modal('hide');
                location.reload(); // Refresh the page to reflect changes
            } else {
                btn11.removeAttribute('disabled');
                myToast(response.message);
            }
        },
        error: function (xhr, status, error) {
            btn11.removeAttribute('disabled');
            myToast("Error occurred. Please try again later.");
        },
    });
}




//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES
//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES
//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES//START ROLES

// Create Role
function newRole(event) {
    event.preventDefault();

    var btn = document.querySelector("#toDisable"); // Button to disable during submission
    const form = document.querySelector("#newRole"); // Form element
    const formData = new FormData(form); // Create FormData object from the form

    const inputs = form.querySelectorAll("input[required], select[required]"); // Required inputs
    let formIsValid = true;

    // Validate all required inputs
    for (const input of inputs) {
        if (!input.value.trim()) {
            input.reportValidity(); // Show browser validation error
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return; // Stop if form is invalid
    }

    // Disable the submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Creating role...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/roles/create.php", // PHP script to handle role creation
        data: formData, // FormData object
        processData: false, // Do not process data (required for file upload)
        contentType: false, // Do not set content type header (required for FormData)
        success: function (response) {
            let messages = "";
            if (response.success) {
                clearFormInputs("#newRole"); // Clear form inputs if role creation succeeds
                messages = "Role created successfully.";
                myToast(messages);
                setTimeout(function () {
                     window.location = "roles";  // Redirect to roles page
                }, 2000);
            } else {
                btn.removeAttribute('disabled'); // Re-enable button if error occurs
                messages = response.message;
                myToast(messages);
            }
        },
        error: function (xhr, status, error) {
            btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}

//edit role

// AJAX function for editing a role
function editRole(event) {
    event.preventDefault();

    var btn = document.querySelector("#editButton"); // Button to disable during submission
    var form = document.querySelector("#editRole"); // Form element
    var formData = new FormData(form); // Create FormData object from the form

    // Disable the submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Updating role...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/roles/update.php", // PHP script to handle role editing
        data: formData, // FormData object
        processData: false, // Do not process data
        contentType: false, // Do not set content type header
        success: function (response) {
            if (response.success) {
                myToast("Role updated successfully.");
                 setTimeout(function () {
                     window.location = "roles";  // Redirect to roles page
                }, 2000);
            } else {
                btn.removeAttribute('disabled'); // Re-enable button if error occurs
                myToast(response.message);
            }
        },
        error: function () {
            btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}
// delete Role

// AJAX function for deleting a role
function deleteRole(event) {
    event.preventDefault();

    var btn = document.querySelector("#deleteButton"); // Button to disable during submission
    var form = document.querySelector("#deleteRole"); // Form element
    var formData = new FormData(form); // Create FormData object from the form

    // Disable the submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Deleting role...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/roles/delete.php", // PHP script to handle role deletion
        data: formData, // FormData object
        processData: false, // Do not process data
        contentType: false, // Do not set content type header
        success: function (response) {
            if (response.success) {
                myToast("Role deleted successfully.");
                setTimeout(function () {
                     window.location = "roles";  // Redirect to roles page
                }, 2000);
            } else {
                btn.removeAttribute('disabled'); // Re-enable button if error occurs
                myToast(response.message);
            }
        },
        error: function () {
            btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}


//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES
//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES
//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES//END ROLES



















//deleteUser
function deleteUser(event) {
    event.preventDefault();

    var btn = document.querySelector("#deleteButton"); // Button to disable during submission
    var form = document.querySelector("#deleteUser"); // Form element
    var formData = new FormData(form); // Create FormData object from the form

    // Disable the submit button
    btn.setAttribute('disabled', 'disabled');
    myToast("Deleting admin user...");

    // Send data via AJAX
    $.ajax({
        type: "post",
        url: "https://analysis.helahub.co/operations/users/delete.php", // PHP script to handle role deletion
        data: formData, // FormData object
        processData: false, // Do not process data
        contentType: false, // Do not set content type header
        success: function (response) {
            if (response.success) {
                myToast("Admin user deleted successfully.");
                setTimeout(function () {
                     window.location = "users";  // Redirect to roles page
                }, 2000);
            } else {
                btn.removeAttribute('disabled'); // Re-enable button if error occurs
                myToast(response.message);
            }
        },
        error: function () {
            btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
            myToast("Error occurred. Please try again later.");
        },
    });
}




//Password Reset


function forgotPassword(event) {
      var btn11 =  document.querySelector("#toDisable");
  event.preventDefault();
  const form2 = document.querySelector("#forgotPassword");
  const forgotData = new FormData(form2);
  const inputs2 = form2.querySelectorAll("input[required]");
  let formIsValid = true;
  for (const input of inputs2) {
    if (!input.value.trim()) {
      input.reportValidity();
      formIsValid = false;
      break;
    }
  }
  if (!formIsValid) {
    return;
  }
btn11.setAttribute('disabled', 'disabled');
  var toast = myToast("Sending Email...");
  $.ajax({
    type: "post",
     url: "https://analysis.helahub.co/operations/users/forgot.php",
    data: forgotData, // Use the FormData object directly, including file data
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content type
    success: function (response) {
      if (response.success) {
        clearFormInputs("#forgotPassword");
        messages = "Check Email for OTP...";
        toast = myToast(messages, 3000, true);
        setTimeout(function () {
          window.location = "reset-otp";
        }, 2000);
      } else if (!response.success) {
        btn11.removeAttribute('disabled');
        messages = response.message;
        toast = myToast(messages, 3000, true);
      }
    },
    error: function (xhr, status, error1) {
      btn11.removeAttribute('disabled');
      toast = myToast(xhr.responseJSON.message, 3000, true);
     
    },
  });
}












function changePassword(event) {
      var btn11 =  document.querySelector("#toDisable");
  event.preventDefault();
  const form1 = document.querySelector("#changePassword");
  const resetData = new FormData(form1);
  const inputs2 = form1.querySelectorAll("input[required]");
  let formIsValid = true;
  for (const input of inputs2) {
    if (!input.value.trim()) {
      input.reportValidity();
      formIsValid = false;
      break;
    }
  }
  if (!formIsValid) {
    return;
  }
btn11.setAttribute('disabled', 'disabled');
  var toast = myToast("Verifying data...");
 
  $.ajax({
    type: "post",
  url: "https://analysis.helahub.co/operations/users/change-password.php",
    data: resetData, // Use the FormData object directly, including file data
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content type
    success: function (response) {
      if (response.success) {
        clearFormInputs("#changePassword");
        messages = "Successfully changed password...";
        toast = myToast(messages, 3000, true);
        setTimeout(function () {
          window.location = "../index";
        }, 2000);
      } else if (!response.success) {
        btn11.removeAttribute('disabled');
        messages = response.message;
        toast = myToast(messages, 3000, true);
      }
    },
    error: function (xhr, status, error1) {
      btn11.removeAttribute('disabled');
      toast = myToast(xhr.responseJSON.message, 3000, true);
      
    },
  });
}









// GROUPS GROUPS GROUPS

function newGroup(event) {
    event.preventDefault();

    const btnSubmit = document.querySelector("#toDisable");
    const formElement = document.querySelector("#newGroup");
    const formData = new FormData(formElement);

    // Validate required inputs
    const requiredInputs = formElement.querySelectorAll("input[required], select[required], textarea[required]");
    let formIsValid = true;
    for (const input of requiredInputs) {
        if (!input.value.trim()) {
            input.reportValidity();
            formIsValid = false;
            break;
        }
    }

    if (!formIsValid) {
        return;
    }

    btnSubmit.setAttribute("disabled", "disabled");
    myToast("Creating Group..."); // or your own toast/message function

    // Use AJAX to send form data
    $.ajax({
        type: "POST",
        url: "https://analysis.helahub.co/operations/groups/create.php",
        data: formData,
        processData: false, // Prevent jQuery from processing data
        contentType: false, // Prevent jQuery from setting content type
        dataType: "json",
        success: function (response) {
            if (response.success) {
                clearFormInputs("#newGroup"); // your function for clearing the form
                myToast("Group added successfully, updating...");
                setTimeout(function () {
                    // Reload or redirect to whichever page you want
                    window.location = "add-group"; 
                }, 1000);
            } else {
                btnSubmit.removeAttribute("disabled");
                myToast(response.message || "Failed to create group. Try again.");
            }
        },
        error: function (xhr, status, error) {
            btnSubmit.removeAttribute("disabled");
            myToast("Error occurred. Try again later.");
            console.error("AJAX Error:", xhr.responseText);
        },
    });
}




function clearFormInputs(id) {
  const form = document.querySelector(id);
  form.reset();
}




// function deleteFinancier(event) {
//       var btn11 =  document.querySelector("#toDisable");
//       btn11.setAttribute('disabled', 'disabled');
//   event.preventDefault();
//   var delete_financier = document.querySelector("#id_value").value;
//   delete_financier = parseInt(delete_financier);
//   myToast("Deleting Financier...");
//   $.ajax({
//     type: "post",
//     url: "https://analysis.helahub.co/operations/financiers/delete.php",
//     data: {
//       delete_financier: delete_financier,
//     },
//     success: function (response) {
//       var messages = "";
//       if (response.success) {
//         clearFormInputs("#deleting");
//         messages = "Financier deleted successfully, updating...";
//         myToast(messages);
//         setTimeout(function () {
//           window.location = "financiers";
//         }, 2000);
//       } else {
//         btn11.removeAttribute('disabled');
//         messages = response.message;
//         myToast(messages);
//       }
//     },
//     error: function (xhr, status, error) {
//       btn11.removeAttribute('disabled');
//       myToast("Error deleting financier");
//     },
//   });
// }


// //approve vendor
//   function approveUser(event) {
//         event.preventDefault();

//         var btn = document.querySelector("#toDisable"); // Button to disable during submission
//         const form = document.querySelector("#editUser"); // Form element
//         const formData = new FormData(form); // Create FormData object from the form

//         const inputs = form.querySelectorAll("input[required], textarea[required], select[required]"); // Required inputs
//         let formIsValid = true;

//         // Validate all required inputs
//         for (const input of inputs) {
//             if (!input.value.trim()) {
//                 input.reportValidity(); // Show browser validation error
//                 formIsValid = false;
//                 break;
//             }
//         }

//         if (!formIsValid) {
//             return; // Stop if form is invalid
//         }

//         // Disable the submit button
//         btn.setAttribute('disabled', 'disabled');
//         myToast("Processing request...");

//         // Send data via AJAX
//         $.ajax({
//             type: "post",
//             url: "https://analysis.helahub.co/operations/vendors/approve_reject.php",
//             data: formData,
//             processData: false,
//             contentType: false,
//             success: function (response) {
//                 let messages = "";
//                 if (response.success) {
//                     clearFormInputs("#editUser"); // Clear form inputs on success
//                     messages = response.message;
//                     myToast(messages);
//                     setTimeout(function () {
//                         location.reload(); // Reload the page to reflect changes
//                     }, 2000);
//                 } else {
//                     btn.removeAttribute('disabled'); // Re-enable button if error
//                     messages = response.message;
//                     myToast(messages);
//                 }
//             },
//             error: function (xhr, status, error) {
//                 btn.removeAttribute('disabled'); // Re-enable button if AJAX fails
//                 myToast("Error occurred. Please try again later.");
//             },
//         });
//     }
