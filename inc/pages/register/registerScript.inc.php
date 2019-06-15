<?php
require_once '../../shared/db_connect.php';
session_start();
upload_file($db);  

function upload_file($db) { // Begin upload_file function
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

        // add user to database
        $firstName   = $_POST['firstName'];
        $lastName    = $_POST['lastName'];
        $email       = $_POST['email'];
        $password    = $_POST['password'];
        $confirmPass = $_POST['confirmPassword'];

        $sql = "SELECT email FROM user WHERE email = '$email' LIMIT 1";
        $result = $db->query($sql);
        $usedEmail = $result->fetch_assoc();
        
        if ($email != $usedEmail['email']) {
          if ($password == $confirmPass) {    
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);    
            $sql       = "INSERT INTO  `user` (
                            `id`, `first_name`, `last_name`, `email`, `password`, `profile_img`, `fav_tag_limit`, `bio_limit`
                          ) 
                          VALUES (
                            NULL, '$firstName', '$lastName', '$email', '$hashedPassword', 'temp', 1, 100
                          )
                          ";     
            // echo $sql;       
            $result    = $db->query($sql);
          
            // get user id so we can update profileImg name
            $sql    = "SELECT id FROM user WHERE first_name = '$firstName' AND last_name = '$lastName'";
            $result = $db->query($sql);
            while ($row = $result->fetch_assoc()) {
              $userId = $row['id'];
            }

            // update profileImg name
            $fileNameNew     = $userId."_".$firstName."_".$lastName.".".$fileActualExt; // append on current time so we don't get duplicates
            $fileDestination = '../../../img/'.$fileNameNew; // set a destination
            move_uploaded_file($fileTmpName, $fileDestination); // move file from temp location to perminent location
          
            $sql2 = "
              UPDATE `user`
              SET `profile_img` = '$fileNameNew'
              WHERE `id` = '$userId'
            ";

            $result = $db->query($sql2);
            echo "success";
            $_SESSION['alert-newUser'] = 1;
            $_SESSION['alert-imageSuccess'] = "<p class='error m-0'>Thank you for joining us! Go ahead and Sign In!</p>";
          } else {
            echo "password";
          }
        } else {
          echo "email";          
        }
      } else { // if there are any errors.....
        echo "error1";
      }
    } else {
      echo "error2";
    }
  } else {
    echo "error3";
  }
} //end upload_file function
?>