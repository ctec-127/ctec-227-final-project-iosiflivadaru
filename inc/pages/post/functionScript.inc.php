<?php 
  require_once '../../shared/db_connect.php';
  session_start();
  $currentDate = date("Y-m-d\TH:i:s");

  if ($_FILES['postImg']['name'] == '') {
    // add post to database
    $description   = $_POST['description'];
    $userId = $_SESSION['id'];
    $sql = "INSERT INTO  `post` (
              `id`, `user_id`, `description`, `post_img`, `date`
            ) 
            VALUES (
              NULL, ?, ?, 'noImg', '$currentDate'
            )
            ";     
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $userId, $description);
    $stmt->execute();
    $stmt->close();
    // $result = $db->query($sql);

    // get user id so we can update postImg name
    $sql    = "SELECT id FROM post WHERE user_id = $userId AND post_img = 'noImg'";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
      $postId = $row['id'];
    }
    $result    = $db->query($sql);

    // add tags to post_tag
    if (!empty($_POST['tag'])) {
      $tag = $_POST['tag'];
      for ($i=0; $i < count($tag); $i++) { 
        $sql = "INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`) VALUES (NULL, $postId, {$tag[$i]})";
        $result = $db->query($sql);
      }
    }

    echo "post";
  } else {
    upload_file($db, $currentDate); 
  }


  function upload_file($db, $currentDate) { // Begin upload_file function
    $file          =  $_FILES['postImg'];             // set $_FILES array to a variable
  
    $fileName      =  $_FILES['postImg']['name'];     // set file name
    $fileTmpName   =  $_FILES['postImg']['tmp_name']; // get temp location of the file
    $fileSize      =  $_FILES['postImg']['size'];     // get size of file
    $fileError     =  $_FILES['postImg']['error'];    // check for errors
    $fileType      =  $_FILES['postImg']['type'];     // get file type
  
    $fileExt       =  explode('.', $fileName);     // get last bit of $fileName aka. the extension
    $fileActualExt =  strtolower(end($fileExt));   // get the end of the file name (ext) and make it all lowercase
  
    $allowed       =  array('jpg', 'jpeg', 'png'); // list of allowed file extesnstions
    
    if (in_array($fileActualExt, $allowed)) { // make sure it's a valid file extension
      if ($fileError === 0) { // make sure there are no errors
        if ($fileSize < 2000000) { // make sure the file size isn't too big
  
          // add post to database
          $description   = $_POST['description'];
          $userId = $_SESSION['id'];

          // Join Contest
          $contest = 0;
          $active = 0;
          if (isset($_POST['contest'])) {
            $sql = "UPDATE user SET contest_join = 1 WHERE id = {$_SESSION['id']}";
            $result = $db->query($sql);
            $_SESSION['contest'] = 1;
            $contest = 1;
            $active = 1;
          }
          $sql = "INSERT INTO  `post` (
                    `id`, `user_id`, `description`, `post_img`, `date`, `contest`, `active`
                  ) 
                  VALUES (
                    NULL, ?, ?, 'temp', '$currentDate', ?, ?
                  )
                  ";      
          $stmt = $db->prepare($sql);
          $stmt->bind_param("ssii", $userId, $description, $contest, $active);
          $stmt->execute();
          $stmt->close();

          // get user id so we can update postImg name
          $sql = "SELECT id FROM post WHERE user_id = $userId AND post_img = 'temp'";
          $result = $db->query($sql);
          while ($row = $result->fetch_assoc()) {
            $postId = $row['id'];
          }

          // add tags to post_tag
          if (!empty($_POST['tag'])) {
            $tag = $_POST['tag'];
            for ($i=0; $i < count($tag); $i++) { 
              $sql = "INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`) VALUES (NULL, $postId, {$tag[$i]})";
              $result = $db->query($sql);
            }
          }

          // upload post image
          $fileNameNew     = "post_".$postId.".".$fileActualExt; // append on current time so we don't get duplicates
          $fileDestination = '../../../img/posts/'.$fileNameNew; // set a destination
          move_uploaded_file($fileTmpName, $fileDestination); // move file from temp location to perminent location
        
          $sql2 = "
            UPDATE `post`
            SET `post_img` = '$fileNameNew'
            WHERE `id` = '$postId'
          ";  
          $result = $db->query($sql2);
          
          echo "post";
          $_SESSION['alert-newUser'] = 1;
          $_SESSION['alert-imageSuccess'] = "<p class='error m-0'>Thank you for joining us! Go ahead and Sign In!</p>";

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