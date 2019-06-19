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

  $allowed       =  array('jpg', 'jpeg'); // list of allowed file extesnstions
  
  if (in_array($fileActualExt, $allowed)) { // make sure it's a valid file extension
    if ($fileError === 0) { // make sure there are no errors
      if ($fileSize < 25000000) { // make sure the file size isn't too big

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


            // *********************

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

            // *********************
            // OLD CODE
            // update profileImg name
            // $fileNameNew     = $userId."_".$firstName."_".$lastName.".".$fileActualExt; // append on current time so we don't get duplicates
            // $fileDestination = '../../../img/'.$fileNameNew; // set a destination
            // move_uploaded_file($fileTmpName, $fileDestination); // move file from temp location to perminent location
          
            $sql2 = "
              UPDATE `user`
              SET `profile_img` = '{$userId}_$fileNewName'
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
?>