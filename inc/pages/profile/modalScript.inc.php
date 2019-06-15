<?php 
session_start();
require_once '../../shared/db_connect.php';

if (isset($_GET['action'])) {
  if ($_GET['action'] == 'follow') {
    follow($db);
  } else if ($_GET['action'] == 'unfollow') {
    unfollow($db);
  }
}

if (isset($_POST['modal'])) {
  if ($_POST['modal'] == 'follow') {
    $userId = $_POST['userId'];
    followers($db, $userId);
  } else if ($_POST['modal'] == 'following') {
    $userId = $_POST['userId'];
    following($db, $userId);
  } else if ($_POST['modal'] == 'edit') {
    edit($db);
  }
}

// FUNCTIONS
function followers($db, $userId) {
  $sql = "SELECT user.first_name, user.last_name, profile_img, user.id FROM user JOIN follow ON follow.user_id = user.id WHERE follow.following = $userId";

  $result = $db->query($sql);
  while ($row = $result->fetch_assoc()) {
    echo "<div class='mb-3'>";
    echo "<img class='rounded-circle profile d-inline mr-3 cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:55px; height:60px;' alt='User Image'>";
    echo "<h4 class='d-inline cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h4>";
    echo "</div>";
  }
  // echo $sql;
}

function following($db, $userId) {
  $currentUser = $_SESSION['id'];
  $sql = "SELECT user.first_name, user.last_name, profile_img, user.id, follow.following FROM user JOIN follow ON follow.following = user.id WHERE follow.user_id = $userId";

  $result = $db->query($sql);
  while ($row = $result->fetch_assoc()) {
    echo "<div class='mb-3 d-flex'>";
    echo "<img class='rounded-circle profile d-inline mr-3 cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:55px; height:60px;' alt='User Image'>";
    echo "<h4 class='d-flex align-items-center cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h4>";
    if ($userId == $currentUser) {
      echo "<a class='align-self-center ml-auto text-right text-danger d-inline bg-dark rounded py-1 px-2 mr-1 cursor-pointer' href='inc/pages/profile/modalScript.inc.php?userId={$row['following']}&action=unfollow&edit'>Unfollow</a>";
    }
    echo "</div>";
  }
  // echo $sql;
}

function follow($db) {
  $currentUser = $_SESSION['id'];
  $profileUser = $_GET['userId'];
  $sql = "INSERT INTO `follow`(`id`, `user_id`, `following`) VALUES (NULL, $currentUser, $profileUser)";
  $result = $db->query($sql);
  header("location: ../../../profile.php?userId=$profileUser");
}

function unfollow($db) {
  $currentUser = $_SESSION['id'];
  $profileUser = $_GET['userId'];
  $sql = "DELETE FROM `follow` WHERE user_id = $currentUser AND following = $profileUser";
  $result = $db->query($sql);
  if (isset($_GET['edit'])) {
    header("location: ../../../profile.php");
  } else {
    header("location: ../../../profile.php?userId=$profileUser");
  }
}

function edit($db) {
  $id  = $_SESSION['id'];
  $sql = "SELECT * FROM user WHERE id=".$id;

  $result = $db->query($sql);

  while ($row = $result->fetch_assoc()) {
    $firstName  = $row['first_name'];
    $lastName   = $row['last_name'];
    $email      = $row['email'];
    $bio        = $row['bio'];
    $profileImg = $row['profile_img'];
    $bioLimit   = $row['bio_limit'];
  }
?>


<form action="inc/pages/profile/updateUser.inc.php" method="POST" enctype="multipart/form-data">
  <!-- Profile Image -->
  <div class="d-block text-center my-2" id="imgBox">
    <label class="m-0" id="imgLabel" for="profileImg">    
      <img class='rounded-circle profile' src='img/<?=$profileImg?>' alt='User Image' id='goodProfileImg'>
    </label>        
    <input class="mx-auto" type="file" id="profileImg" name="profileImg" accept=".jpg,.png,.jpeg" value="img/<?=$profileImg?>" hidden>
  </div>
  <!-- First Name -->
  <div class="form-group">
    <label for="firstName">First Name</label>
    <input type="text" class="form-control" id="firstName" name="firstName" placeholder="<?=$firstName?>">
  </div>
  <!-- Last Name -->
  <div class="form-group">
    <label for="lastName">Last Name</label>
    <input type="text" class="form-control" id="lastName" name="lastName" placeholder="<?=$lastName?>">
  </div>
  <!-- Email -->
  <div class="form-group d-block mx-auto">
    <label class="text-center" for="email">Email</label>
    <input type="text" class="form-control" id="email" name="email" aria-describedby="textHelp" placeholder="<?=$email?>">
    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
  </div>
  <!-- Bio -->
  <div class="mb-3">
    <label for="validationTextarea">Bio <span class="text-muted font-italic">(characters limit: <?=$bioLimit?>)</span></label>
    <textarea class="form-control" name="bio" placeholder="<?= ($bio == '')? "Tell us something about you!" : '' ?>" maxlength="<?=$bioLimit?>" rows="6"><?= ($bio != '')? $bio : '' ?></textarea>
  </div>
  <!-- Favourite Tag -->
  <!-- <div class="form-group">
    <label for="selectTag" class="form-control-label">Favourite Tag</label>
    <select class="form-control selectpicker" id="selectTag" data-live-search="true">
      <option value="" hidden>Tags</option>
      <?php 
        $sql = "SELECT * FROM tag";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<option data-tokens='{$row['tag']}'>{$row['tag']}</option>";
        }
      ?>
    </select>
  </div> -->
  <!-- Allow Change Password -->
  <div id="passAlert"></div>
  <div class="form-group">
    <div class="custom-control custom-checkbox mr-sm-2">
      <input type="checkbox" class="custom-control-input text-link" id="customControlAutosizing" name="changePass">
      <label class="custom-control-label" for="customControlAutosizing">Change Password</label>
    </div>
  </div>
  <!-- Change Password inputs -->
  <div class="d-none" id="passInputs">
    <!-- Old Password -->
    <div class="form-group">
      <label for="oldPassword">Old Password</label>
      <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="Old Password">      
    </div>
    <!-- New Password -->
    <div class="form-group">
      <label for="newPassword">New Password</label>
      <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="New Password">      
    </div>
    <!-- Confirm Password -->
    <div class="form-group">
      <label for="confirmPassword">Confirm Password</label>
      <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm New Password">      
    </div>
  </div>
  <!-- Submit Button -->
  <div class="d-flex flex-row-reverse">
    <button type="reset" class="btn btn-outline-dark  text-link-normal" onclick="resetForm()">Reset</button>
    <button type="submit" class="btn bg-dark text-link w-auto px-5 float-right mr-3">Submit</button>
  </div>
</form>    
<?php
} // edit function ending
?>

<script>

  $(function() {
    $('.selectpicker').selectpicker();
  });

  function resetForm() {
    $("#editProfileImg").remove();
    $(".alertNewImage").remove(); 
    $("#goodProfileImg").removeClass("d-none");
    if($("#customControlAutosizing").checked) {
        $("#passInputs").removeClass("d-none");
        $("#oldPassword").prop("required", true);
        $("#newPassword").prop("required", true);
        $("#confirmPassword").prop("required", true);
      } else {
        $("#passInputs").addClass("d-none");
        $("#oldPassword").removeAttr("required");
        $("#newPassword").removeAttr("required");
        $("#confirmPassword").removeAttr("required");
      }
  }
  $("#customControlAutosizing").change(function() {
      if(this.checked) {
        $("#passInputs").removeClass("d-none");
        $("#oldPassword").prop("required", true);
        $("#newPassword").prop("required", true);
        $("#confirmPassword").prop("required", true);
      } else {
        $("#passInputs").addClass("d-none");
        $("#oldPassword").removeAttr("required");
        $("#newPassword").removeAttr("required");
        $("#confirmPassword").removeAttr("required");
      }
  });
  // Remove the default image when you select your profile image
  function tempImg(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $("#editProfileImg").remove();
        $("#defaultImg").remove();
        $("#imgText").remove();
        $(".alertNewImage").remove(); 
        $("#imgLabel").append(`<img class='rounded-circle profile' src='${e.target.result}' alt='User Image' id='editProfileImg'>`); 
      }
      reader.readAsDataURL(input.files[0]);
    }

    // If user selects an image and cancels the file get the default image back
    var name = $("#profileImg").val();
    if (name == '') {
      $("#editProfileImg").remove();
      $(".alertNewImage").remove(); 
      $("#imgLabel").append("<i class='fas fa-10x fa-user-circle' id='defaultImg'></i>");
      $("#imgLabel").after("<p class='mt-2 text-muted' id='imgText'>Add a profile image</p>")
    }
  }

  // When the file input is being changed execute tempImg function
  $("#profileImg").change(function() {
    $("#goodProfileImg").addClass("d-none");
    tempImg(this);
  });

  // Submit form with AJAX and also check if there is an image selected
  $("form").submit(function(e){
      e.preventDefault();
      $(".alertNewImage").remove(); 
      var formData = new FormData($('form')[0]);
      $.ajax({
        type: "post",
        url: "inc/pages/profile/updateUser.inc.php",
        data: formData,
        success: function (response) {
          // console.log(response)
          // on success redirect user to index.php
          var results = response.split(" ");
          console.log(results.length)

          if (results.length > 2) {
            results.forEach(function(item, array) {
              console.log(results);
              if (item == 'error1') { 
                $("#imgBox").after("<div class='alert alert-danger my-3 w-auto text-center alertNewImage' role='alert' >Your file is too big! Your Image should be less than 2MB!</div>");  
              } else if (item == 'error2') { 
                $("#imgBox").after("<div class='alert alert-danger my-3 w-auto text-center alertNewImage' role='alert' >There was an error uploading your file!</div>");  
              } else if (item == 'error3') { 
                $("#imgBox").after("<div class='alert alert-danger my-3 w-auto text-center alertNewImage' role='alert' >Invalid file type! Only jpg, jpeg, and png files are allowed!</div>");  
              } else if (item == 'password') { 
                $("#passAlert").after("<div class='alert alert-danger mb-3 w-auto text-center alertNewImage' role='alert' >Passwords don't match!</div>");  
              } else if (item == 'wrongPass') { 
                $("#passAlert").after("<div class='alert alert-danger mb-3 w-auto text-center alertNewImage' role='alert' >Old Password is wrong!</div>");  
              } else if (item == 'email') { 
                $("#passAlert").after("<div class='alert alert-danger mb-3 w-auto text-center alertNewImage' role='alert' >Email already in use!</div>");                
              }
            });
          } else {
            $(location).attr('href', 'profile.php?update')
          }
          // if there are any errors coming from the script the following alerts will be displayed
        },
        contentType: false,
        processData: false
      });
    
    });
</script>