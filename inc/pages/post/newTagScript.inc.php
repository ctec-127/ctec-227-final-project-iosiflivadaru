<?php 
  require_once '../../shared/db_connect.php';
  session_start();
  
  if ($_POST['description'] != '') {
    $_SESSION['description'] = $_POST['description'];
  }

  if (isset($_POST['tag'])) {
    if ($_POST['tag'] != '') {
      $_SESSION['tag'] = $_POST['tag'];
    }
  }
 
  $newTag = $_POST['newTag'];
  $sql = "INSERT INTO `tag` (`id`, `tag`) VALUES (NULL,'$newTag')";
  $result = $db->query($sql);
  echo "success";
  
?>