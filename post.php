<?php 
  require_once 'inc/layout/header.inc.php'; 
  if ($_SESSION['loggedIn'] == 1) { 
    require_once 'inc/pages/post/postScript.inc.php';
  } else { 
    header('Location: index.php');
  }
  require_once 'inc/layout/footer.inc.php';
?>