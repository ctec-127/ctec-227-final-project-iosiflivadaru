<?php 
require_once 'inc/layout/header.inc.php'; 
if ($_SESSION['loggedIn'] == 0) { 
?>




<div class="container my-5 pt-3">
  <div class="row mt-3">

    <div class="col-lg-5">      
      <div class="jumbotron py-3 m-0">
        <h3 class="">Register Now!</h3>
        <h4>Start your journey with LivIt!</h4>
        <hr class="my-4">
        <ul>
          <li>Make Friends</li>
          <li>Share your thoughts</li>
          <li>Post your favourite photos</li>
          <li>Join our weekly contest</li>
          <li>Have Fun!!</li>
        </ul>
      </div>
    </div>
  
    <div class="col-lg-7 mb-3 mb-lg-0 px-5">
      <form action="inc/pages/register/registerScript.inc.php" method="POST" enctype="multipart/form-data">
        <!-- Profile Image -->
        <div class="d-block text-center my-3" id="imgBox">
          <label class="m-0" id="imgLabel" for="profileImg">    
          <i class="fas fa-10x fa-user-circle" id='defaultImg'></i>
          <p class="mt-2 text-muted" id="imgText"><i>Add a profile image</i></p>
          </label>        
          <input class="mx-auto" type="file" id="profileImg" name="profileImg" accept=".jpg,.png,.jpeg" hidden>
        </div>
        <!-- First Name -->
        <div class="form-group">
          <label for="firstName">First Name</label>
          <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
        </div>
        <!-- Last Name -->
        <div class="form-group">
          <label for="lastName">Last Name</label>
          <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" required>
        </div>
        <!-- Email -->
        <div class="form-group d-block mx-auto">
          <label class="text-center" for="email">Email</label>
          <input type="text" class="form-control" id="email" name="email" aria-describedby="textHelp" placeholder="Email" required>
          <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>      
        </div>
        <!-- Confirm Password -->
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>      
        </div>
        <!-- Submit Button -->
        <button type="submit" class="btn bg-dark text-link w-auto px-5" id="submit">Submit</button>
      </form>      
    </div>

  </div><!-- row -->
</div><!-- container -->

<script>
  // Remove the default image when you select your profile image
  function tempImg(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $("#profileImgReg").remove();
        $("#defaultImg").remove();
        $("#imgText").remove();
        $("#alertNewImage").remove(); 
        $("#imgLabel").append(`<img class='rounded-circle profile' src='${e.target.result}' alt='User Image' id='profileImgReg'>`); 
      }
      reader.readAsDataURL(input.files[0]);
    }

    // If user selects an image and cancels the file get the default image back
    var name = $("#profileImg").val();
    if (name == '') {
      $("#profileImgReg").remove();
      $("#alertNewImage").remove(); 
      $("#imgLabel").append("<i class='fas fa-10x fa-user-circle' id='defaultImg'></i>");
      $("#imgLabel").after("<p class='mt-2 text-muted'>Add a profile image</p>")
    }
  }

  // When the file input is being changed execute tempImg function
  $("#profileImg").change(function() {
    tempImg(this);
  });

  // Submit form with AJAX and also check if there is an image selected
  $("form").submit(function(e){
    var img = $("#profileImg").val();
    if (img == '') {
      e.preventDefault();
      $("#alertNewImage").remove();     
      $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Please Upload an Image!</div>");    
    } else {
      e.preventDefault()
      var formData = new FormData($('form')[0]);
      $.ajax({
        type: "post",
        url: "inc/pages/register/registerScript.inc.php",
        data: formData,
        success: function (response) {
          console.log(response)
          // on success redirect user to index.php
          if (response == "success") {
            $(location).attr('href', 'index.php')
          }
          // if there are any errors coming from the script the following alerts will be displayed
          if (response == 'error1') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Your file is too big! Your Image should be less than 2MB!</div>");  
          }
          if (response == 'error2') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>There was an error uploading your file!</div>");  
          }
          if (response == 'error3') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Invalid file type! Only jpg, jpeg, and png files are allowed!</div>");  
          }
          if (response == 'password') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Passwords don't match!</div>");  
          }
          if (response == 'email') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Email already in use!</div>");  
          }
        },
        contentType: false,
        processData: false
      });
    }
  });
</script>

<?php 
  } else { 
    header('Location: index.php');
  }

require_once 'inc/layout/footer.inc.php';
?>