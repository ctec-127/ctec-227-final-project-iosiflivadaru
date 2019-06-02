<div class="container mb-5">
  <div class="row mt-4 mb-5"><!-- First Row Start -->
    <div class="col-md-7"><!-- col-md-12 Start -->
      <div class="accordion" id="accordionExample">
    <?php 
      function get_time_ago( $time )
      {
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
      $sql = "SELECT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id FROM `post` JOIN user ON post.user_id = user.id WHERE 1=1 ORDER BY post.date DESC";
      $result = $db->query($sql);
      while ($row = $result->fetch_assoc()) {
        $postId = $row['post_id'];
        $date = strtotime($row['date']);
        $date = get_time_ago($date);
        echo "<div class='card mb-4 shadow-sm'>";
          echo "<div class='px-3 searchUsers card-header' >";
            echo "<div class='d-flex align-items-center'>";
              echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:55px; height:55px;' alt='User Image'>";
              echo "<div class='d-flex float-left align-items-end flex-column'>";
                echo "<h4 class='d-inline-block mr-auto ml-3 mb-0 cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h4>";
                echo "<p class='d-inline-block mr-auto ml-3 mb-0 cursor-pointer' onclick='profile({$row['id']})'><i class='fas fa-award'></i> Contest - <span class='text-muted'>$date</span></p>";
              echo "</div>";
            echo "</div>";
          echo "</div>";
          echo "<div class='card-body p-3 border-bottom'>";
            echo "<p class='card-text'>{$row['description']}</p>";
          echo "</div>";
          if ($row['post_img'] != "noImg") {
            echo "<img src='img/posts/{$row['post_img']}' class='card-img-top rounded-0' alt='...'>";
          }

          // Likes SQL
          $sqlLike = "SELECT user_id, post_id, user.first_name, user.last_name FROM `like` JOIN user ON user.id = like.user_id WHERE post_id = $postId";
          $resultLike = $db->query($sqlLike);
          $nrLikes = 0;
          $usersLikes = '';
          while ($rowLike = $resultLike->fetch_assoc()) {
            $nrLikes += 1;
            if ($nrLikes <= 10) {
              $usersLikes .= $rowLike['first_name'] . ' ' . $rowLike['last_name'] . '<br>';
            }
          }
          if ($nrLikes > 10) {
            $usersLikes .= $nrLikes - 10 . " more";
          }


          // Comments SQL
          $sqlComment = "SELECT comment.comment,comment.date,user.first_name,user.last_name, user.profile_img, user.id FROM comment JOIN user ON comment.user_id = user.id WHERE comment.post_id = $postId ORDER BY comment.date DESC";
          $resultComment = $db->query($sqlComment);
          $usersComments = '';
          $results = [];
          while ($rowComment = $resultComment->fetch_assoc()) {
            $results[] = $rowComment;
          }
          $nrComments = 0;

          foreach ($results as $rowComment) {
            $nrComments += 1;
            if ($nrComments <= 10) {
              $usersComments .= $rowComment['first_name'] . ' ' . $rowComment['last_name'] . '<br>';              
            }
          }
          if ($nrComments > 10) {
            $usersComments .= $nrComments - 10 . " more";
          }

          // ************CARD BODY***********
          echo "<div class='card-body p-1'>";
            // like button
            echo "<button type='button' class='btn no-outline rounded-0 border-right'><i class='far fa-thumbs-up'></i> <span class='badge badge-secondary p-1' data-toggle='tooltip' data-html='true' title='$usersLikes'>$nrLikes</span></button>";
            // comment button
            echo "<button type='button' class='btn no-outline rounded-0 border-right collapsed' data-toggle='collapse' data-target='#collapse$postId'><span data-toggle='tooltip' data-html='true' title='$usersComments'><i class='far fa-comment'></i> <span class='badge badge-secondary p-1'>$nrComments</span></span></button>";
            // tag button
            echo "<button type='button' class='btn no-outline rounded-0 border-right'><i class='fas fa-hashtag'></i> <span class='badge badge-secondary p-1'>4</span></button>";
            echo "<button type='button' class='btn no-outline rounded-0 border-right'><i class='far fa-star'></i> <span class='badge badge-secondary p-1'>0</span></button>";
            echo "<button type='button' class='btn no-outline rounded-0'><i class='fas fa-star'></i> <span class='badge badge-secondary p-1'>0</span></button>";

            
            echo "</div>"; //ending of CARD BODY

            // comments
            echo "<div id='collapse$postId' class='collapse' data-parent='#accordionExample'>";    
            echo "<div class='card card-body pb-0 rounded-0 border-left-0 border-bottom-0 border-right-0'>";
              // comment input
              echo "<div class='d-flex justify-content-start align-items-center mb-3'>";
                echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$_SESSION['id']})' src='img/{$_SESSION['profileImg']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2'>{$_SESSION['firstName']} {$_SESSION['lastName']}</h6>";      
              echo "</div>";
              echo "<form class='d-flex mb-3'>";
                echo "<input type='text' class='form-control' id='comment' name='comment' placeholder='Leave a Comment...'>";
                echo "<button type='submit' class='btn bg-dark text-link w-auto px-4 ml-3' id='submit'>Submit</button>";
              echo "</form>";
                            
            $commentFlag = true;
            foreach ($results as $row) {    
              $date = strtotime($row['date']);
              $date = get_time_ago($date);  
              if ($commentFlag) {
                // first comment
                echo "<hr class='mt-0'><div class='d-flex justify-content-start align-items-center mb-2'>";
                  echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2'>{$row['first_name']} {$row['last_name']} -</h6>";      
                  echo "<p class='m-0 ml-1 text-muted'>$date</p>";
                echo "</div>";
                echo "<p class='ml-1'>{$row['comment']}</p>";
                $commentFlag = false;
              } else {
                // the rest of the comments
                echo "<hr class='mt-0'>"; 
                echo "<div class='d-flex justify-content-start align-items-center mb-2'>";
                  echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2'>{$row['first_name']} {$row['last_name']} -</h6>";  
                  echo "<p class='m-0 ml-1 text-muted'>$date</p>";
                echo "</div>";
                echo "<p class='ml-1'>{$row['comment']}</p>";  
                    
              }
            }
            echo "</div>";
          echo "</div>"; //ending of comments

        echo "</div>";
      }
    ?>

      </div>
    </div><!-- col-md-12 End -->
  </div><!-- First Row End -->
</div><!-- Container End -->


<script>
  function profile(userId) {
    console.log(userId);
    $(location).attr('href', `profile.php?userId=${userId}`);
  }

  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  // Submit post
  $('#submit').click(function(e){
    e.preventDefault();
    var desc = $("textarea").val()
    var img = $("#postImg").val()
    // console.log("DESC:"+desc+" and IMG:"+img)
    if (desc != '' || img != '') {
      console.log("hi")
      var formData = new FormData($('form')[0]);
      // AJAX request
      $.ajax({
        url: 'inc/pages/home/postCommentScript.inc.php',
        type: 'post',
        data: formData,
        success: function(response){
          console.log(response)
        },
        contentType: false,
        processData: false
      });
    }
  });
</script>