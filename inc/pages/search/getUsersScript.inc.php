<?php 
  require_once '../../shared/db_connect.php';
  $search = $_POST['search'];
  

  if (substr( $search, 0, 1 ) === '#') {
    $tag = str_replace("#","",$search);
    $sql = "SELECT id FROM tag WHERE tag = '$tag' LIMIT 1";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    // echo $sql;
    if (mysqli_num_rows($result) != 0) {
      echo (int)$row['id'];
    } else {
      echo "noTag";
    }
  } else {
    $searchArray = explode(" ", $search);
    if (!empty($search)) {
      $sql = "SELECT * FROM user WHERE ";
      $flag = true;
      for ($i=0; $i < count($searchArray); $i++) { 
        if ($flag) {
          $sql .= "first_name LIKE '%{$searchArray[$i]}%' OR last_name LIKE '%{$searchArray[$i]}%' ";
          $flag = false;
        } else {
          $sql .= "OR first_name LIKE '%{$searchArray[$i]}%' OR last_name LIKE '%{$searchArray[$i]}%' ";
        }
      }
      $sql .= "LIMIT 5";
      // echo $sql;
      $result = $db->query($sql);
    
      if (mysqli_num_rows($result) != 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<div class='mb-3 mt-2 cursor-pointer d-inline-block searchUsers' onclick='profile({$row['id']})'>";
            echo "<img class='rounded-circle profile mr-3 mb-2 mx-auto d-inline-block' src='img/{$row['profile_img']}' style='width:105px; height:105px;' alt='User Image'>";
            echo "<h4 class='text-center d-inline-block ml-4'>{$row['first_name']} {$row['last_name']}</h4>";
          echo "</div><br>";
        }
      } else {
        echo "none";
      }
    } else {
      echo "empty";
    }
  }




?>