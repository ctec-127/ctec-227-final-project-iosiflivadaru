<?php 
require_once '../../shared/db_connect.php';
session_start();

if (isset($_POST['upvote'])) {
  $user = $_SESSION['id'];
  $postId = $_POST['postId'];

  $sql = "SELECT user_id, post_id FROM `contest` WHERE user_id = $user AND post_id = $postId";
  $result = $db->query($sql);
  
  if (mysqli_num_rows($result) != 1) {
    $sql = "INSERT INTO `contest`(`id`, `user_id`, `post_id`, `active`) VALUES (NULL, $user, $postId, 1)";
    $result = $db->query($sql);
  }
}

if (isset($_POST['downvote'])) {
  $user = $_SESSION['id'];
  $postId = $_POST['postId'];

  $sql = "SELECT user_id, post_id FROM `contest` WHERE user_id = $user AND post_id = $postId";
  $result = $db->query($sql);

  if (mysqli_num_rows($result) == 1) {
    $sql = "DELETE FROM `contest` WHERE user_id = $user AND post_id = $postId";
    $result = $db->query($sql);
  }
}

?>