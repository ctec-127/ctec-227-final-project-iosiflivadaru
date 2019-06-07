<?php 
require_once '../../shared/db_connect.php';
session_start();

if (isset($_POST['like'])) {
  $user = $_SESSION['id'];
  $postId = $_POST['postId'];

  $sql = "SELECT user_id, post_id FROM `like` WHERE user_id = $user AND post_id = $postId";
  $result = $db->query($sql);
  
  if (mysqli_num_rows($result) != 1) {
    $sql = "INSERT INTO `like`(`id`, `user_id`, `post_id`) VALUES (NULL, $user, $postId)";
    $result = $db->query($sql);
  }
}

if (isset($_POST['dislike'])) {
  $user = $_SESSION['id'];
  $postId = $_POST['postId'];

  $sql = "SELECT user_id, post_id FROM `like` WHERE user_id = $user AND post_id = $postId";
  $result = $db->query($sql);

  if (mysqli_num_rows($result) == 1) {
    $sql = "DELETE FROM `like` WHERE user_id = $user AND post_id = $postId";
    $result = $db->query($sql);
  }
}

?>