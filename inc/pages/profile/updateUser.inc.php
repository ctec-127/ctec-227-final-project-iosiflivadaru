<?php
require_once '../../shared/db_connect.php';
session_start();

// update profile image
if (isset($_FILES['profileImg']) || $_FILES['profileImg']['name'] == 0) {

  // update email
  if ($_POST['email'] != '') {
    $email = $_POST['email'];
    $sql = "SELECT email FROM user WHERE email = '$email' AND id != {$_SESSION['id']} LIMIT 1";
    $result = $db->query($sql);
    $usedEmail = $result->fetch_assoc();
    
    if ($email != $usedEmail['email']) {    
      $sql = "UPDATE `user`
                SET `email` = '$email'
                WHERE `id` = '{$_SESSION['id']}'
              ";

      $result = $db->query($sql);
    } else {
      echo "email ";    
    }
  }

  // update password
  if (isset($_POST['changePass'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPass = $_POST['confirmPassword'];

    $getHashedPassword = "SELECT password FROM user WHERE id = '{$_SESSION['id']}'";

    $result = $db->query($getHashedPassword);
    while ($row = $result->fetch_assoc()) { 
      $hashedPassword = $row['password'];
    }

    if (password_verify($oldPassword, $hashedPassword)) {
      if ($newPassword == $confirmPass) {    
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);    
        
        $sql = "UPDATE `user`
                SET `password` = '$hashedPassword'
                WHERE `id` = '{$_SESSION['id']}'
              ";
        $result = $db->query($sql);
      } else {
        echo "password ";
      }
    } else {
      echo "wrongPass ";
    }  
  }

  // update user info
  $sql = "SELECT first_name, last_name FROM user WHERE `id` = {$_SESSION['id']} LIMIT 1";
  $result = $db->query($sql);
  $row = $result->fetch_assoc();
  $firstName = $row['first_name'];
  $lastName  = $row['last_name'];
  $bio = "{$_POST['bio']}";

  if ($_POST['firstName'] != '') {
    $firstName = $_POST['firstName'];
  }
  if ($_POST['lastName'] != '') {
    $lastName = $_POST['lastName'];
  }  

  $sql = "UPDATE `user`
          SET `first_name` = ?, `last_name` = ?, `bio` = ?
          WHERE `id` = {$_SESSION['id']}
        ";
  // echo $sql;
  $stmt = $db->prepare($sql);
  $stmt->bind_param("sss", $firstName, $lastName, $bio);
  $stmt->execute();
  $stmt->close();

  $_SESSION['firstName'] = $firstName;
  echo "info ";
  

  // update image
  if (!empty($_FILES['profileImg']['name'])) {
    $file          =  $_FILES['profileImg'];             // set $_FILES array to a variable
  
    $fileName      =  $_FILES['profileImg']['name'];     // set file name
    $fileTmpName   =  $_FILES['profileImg']['tmp_name']; // get temp location of the file
    $fileSize      =  $_FILES['profileImg']['size'];     // get size of file
    $fileError     =  $_FILES['profileImg']['error'];    // check for errors
    $fileType      =  $_FILES['profileImg']['type'];     // get file type
  
    $fileExt       =  explode('.', $fileName);     // get last bit of $fileName aka. the extension
    $fileActualExt =  strtolower(end($fileExt));   // get the end of the file name (ext) and make it all lowercase
  
    $allowed       =  array('jpg', 'jpeg', 'png'); // list of allowed file extesnstions
    if (in_array($fileActualExt, $allowed)) { // make sure it's a valid file extension
      if ($fileError === 0) { // make sure there are no errors
        if ($fileSize < 2000000) { // make sure the file size isn't too big
          // update profileImg name
          $sql = "SELECT first_name, last_name FROM user WHERE `id` = {$_SESSION['id']} LIMIT 1";
          $result = $db->query($sql);
          $row = $result->fetch_assoc();
          $firstName = $row['first_name'];
          $lastName  = $row['last_name'];
          $userId = $_SESSION['id'];
          $fileNameNew     = $userId."_".$firstName."_".$lastName.".".$fileActualExt; // append on current time so we don't get duplicates
          $fileDestination = '../../../img/'.$fileNameNew; // set a destination
          move_uploaded_file($fileTmpName, $fileDestination); // move file from temp location to perminent location
        
          $sql = "
            UPDATE `user`
            SET `profile_img` = '$fileNameNew'
            WHERE `id` = '{$_SESSION['id']}'
          ";
  
          $result = $db->query($sql);
          // echo "success ";
        } else { // if there are any errors.....
          echo "error1 ";
        }
      } else {
        echo "error2 ";
      }
    } else {
      echo "error3 ";
    }
  }
}

// header("location:../../../profile.php");
?>