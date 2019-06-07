<?php 
  require_once '../../shared/db_connect.php';
  session_start();

  function get_time_ago($time) {
    $time_difference = time() - $time;

    if( $time_difference < 1 ) { return '1 second ago'; }
    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $time_difference / $secs;

        if( $d >= 1 )
        {
            $t = round( $d );
            return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
  } 
 
  $user = $_SESSION['id'];
  $postId = $_POST['postId'];
  $comment = $_POST['comment'];
  $commentsNr = $_POST['commentsNr'];
  $date = date('Y-m-d H:i:s');
  $dateAgo = get_time_ago(strtotime(date('Y-m-d H:i:s')));

  $sql = "INSERT INTO `comment` (`id`, `user_id`, `post_id`, `comment`, `date`) VALUES (NULL, ?, ?, ?, ?)";

  $stmt = $db->prepare($sql);
  $stmt->bind_param("ssss", $user, $postId, $comment, $date);
  $stmt->execute();
  $stmt->close();

  // echo "<hr class='mt-0'>";
  echo "<div class='d-flex justify-content-start align-items-center mb-3'>";
  echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$_SESSION['id']})' src='img/{$_SESSION['profileImg']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2'>{$_SESSION['firstName']} {$_SESSION['lastName']} - </h6>";      
  echo "<p class='m-0 ml-1 text-muted'>$dateAgo</p>";
  echo "</div>";
  echo "<p class='ml-1'>$comment</p>";
  if ($commentsNr != '0') {
    echo "<hr class='mt-0'>"; 
  }
  
?>