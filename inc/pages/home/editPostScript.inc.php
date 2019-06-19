<?php 
  require_once '../../shared/db_connect.php';
  session_start();
  if (isset($_GET['delete'])) {
    $sql = "SELECT post_img, active FROM post WHERE id = {$_GET['delete']}";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    unlink("../../../img/posts/".$row['post_img']);
    if ($row['active'] == 1) {
      $sql = "UPDATE user SET contest_join = 0 WHERE id = {$_SESSION['id']}";
      $result = $db->query($sql);
      $_SESSION['contest'] = 0;
    }
    $sql = "DELETE FROM post WHERE id = {$_GET['delete']}";
    $result = $db->query($sql);
    $currentPage = $_SESSION['currentEditPage'];
    header("location: ../../../".$currentPage);
  } else {
    $postId = $_POST['postId'];
    $sql1 = "SELECT user_id FROM post WHERE id = $postId";
    $result1 = $db->query($sql1);
    $row1 = $result1->fetch_assoc();
    if ($row1['user_id'] == $_SESSION['id']) {
      // add new tags to post_tag
      if (!empty($_POST['tag'])) {
        $tag = $_POST['tag'];
        $sql = "SELECT tag_id FROM post_tag WHERE post_id = $postId";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()){
          $flag = false;
          foreach ($tag as $key => $value) {
            if ($value == $row['tag_id']) {
              $index = array_search($value, $tag);
              if($index !== FALSE){
                  unset($tag[$index]);
              }
              $flag = true;
            }
            if ($flag) {
              break;
            }
          }
          if (!$flag) {
            $sql3 = "DELETE FROM post_tag WHERE tag_id = {$row['tag_id']}";
            $result3 = $db->query($sql3);
          }
        }

        if (count($tag) != 0) {
          foreach ($tag as $key => $value) {
            $sql2 = "INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`) VALUES (NULL, $postId, $value)";
            $result2 = $db->query($sql2);
          }
        }
      } else {
        $sql4 = "DELETE FROM post_tag WHERE post_id = $postId";
        $result4 = $db->query($sql4);
      }

      // change description
      if (!empty($_POST['description'])) {
        $description = $_POST['description'];
        $sql = "UPDATE post SET description = ? WHERE id = $postId";      
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $description);
        $stmt->execute();
        $stmt->close();

      }
      echo "post";
    }
  }
?>