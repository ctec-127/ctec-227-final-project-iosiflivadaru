<?php 
  session_start();
  require_once '../../shared/db_connect.php';
  $email    = $_POST['email'];
  $password = $_POST['password'];
  if (isset($_POST['remember'])) {
    $remember = $_POST['remember'];
  }

  $getHashedPassword = "SELECT password FROM user WHERE email = '$email'";
  echo $getHashedPassword;
  $result = $db->query($getHashedPassword);
  while ($row = $result->fetch_assoc()) { 
    $hashedPassword = $row['password'];
  }

  if (password_verify($password, $hashedPassword)) {
    $sql = "SELECT id, first_name, last_name, profile_img FROM user WHERE `email` = '$email' AND `password` = '$hashedPassword' LIMIT 1";

    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    if (mysqli_num_rows($result) > 0) {
      $_SESSION['loggedIn'] = 1;
      $_SESSION['id'] = $row['id'];
      $_SESSION['firstName'] = $row['first_name'];
      $_SESSION['lastName'] = $row['last_name'];
      $_SESSION['profileImg'] = $row['profile_img'];
      if (isset($remember)) {
        setcookie('loggedIn', $row['id'], time() + (86400 * 90), "/");
      }
      header("location: ../../../home.php");
    }
  } else {
    $_SESSION['loggedIn']    = 0;
    $_SESSION['alert-login'] = 2;
    header("location: ../../../index.php");
  }
  
?>