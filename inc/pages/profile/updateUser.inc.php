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
  
    $allowed       =  array('jpg', 'jpeg'); // list of allowed file extesnstions
    if (in_array($fileActualExt, $allowed)) { // make sure it's a valid file extension
      if ($fileError === 0) { // make sure there are no errors
        if ($fileSize < 25000000) { // make sure the file size isn't too big
          // update profileImg name
          $sql = "SELECT first_name, last_name FROM user WHERE `id` = {$_SESSION['id']} LIMIT 1";
          $result = $db->query($sql);
          $row = $result->fetch_assoc();
          $firstName = $row['first_name'];
          $lastName  = $row['last_name'];
          $userId = $_SESSION['id'];


          $file = $_FILES['profileImg']['tmp_name']; 
          $sourceProperties = getimagesize($file);
          $fileNewName = $firstName."_".$lastName.".".$fileActualExt;
          $folderPath = "../../../img/";
          $ext = pathinfo($_FILES['profileImg']['name'], PATHINFO_EXTENSION);
          $imageType = $sourceProperties[2];


          switch ($imageType) {


              case IMAGETYPE_PNG:
                  $imageResourceId = imagecreatefrompng($file); 
                  $targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1]);
                  imagepng($targetLayer,$folderPath. $userId."_". $fileNewName);
                  break;


              case IMAGETYPE_GIF:
                  $imageResourceId = imagecreatefromgif($file); 
                  $targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1]);
                  imagegif($targetLayer,$folderPath. $userId."_". $fileNewName);
                  break;


              case IMAGETYPE_JPEG:
                  $imageResourceId = imagecreatefromjpeg($file); 
                  $targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1]);
                  imagejpeg($targetLayer,$folderPath. $userId."_". $fileNewName);


                  break;


              default:
                  echo "Invalid Image type.";
                  exit;
                  break;
          }


          move_uploaded_file($file, $folderPath. $fileNewName);
          // echo "Image Resize Successfully.";

          $image = imagecreatefromstring(file_get_contents($folderPath. $fileNewName));
          $exif = exif_read_data($folderPath. $fileNewName);
          if(!empty($exif['Orientation'])) {
              // echo $exif['Orientation'];
              switch($exif['Orientation']) {
                  case 8:
                                          
                      list($width, $height) = getimagesize($folderPath. $fileNewName);
                      if ($width > 1920) {
                          $newwidth = $width / 4;
                          $newheight = $height / 4;
                      } else {
                          $newwidth = $width;
                          $newheight = $height;
                      }

                      // Load
                      $thumb = imagecreatetruecolor($newwidth, $newheight);
                      $source = imagecreatefromjpeg($folderPath. $fileNewName);

                      // Resize
                      imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                      $image = imagerotate($thumb,90,0);

                      // Save
                      imagejpeg($image, $folderPath. $userId."_". $fileNewName);
                      break;
                  case 3:
                                          
                      list($width, $height) = getimagesize($folderPath. $fileNewName);

                      $newwidth = 500;
                      $newheight = 500;                        

                      // Load
                      $thumb = imagecreatetruecolor($newwidth, $newheight);
                      $source = imagecreatefromjpeg($folderPath. $fileNewName);

                      // Resize
                      imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                      $image = imagerotate($thumb,180,0);

                      // Save
                      imagejpeg($image, $folderPath. $userId."_". $fileNewName);
                      break;
                  case 6:
                      
                      list($width, $height) = getimagesize($folderPath. $fileNewName);

                      $newwidth = 500;
                      $newheight = 500;                        

                      // Load
                      $thumb = imagecreatetruecolor($newwidth, $newheight);
                      $source = imagecreatefromjpeg($folderPath. $fileNewName);

                      // Resize
                      imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                      $image = imagerotate($thumb,-90,0);

                      // Save
                      imagejpeg($image, $folderPath. $userId."_". $fileNewName);
                      break;
              }
          }

          unlink($folderPath. $fileNewName);


          // OLD CODE
          // $fileNameNew     = $userId."_".$firstName."_".$lastName.".".$fileActualExt; // append on current time so we don't get duplicates
          // $fileDestination = '../../../img/'.$fileNameNew; // set a destination
          // move_uploaded_file($fileTmpName, $fileDestination); // move file from temp location to perminent location
        
          $sql = "
            UPDATE `user`
            SET `profile_img` = '{$userId}_$fileNewName'
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
function imageResize($imageResourceId,$width,$height) {

  if ($width > 1920) {
      $targetWidth =$width / 4;
      $targetHeight =$height / 4;
  } else {
      $targetWidth =$width;
      $targetHeight =$height;
  }

  $targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
  imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);

  return $targetLayer;
}
// header("location:../../../profile.php");
?>