<?php 
session_start();
require_once '../../shared/db_connect.php';

if (isset($_POST['itemTag'])) {
  $itemId = $_POST['itemId'];
  $itemTag = $_POST['itemTag'];  
  
  $sql = "SELECT price, user.contest_tokens, user.bio_limit FROM market JOIN user WHERE market.id = $itemId AND user.id = {$_SESSION['id']} LIMIT 1";
  $result = $db->query($sql);
  $row = $result->fetch_assoc();

  $itemPrice = $row['price'];
  $userTokens = $row['contest_tokens'];  
  $validatePurchase = $userTokens - $itemPrice;

  if ($validatePurchase >= 0) {
    if ($itemTag == 'badge') {
      $sql = "UPDATE user SET contest_tokens = $validatePurchase WHERE id={$_SESSION['id']}";
      $result = $db->query($sql);
  
      $sql = "INSERT INTO `user_badge`(`user_id`, `badge_id`) VALUES ({$_SESSION['id']},$itemId)";
      $result = $db->query($sql);
      $_SESSION['purchase'] = 1;
      echo 1;
    }

    if ($itemTag == 'bioLimit') {
      $newBioLimit = $row['bio_limit'] + 20;
      $sql = "UPDATE user SET contest_tokens = $validatePurchase, bio_limit = $newBioLimit WHERE id={$_SESSION['id']}";
      $result = $db->query($sql);
      echo 1;
    }
  }
}

?>