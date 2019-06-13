<?php require_once 'inc/layout/header.inc.php';
if ($_SESSION['loggedIn'] == 0) {
?>

    <div class="container my-5 pt-4">        

      <?php
      if (isset($_SESSION['alert-newUser'])) {
        if ($_SESSION['alert-newUser'] == 1) {
          echo "<div class='row'>";
          echo "<div class='col-12'>";
          echo "<div class='alert alert-success w-auto text-center' role='alert' id='alertNewImage'>{$_SESSION['alert-imageSuccess']}</div>";
          echo "</div>";
          echo "</div>";
          $_SESSION['alert-newUser'] = 0;
        }
      }
      ?>        

      <div class="row">

        <div class="col-lg-8 mt-0 mt-lg-3 d-flex align-items-center">
          
          <div class="jumbotron bg-white shadow-sm py-3 m-0">
            <h3 class="font-weight-light text-center mt-1">Welcome to <?=$config['appName'];?>!</h3>
            <hr class="my-4">
            <h5 class="font-weight-light text-center">Make Friends, share your favourite photos and join our weekly Photo Contest!</h5>
            <h5 class="text-center"><a class="text-link-normal" href="register.php"><u>Register Now!</u></a></h5>
          </div>

        </div>
      
        <div class="col-lg-4 d-flex align-items-lg-center justify-content-center mt-3">

          <form action="inc/pages/login/loginScript.inc.php" method="POST">
            <div class="form-group d-block mx-auto">
              <label class="text-center" for="email">Email</label>
              <input type="text" class="form-control w-auto<?php if($_SESSION['alert-login'] == 2) echo " is-invalid";?>" id="email" name="email" aria-describedby="textHelp" placeholder="email">
              <div class="invalid-feedback">
                Please enter the correct email.
              </div>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control w-auto <?php if($_SESSION['alert-login'] == 2) echo "is-invalid";?>" id="password" name="password" placeholder="password">
              <div class="invalid-feedback">
                Please enter the correct password.
              </div>
              
            </div>
            <div class="form-group">
              <div class="custom-control custom-checkbox mr-sm-2">
                <input type="checkbox" class="custom-control-input text-link" id="customControlAutosizing" name="remember">
                <label class="custom-control-label" for="customControlAutosizing">Remember me</label>
              </div>
            </div>
            <button type="submit" class="btn bg-dark text-link w-100">Sign In</button>
          </form>              
        
        </div>


      </div><!-- row -->
    </div><!-- container -->
    
<?php 
$_SESSION['alert-login'] = 0;
} else {
  header('location: home.php');
}
require_once 'inc/layout/footer.inc.php';
?>

