<?php
   // Begin upload_file function
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
            if ($fileSize < 10000000) { // make sure the file size isn't too big
                $rand = rand(0,100);
                $fileNameNew = "temp$rand.".$fileActualExt; // append on current time so we don't get duplicates
                $fileDestination = "../img/".$fileNameNew; // set a destination
                $_SESSION['tempImg'] = $fileNameNew;   
                move_uploaded_file($fileTmpName, $fileDestination); // move file from temp location to perminent location
                echo "success " . $fileNameNew;
            } else { // if there are any errors.....
              echo "<p class='error m-0'>Your file is too big!</p>";
            }
        } else {
          echo "<p class='error m-0'>There was an error uploading your file!</p>";
        }
    } else {
      echo "<p class='error m-0'>Invalid file type! Only jpg, jpeg, and png files are allowed!</p>";
    }
  //end upload_file function
?>