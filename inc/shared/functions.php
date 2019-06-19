<?php 
function echoActiveClassIfRequestMatches($requestUri){
  $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");

  if ($current_file_name == $requestUri) {
    echo 'text-link';  
  } else {
    echo 'text-light';
  }
}

function get_time_ago( $time ) {
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

function posts($db, $currentUser, $page, $par) {
  if (isset($_GET['tag'])) {
    $followFlag = false;
    $sql = "SELECT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id, contest FROM `post` JOIN user ON post.user_id = user.id JOIN post_tag ON post.id = post_tag.post_id WHERE post_tag.tag_id = {$_GET['tag']} ORDER BY post.date DESC";
    $result = $db->query($sql);
    if (!mysqli_num_rows($result)) {
      $sql = "SELECT tag FROM tag WHERE id = {$_GET['tag']}";
      $result = $db->query($sql);
      $row = $result->fetch_assoc();
      echo "<div class='jumbotron py-3 text-center' id='noResult'><h3 class='text-center'>No posts found with <b><i>#{$row['tag']}</i></b>!</h3></div>";
    }
  } else if (isset($_GET['contest'])){
    $followFlag = false;
    $sql = "SELECT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id, contest FROM `post` JOIN user ON post.user_id = user.id WHERE contest = 1 ORDER BY post.date DESC";
    $result = $db->query($sql);
  } else {
    $followingUsers = [];
    $followFlag = true;
    $sql = "SELECT following FROM follow WHERE user_id = {$currentUser}";
    $result = $db->query($sql);
    while($row = $result->fetch_assoc()) {
      if ($followFlag) {
        if ($row['following'] != $currentUser) {
          if (!in_array($row['following'], $followingUsers)){
            $followingUsers[] = $row['following'];
          }
        }
      }
    }
    if ($followFlag) {
      $_SESSION['followingUsers'] = $followingUsers;
    }
    $followFlag = false;

    if ($page == 0) {
      $homeFeed = "follow.user_id = {$currentUser} OR";
    } else if ($page == 1) {
      $homeFeed = '';
    } else if ($page == 2) {
      $homeFeed = 'post.contest = 1 AND';
    }
    $sql = "SELECT DISTINCT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id, contest, follow.following FROM `post` LEFT JOIN user ON post.user_id = user.id LEFT JOIN follow ON follow.following = post.user_id WHERE $homeFeed post.user_id = {$currentUser} ORDER BY post.date DESC";
    $result = $db->query($sql);
    // echo $sql;
  }
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
            echo "<p class='d-inline-block mr-auto ml-3 mb-0'>";
            if ($row['contest'] == 1) {
              echo "<a class='cursor-pointer text-dark' href='home.php?contest'><i class='fas fa-award'></i> Contest - </a>";
            }
            echo "<span class='text-muted'>$date</span></p>";
            echo "</div>";
            if ($_SESSION['id'] == $row['id']) {
              echo "<p class='d-inline-block ml-auto ml-3 mb-0 cursor-pointer editPost' data-toggle='modal' data-target='#exampleModalLong' data-postId='$postId'><i class='fas fa-ellipsis-v'></i></p>";
            }
        echo "</div>";
      echo "</div>";
      if (!empty($row['description'])) {
        echo "<div class='card-body p-3 border-bottom'>";
          echo "<p class='card-text'>{$row['description']}</p>";
        echo "</div>";
      }
      if ($row['post_img'] != "noImg") {
        echo "<img src='img/posts/{$row['post_img']}' class='card-img-top rounded-0' alt='...'>";
      }

      // Likes SQL
      $sqlLike = "SELECT user_id, post_id, user.first_name, user.last_name, user.profile_img, user.id FROM `like` JOIN user ON user.id = like.user_id WHERE post_id = $postId";
      $resultLike = $db->query($sqlLike);
      $nrLikes = 0;
      $usersLikes = '';
      $liked = false;
      $resultsLike = [];
      $displayUserLike = '';
      while ($rowLike = $resultLike->fetch_assoc()) {
        $resultsLike[] = $rowLike;
        $nrLikes += 1;
        if ($rowLike['user_id'] == $currentUser) {
          $liked = true;
        }
        if ($nrLikes <= 10) {
          $usersLikes .= $rowLike['first_name'] . ' ' . $rowLike['last_name'] . '<br>';
        }
      }
      if ($nrLikes > 10) {
        $usersLikes .= $nrLikes - 10 . " more";
      }

      // Contest SQL      
      $sqlContest = "SELECT contest.user_id, post_id, user.first_name, user.last_name, user.profile_img, user.id, post.active, post.contest FROM `contest` JOIN user ON user.id = contest.user_id JOIN post ON contest.post_id = post.id WHERE post_id = $postId";
      $resultContest = $db->query($sqlContest);

      $nrUpvote = 0;
      $usersUpvote = '';
      $upvoted = false;
      $resultsContest = [];

      if (mysqli_num_rows($resultContest) != 0) {
        while ($rowContest = $resultContest->fetch_assoc()) {

          $resultsContest[] = $rowContest;
          $nrUpvote += 1;
          if ($rowContest['user_id'] == $_SESSION['id']) {
            $upvoted = true;
          }
          if ($nrUpvote <= 10) {
            $usersUpvote .= $rowContest['first_name'] . ' ' . $rowContest['last_name'] . '<br>';
          }     
        }
        if ($nrUpvote > 10) {
          $usersUpvote .= $nrUpvote - 10 . " more";
        }
      }

      $sqlPostContest = "SELECT contest, active FROM post WHERE id = $postId";
      $resultPostContest = $db->query($sqlPostContest);
      $hasContest = 0;
      $activeContest = 0;
      while ($rowPostContest = $resultPostContest->fetch_assoc()) {
        if ($rowPostContest['contest'] == 1) {
          $hasContest = 1;
        }
        if ($rowPostContest['active'] == 1) {
          $activeContest = 1;
        }
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
      
      // don't repeat the same user in the tooltip for comments
      $userId = [0];
      $flag = false;
      foreach ($results as $rowComment) {
        $nrComments += 1;
        if ($nrComments <= 10) {
          for ($i=0; $i < count($userId); $i++) { 
            if ($userId[$i] == $rowComment['id']) {
              $flag = true;
            } else {
              $flag = false;
            }
          }      
        }    

        if ($flag != true) {
          $usersComments .= $rowComment['first_name'] . ' ' . $rowComment['last_name'] . '<br>';    
        } else {
          $usersComments .= ' ';
        }
        
        array_push($userId, $rowComment['id']);
      }
      if ($nrComments > 10) {
        $usersComments .= $nrComments - 10 . " more";
      }

      // Tags
      $sqlTag = "SELECT tag, tag.id FROM tag JOIN post_tag ON post_tag.tag_id = tag.id WHERE post_id = $postId";
      $resultTag = $db->query($sqlTag);
      $tags = false;
      $nrTags = 0;
      $tagNames = "";
      $displayTags = '';
      while ($rowTag = $resultTag->fetch_assoc()) {
        $nrTags += 1;
        if ($nrTags <= 5) {
          $tagNames .= "<i class=\"\">#" . $rowTag['tag'] . "</i><br>";
        }
        $displayTags .= "<a href='home.php?tag={$rowTag['id']}'><span class='text-link bg-dark px-1 rounded font-weight-normal d-inline-block ml-1 my-1 cursor-pointer'>#{$rowTag['tag']}</span></a>";
      }
      if ($nrTags > 5) {
        $tagNames .= $nrTags - 5 . " more";
      }

      $resultsTag = [];
      while ($rowTag = $resultComment->fetch_assoc()) {
        $resultsTag[] = $rowComment;
      }

      if ($par == 1) {
        $copy = 'a';
      } else {
        $copy = '';
      }

      // ************CARD BODY***********
      echo "<div class='card-body p-1'>";
        // like button
        echo "<button type='button' class='btn no-outline rounded-0 border-right'>";
        if ($liked == true) {
          // echo "<span data-toggle='collapse' data-target='#collapseLike$postId'>";
          echo "<i class='fas fa-thumbs-up' id='dislike$postId$copy' onclick='dislike($postId,\"$copy\")'></i> ";
        } else {
          // echo "<span data-toggle='collapse' data-target='#collapseLike$postId$copy'>";
          echo "<i class='far fa-thumbs-up' id='like$postId$copy' onclick='like($postId,\"$copy\")'></i> ";
        }
        echo "<span class='badge badge-secondary p-1' data-toggle='collapse' data-target='#collapseLike$postId$copy' data-toggleTool='tooltip' data-html='true' title='$usersLikes'>$nrLikes</span></button>";
        // comment button
        echo "<button class='btn no-outline rounded-0 border-right collapsed' data-toggle='collapse' data-target='#collapse$postId$copy'><span data-toggleTool='tooltip' data-html='true' title='$usersComments'><i class='far fa-comment'></i> <span class='badge badge-secondary p-1'>$nrComments</span></span></button>";
        // tag button
        echo "<button class='btn no-outline rounded-0 border-right collapsed' data-toggle='collapse' data-target='#collapseTag$postId$copy'><i class='fas fa-hashtag'></i> <span class='badge badge-secondary p-1' data-toggleTool='tooltip' data-html='true' title='$tagNames'>$nrTags</span></button>";
        // contest button
        
        if ($hasContest == 1) {
          echo "<button type='button' class='btn no-outline rounded-0 border-right'>";
          if ($activeContest) {
            if ($upvoted == true) {
              echo "<i class='fas fa-star' id='downvote$postId$copy' onclick='downvote($postId,\"$copy\")'></i> ";
            } else {
              echo "<i class='far fa-star' id='upvote$postId$copy' onclick='upvote($postId,\"$copy\")'></i> ";
            }
          } else {
            echo "<i class='fas fa-star'></i> ";
          }
          echo "<span class='badge badge-secondary p-1' data-toggle='collapse' data-target='#collapseContest$postId$copy' data-toggleTool='tooltip' data-html='true' title='$usersUpvote'>$nrUpvote</span></button>";
        }
        
        
        echo "</div>"; //ending of CARD BODY

        // accordion for each post so I can switch from likes to comments to tags
        echo "<div class='accordion' id='accordionExample$postId$copy'>";
        
          // contest
          echo "<div id='collapseContest$postId$copy' class='collapse multi-collapse' data-parent='#accordionExample$postId$copy'>"; 
          echo "<div class='pb-3 px-3 border-top' id='displayContest$postId$copy'>";
            foreach ($resultsContest as $row) {
              echo "<div class='d-flex justify-content-start align-items-center mt-3' data-userContest='{$row['first_name']} {$row['last_name']}'>";
                echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h6>";  
              echo "</div>";
            }
          echo "</div>";
        echo "</div>"; // ending of contest

          // tags
          echo "<div id='collapseTag$postId$copy' class='collapse multi-collapse' data-parent='#accordionExample$postId$copy'>"; 
            echo "<div class='py-3 px-3 border-top'>$displayTags</div>";
          echo "</div>"; // ending of tags

          // comments
          echo "<div id='collapse$postId$copy' class='collapse multi-collapse' data-parent='#accordionExample$postId$copy'>";    
          echo "<div class='card card-body pb-0 rounded-0 border-left-0 border-bottom-0 border-right-0'>";
            // comment input
            echo "<div class='d-flex justify-content-start align-items-center mb-3'>";
              echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$currentUser})' src='img/{$_SESSION['profileImg']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2'>{$_SESSION['firstName']} {$_SESSION['lastName']}</h6>";      
            echo "</div>";
            echo "<div class='d-flex mb-3' id='commentInput$postId$copy'>";
              echo "<input type='text' class='form-control comment$postId'  name='comment' maxlength='500' placeholder='Leave a Comment...'>";
              echo "<button class='btn bg-dark text-link w-auto px-4 ml-3 submit' data-postId='$postId$copy'>Submit</button>";
            echo "</div>";
                          
          $commentFlag = true;
          foreach ($results as $row) {    
            $date = strtotime($row['date']);
            $date = get_time_ago($date);  
            if ($commentFlag) {
              // first comment
              echo "<div class='d-flex justify-content-start align-items-center mb-2'>";
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

        // likes
        echo "<div id='collapseLike$postId$copy' class='collapse multi-collapse' data-parent='#accordionExample$postId$copy'>"; 
          echo "<div class='pb-3 px-3 border-top' id='displayLikes$postId$copy'>";
            foreach ($resultsLike as $row) {
              echo "<div class='d-flex justify-content-start align-items-center mt-3' data-userLike='{$row['first_name']} {$row['last_name']}'>";
                echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h6>";  
              echo "</div>";
            }
          echo "</div>";
        echo "</div>"; // ending of likes

      echo "</div>"; // accordion
    echo "</div>";
  } // ending while loop
  if (mysqli_num_rows($result) == 0) {
    echo "<div class='jumbotron py-3' id='noResult'>
    <h5 class='text-center m-0'>Join the contest, Follow people and Share your thoughts!!</h5>
    </div>";
  }
}

function sidePost($db) { ?>
  <div class='card mb-4 shadow-sm'>
    <div class='px-3 card-header w-100 text-center' >
      <h4 class="text-center m-0 d-inline-block">Current Contest Ranks <sup><i class="fas fa-sm fa-info-circle d-inline-block cursor-pointer" data-toggle="modal" data-target="#contestInfo"></i></sup></h4>
    </div>
    <div class='card-body p-3'>
      <?php 
        $sql = "SELECT COUNT(*) AS `num`, user.profile_img, user.id FROM contest JOIN post ON post.id = contest.post_id JOIN user on user.id = post.user_id WHERE active = 1 GROUP BY post_id ORDER BY num DESC LIMIT 3";
        $result = $db->query($sql);
        $first = true;
        $second = false;
        $third = false;
        while($row = $result->fetch_assoc()) {
          if ($first) {
            echo "<div class='d-flex align-items-center flex-column'>";
              echo "<div class=''>";
                echo "<h2 class='mr-2 mb-0 gold float-left'>1<sup>st</sup></h2>";
                echo "<img class='rounded-circle profile d-inline cursor-pointer gold-border' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:80px; height:80px;' alt='User Image'><br>";  
                echo "<p class='mb-0'><i class='fas fa-star gold mr-2'></i><strong>{$row['num']}</strong></p>";
              echo "</div>";
            echo "</div>";
            $first = false;
            $second = true;
          } else if ($second) {
            echo "<hr>";
            echo "<div class='d-flex'>";
            echo "<div class='d-inline-block mx-auto mt-3'>";
              echo "<h2 class='mr-2 silver d-inline'>2<sup>nd</sup></h2>";
              echo "<img class='rounded-circle profile d-inline cursor-pointer silver-border' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:60px; height:60px;' alt='User Image'><br>";  
              echo "<p class='mb-0'><i class='fas fa-star silver mr-2'></i><strong>{$row['num']}</strong></p>";
            echo "</div>";
            $second = false;
            $third = true;
          } else if ($third) {
            echo "<div class='vr d-inline'></div>";
            echo "<div class='d-inline-block mx-auto mt-3'>";
              echo "<h2 class='mr-2 bronze d-inline'>3<sup>rd</sup></h2>";
              echo "<img class='rounded-circle profile d-inline cursor-pointer bronze-border' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:60px; height:60px;' alt='User Image'><br>";  
              echo "<p class='mb-0'><i class='fas fa-star bronze mr-2'></i><strong>{$row['num']}</strong></p>";
            echo "</div>";           
            echo "</div>";   
            $third = false;        
          }
        }
        if ($third) {
          echo "</div>";
        }
        if (mysqli_num_rows($result) == 0) {
          echo "<p class='m-0 text-center'><i>Post on the contest to become Rank 1!! :O</i></p>";
        }
      ?>
    </div>
    <div class="card-footer text-muted text-center">
      <?php 
        $sql = "SELECT contest_date FROM user WHERE id = 1";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $contestDate = $row['contest_date'];
        //A: RECORDS TODAY'S Date And Time
        $today = time();

        //B: RECORDS Date And Time OF YOUR EVENT
        $event = strtotime($row['contest_date']);

        //C: COMPUTES THE DAYS UNTIL THE EVENT.
        $countdown = round(($event - $today)/86400);

        // execute if statement after the days left end
        if ($countdown < 0) {
          // Reward top 3 users with contest tokens
          $sql = "SELECT COUNT(*) AS `num`, user.id FROM contest JOIN post ON post.id = contest.post_id JOIN user on user.id = post.user_id WHERE active = 1 GROUP BY post_id ORDER BY num DESC LIMIT 3";
          $result = $db->query($sql);
          $ranks = [];
          while ($row = $result->fetch_assoc()) {
            $ranks[] = $row['id'];
          }
          $rank1 = true;
          foreach ($ranks as $id) {
            if ($rank1) {
              $sql = "UPDATE user SET contest_tokens = contest_tokens + 20 WHERE id = $id";
              $result = $db->query($sql);
              $rank1 = false;
              $rank2 = true;
            } else if ($rank2) {
              $sql = "UPDATE user SET contest_tokens = contest_tokens + 10 WHERE id = $id";
              $result = $db->query($sql);
              $rank2 = false;
              $rank3 = true;
            } else if ($rank3) {
              $sql = "UPDATE user SET contest_tokens = contest_tokens + 5 WHERE id = $id";
              $result = $db->query($sql);
              $rank3 = false;
            }
          }

          $sql = 'UPDATE user SET contest_date = ADDDATE("'.$contestDate.'", INTERVAL 7 DAY) WHERE id = 1';
          $result = $db->query($sql);

          $sql = "SELECT contest_date FROM user WHERE id = 1";
          $result = $db->query($sql);
          $row = $result->fetch_assoc();
          $contestDate = $row['contest_date'];
          //A: RECORDS TODAY'S Date And Time
          $today = time();
  
          //B: RECORDS Date And Time OF YOUR EVENT
          $event = strtotime($row['contest_date']);
  
          //C: COMPUTES THE DAYS UNTIL THE EVENT.
          $countdown = round(($event - $today)/86400);
          
          // Reset users that joined the contest
          $sql = "UPDATE user SET contest_join = 0 WHERE contest_join = 1";
          $result = $db->query($sql);

          // Set active contest posts to 0
          $sql = "UPDATE post SET active = 0 WHERE active = 1";
          $result = $db->query($sql);

          $_SESSION['contest'] = 0;
        }
        

        //D: DISPLAYS COUNTDOWN UNTIL EVENT
        echo "$countdown days left";
      ?>
    </div>
  </div> <!-- Ending Contest Ranks -->

  <div class='card mb-4 shadow-sm'>
    <div class='px-3 card-header' >
      <h4 class="text-center m-0">People you may know:</h4>
    </div>
    <div class='card-body px-3 py-0 border-bottom'>
    <?php 
      if (!empty($_SESSION['followingUsers'])) {
        $sqlString = '';
        $firstFlag = true;
        if (count($_SESSION['followingUsers']) > 3) {
          $loopCount = 3;
        } else {
          $loopCount = count($_SESSION['followingUsers']);
        }
        for ($i=0; $i < $loopCount; $i++) { 
          if (!$firstFlag) {
            $randomUser1 = rand(0, count($_SESSION['followingUsers']) - 1);
            $sqlString .= " OR follow.user_id = {$_SESSION['followingUsers'][$randomUser1]} AND follow.following NOT IN (SELECT DISTINCT following FROM follow WHERE follow.user_id = {$_SESSION['id']} OR follow.following = {$_SESSION['id']})";
          }
          if ($firstFlag) {
            $randomUser1 = rand(0, count($_SESSION['followingUsers']) - 1);
            $sqlString .= "follow.user_id = {$_SESSION['followingUsers'][$randomUser1]} AND follow.following NOT IN (SELECT DISTINCT following FROM follow WHERE follow.user_id = {$_SESSION['id']} OR follow.following = {$_SESSION['id']})";
            $firstFlag = false;
          }
        }
        $sql = "SELECT DISTINCT user.first_name, user.last_name, user.profile_img, user.id, follow.following 
                FROM user 
                JOIN follow 
                ON follow.following = user.id 
                WHERE $sqlString ORDER BY RAND() LIMIT 2";
        // echo $sql;
        // echo "<br><br><br>";
        // print_r($_SESSION['followingUsers']);
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<div class='d-flex align-items-center my-3'>";
            echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:55px; height:55px;' alt='User Image'>";
            echo "<div class='d-flex float-left align-items-end flex-column'>";
              echo "<h5 class='d-inline-block mr-auto ml-3 mb-0 cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h5>";
              echo "</div>";
          echo "</div>";
        }
        if (mysqli_num_rows($result) == 0) {
          echo "<p class='my-3'><i>In order to get recommendations you need to follow people! :)</i></p>";
        }
      } else {
          echo "<p class='my-3'><i>In order to get recommendations you need to follow people! :)</i></p>";
      }
    ?>
    </div>
  </div><!-- Ending People you may know -->
  
  <div class='card mb-4 shadow-sm'>
    <div class='px-3 card-header' >
      <h4 class="text-center m-0">Feedback:</h4>
    </div>
    <div class='card-body px-3 py-0 border-bottom'>
       <p class="my-3">If you find any bugs please send me details about the bug and a screenshot at iosiflivadaru@yahoo.com. Thank you for using LivIt! :)</p>
    </div>
  </div><!-- Ending Feedback-->
  
  <p>&copy; 2019. Created by Iosif Livadaru</p>
<?php
}

function editModal() { ?>
  <!-- Modal -->
  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Edit Post</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ...
        </div>
      </div>
    </div>
  </div>
<?php
}

function contestInfo() {?>
  <!-- Modal -->
  <div class="modal fade" id="contestInfo" tabindex="-1" role="dialog" aria-labelledby="contestInfoTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="contestInfoTitle">Contest Info</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <ul>
            <li>You can ONLY join the contest with an image</li>
            <li>Contest lasts 7 days</li>
            <li>The rank system is based on the amount of stars you get on your post</li>
            <li>You can ONLY <strong>star</strong> current contest posts</li>
          </ul>
            <hr>
          <ul>
            <li>Rank 1: <img src='icons/contestToken.png' class='token' alt='Contest Token'><strong>20</strong></li>
            <li>Rank 2: <img src='icons/contestToken.png' class='token' alt='Contest Token'><strong>10</strong></li>
            <li>Rank 3: <img src='icons/contestToken.png' class='token' alt='Contest Token'><strong>5</strong></li>
          </ul>
            <hr>
          <ul>
            <li>Contest tokens can be used in the market</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?php
}
?>