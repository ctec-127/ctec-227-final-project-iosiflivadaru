<div class="container my-5 pt-3" data-currentUser="<?= $_SESSION['firstName'].' '.$_SESSION['lastName'] ?>" data-userId="<?=$_SESSION['id']?>" data-profileImg="<?=$_SESSION['profileImg']?>" >
  <div class="row pt-4"><!-- First Row Start -->
    <div class="col-lg-8"><!-- col-lg-7 Start -->
    <?php 
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
      if (isset($_GET['tag'])) {
        $sql = "SELECT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id, contest FROM `post` JOIN user ON post.user_id = user.id JOIN post_tag ON post.id = post_tag.post_id WHERE post_tag.tag_id = {$_GET['tag']} ORDER BY post.date DESC";
        $result = $db->query($sql);
        if (!mysqli_num_rows($result)) {
          $sql = "SELECT tag FROM tag WHERE id = {$_GET['tag']}";
          $result = $db->query($sql);
          $row = $result->fetch_assoc();
          echo "<div class='jumbotron py-3 text-center' id='noResult'><h3 class='text-center'>No posts found with <b><i>#{$row['tag']}</i></b>!</h3></div>";
        }
      } else if (isset($_GET['contest'])){
        $sql = "SELECT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id, contest FROM `post` JOIN user ON post.user_id = user.id WHERE contest = 1 ORDER BY post.date DESC";
        $result = $db->query($sql);
      } else {
        $sql = "SELECT description, post_img, date, user.first_name, user.last_name, user.profile_img, user.id, post.id AS post_id, contest FROM `post` JOIN user ON post.user_id = user.id WHERE 1=1 ORDER BY post.date DESC";
        $result = $db->query($sql);
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
            if ($rowLike['user_id'] == $_SESSION['id']) {
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
          if ($row['contest'] == 1) {
            $activeContest = true;
            $sqlContest = "SELECT user_id, post_id, user.first_name, user.last_name, user.profile_img, user.id FROM `contest` JOIN user ON user.id = contest.user_id WHERE post_id = $postId";
            $resultContest = $db->query($sqlContest);
            $nrUpvote = 0;
            $usersUpvote = '';
            $upvoted = false;
            $resultsContest = [];
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
          } else {
            $activeContest = false;
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
            $displayTags .= "<a href='home.php?tag={$rowTag['id']}'><span class='text-link bg-dark px-1 rounded font-weight-normal d-inline-block ml-1 mb-1 cursor-pointer'>#{$rowTag['tag']}</span></a>";
          }
          if ($nrTags > 5) {
            $tagNames .= $nrTags - 5 . " more";
          }

          $resultsTag = [];
          while ($rowTag = $resultComment->fetch_assoc()) {
            $resultsTag[] = $rowComment;
          }

          // ************CARD BODY***********
          echo "<div class='card-body p-1'>";
            // like button
            echo "<button type='button' class='btn no-outline rounded-0 border-right'>";
            if ($liked == true) {
              // echo "<span data-toggle='collapse' data-target='#collapseLike$postId'>";
              echo "<i class='fas fa-thumbs-up' id='dislike$postId' onclick='dislike($postId)'></i> ";
            } else {
              // echo "<span data-toggle='collapse' data-target='#collapseLike$postId'>";
              echo "<i class='far fa-thumbs-up' id='like$postId' onclick='like($postId)'></i> ";
            }
            echo "<span class='badge badge-secondary p-1' data-toggle='collapse' data-target='#collapseLike$postId' data-toggleTool='tooltip' data-html='true' title='$usersLikes'>$nrLikes</span></button>";
            // comment button
            echo "<button class='btn no-outline rounded-0 border-right collapsed' data-toggle='collapse' data-target='#collapse$postId'><span data-toggleTool='tooltip' data-html='true' title='$usersComments'><i class='far fa-comment'></i> <span class='badge badge-secondary p-1'>$nrComments</span></span></button>";
            // tag button
            echo "<button class='btn no-outline rounded-0 border-right collapsed' data-toggle='collapse' data-target='#collapseTag$postId'><i class='fas fa-hashtag'></i> <span class='badge badge-secondary p-1' data-toggleTool='tooltip' data-html='true' title='$tagNames'>$nrTags</span></button>";
            // contest button
            if ($activeContest) {
              echo "<button type='button' class='btn no-outline rounded-0 border-right'>";
              if ($upvoted == true) {
                echo "<i class='fas fa-star' id='downvote$postId' onclick='downvote($postId)'></i> ";
              } else {
                echo "<i class='far fa-star' id='upvote$postId' onclick='upvote($postId)'></i> ";
              }
              echo "<span class='badge badge-secondary p-1' data-toggle='collapse' data-target='#collapseContest$postId' data-toggleTool='tooltip' data-html='true' title='$usersUpvote'>$nrUpvote</span></button>";
            }
            
            echo "</div>"; //ending of CARD BODY

            // accordion for each post so I can switch from likes to comments to tags
            echo "<div class='accordion' id='accordionExample$postId'>";
            
              // contest
              echo "<div id='collapseContest$postId' class='collapse multi-collapse' data-parent='#accordionExample$postId'>"; 
              echo "<div class='pb-3 px-3 border-top' id='displayContest$postId'>";
                foreach ($resultsContest as $row) {
                  echo "<div class='d-flex justify-content-start align-items-center mt-3' data-userContest='{$row['first_name']} {$row['last_name']}'>";
                    echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h6>";  
                  echo "</div>";
                }
              echo "</div>";
            echo "</div>"; // ending of contest

              // tags
              echo "<div id='collapseTag$postId' class='collapse multi-collapse' data-parent='#accordionExample$postId'>"; 
                echo "<div class='py-3 px-3 border-top'>$displayTags</div>";
              echo "</div>"; // ending of tags

              // comments
              echo "<div id='collapse$postId' class='collapse multi-collapse' data-parent='#accordionExample$postId'>";    
              echo "<div class='card card-body pb-0 rounded-0 border-left-0 border-bottom-0 border-right-0'>";
                // comment input
                echo "<div class='d-flex justify-content-start align-items-center mb-3'>";
                  echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$_SESSION['id']})' src='img/{$_SESSION['profileImg']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2'>{$_SESSION['firstName']} {$_SESSION['lastName']}</h6>";      
                echo "</div>";
                echo "<div class='d-flex mb-3' id='commentInput$postId'>";
                  echo "<input type='text' class='form-control comment$postId'  name='comment' placeholder='Leave a Comment...'>";
                  echo "<button class='btn bg-dark text-link w-auto px-4 ml-3 submit' data-postId='$postId'>Submit</button>";
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
            echo "<div id='collapseLike$postId' class='collapse multi-collapse' data-parent='#accordionExample$postId'>"; 
              echo "<div class='pb-3 px-3 border-top' id='displayLikes$postId'>";
                foreach ($resultsLike as $row) {
                  echo "<div class='d-flex justify-content-start align-items-center mt-3' data-userLike='{$row['first_name']} {$row['last_name']}'>";
                    echo "<img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:35px; height:35px;' alt='User Image'>" . "<h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile({$row['id']})'>{$row['first_name']} {$row['last_name']}</h6>";  
                  echo "</div>";
                }
              echo "</div>";
            echo "</div>"; // ending of likes

          echo "</div>"; // accordion
        echo "</div>";
      }
    ?>

    </div><!-- col-lg-7 End -->

    <div class="col-lg-4 d-none d-lg-block"><!-- col-lg-5 Start -->
      <div class='card mb-4 shadow-sm'>
        <div class='px-3 searchUsers card-header' >
          <h3 class="text-center">Current Contest Ranks:</h3>
        </div>
        <div class='card-body p-3 border-bottom'>
          <?php 
            $sql = "SELECT COUNT(*) AS `num`, user.first_name, user.last_name, user.profile_img, user.id FROM contest JOIN post ON post.id = contest.post_id JOIN user on user.id = post.user_id GROUP BY post_id ORDER BY num DESC LIMIT 3";
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
                echo "<hr>";
                $first = false;
                $second = true;
              } else if ($second) {
                echo "<div class='d-flex'>";
                echo "<div class='d-inline-block mr-auto mt-3'>";
                  echo "<h2 class='mr-2 silver d-inline'>2<sup>nd</sup></h2>";
                  echo "<img class='rounded-circle profile d-inline cursor-pointer silver-border' onclick='profile({$row['id']})' src='img/{$row['profile_img']}' style='width:60px; height:60px;' alt='User Image'><br>";  
                  echo "<p class='mb-0'><i class='fas fa-star silver mr-2'></i><strong>{$row['num']}</strong></p>";
                echo "</div>";
                $second = false;
                $third = true;
              } else if ($third) {
                echo "<div class='vr d-inline'></div>";
                echo "<div class='d-inline-block ml-auto mt-3'>";
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
          ?>
        </div>
      </div>
        <h3 class="text-center">People you may know:</h3>
    </div><!-- col-lg-4 End -->
  </div><!-- First Row End -->
</div><!-- Container End -->

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

<script>
  function profile(userId) {
    console.log(userId);
    $(location).attr('href', `profile.php?userId=${userId}`);
  }

  $(function () {
    $('[data-toggleTool="tooltip"]').tooltip()
  })

  // Submit Comment
  $(".form-control").keyup(function(event) {
    if (event.keyCode === 13) {
        $(".submit").click();
    }
  });

  $('.submit').click(function(){
    var postId = $(this).attr("data-postId")
    var comment = $(".comment"+postId).val()
    var commentsNr = $(`button[data-target='#collapse${postId}']`).find('span.badge').html()
    
    if (comment != '' && comment.replace(/\s/g, '').length) {
      // AJAX request
      $.ajax({
        url: 'inc/pages/home/postCommentScript.inc.php',
        type: 'post',
        data: {
          comment: comment,
          commentsNr: commentsNr,
          postId: postId
          },
        success: function(response){
          // console.log(response)
          var newNr = parseInt(commentsNr) + 1
          $(`button[data-target='#collapse${postId}']`).find('span.badge').html(newNr)
          $(".comment"+postId).val('')
          $("#collapse"+postId).children('.card-body').children("#commentInput"+postId).after(response)
        }
      });
    }
  });

  // Like
  function like(postId) {
    var postId = postId
    var like = "like"
    var likesNr = $("#like"+postId).next().html()

    // AJAX request
    $.ajax({
      url: 'inc/pages/home/like&dislikeScript.inc.php',
      type: 'post',
      data: {
        like: like,
        postId: postId
        },
      success: function(response){
        var newNr = parseInt(likesNr) + 1
        $("#like"+postId).next().html(newNr)

        var profileImg = $(".container").attr("data-profileImg")
        var userId = $(".container").attr("data-userId")

        var userName = $(".container").attr("data-currentUser")

        var oldTooltip = $("#like"+postId).next().attr('data-original-title')
        var currentUser = $(".container").attr("data-currentUser") + "<br>"
        var res = currentUser + oldTooltip

        $("#like"+postId).next().attr('data-original-title', `${res}`)
        $("#like"+postId).after(`<i class='fas fa-thumbs-up' id='dislike${postId}' onclick='dislike(${postId})'></i>`)
        $("#like"+postId).remove()
        $("#displayLikes"+postId).append(`<div class='d-flex justify-content-start align-items-center mt-3' data-userLike='${userName}'><img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile(${userId})' src='img/${profileImg}' style='width:35px; height:35px;' alt='User Image'><h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile(${userId})'>${userName}</h6></div>`)
      }
    });
  }
  // Dislike
  function dislike(postId) {
    var postId = postId
    var dislike = "dislike"
    var likesNr = $("#dislike"+postId).next().html()

    // AJAX request
    $.ajax({
      url: 'inc/pages/home/like&dislikeScript.inc.php',
      type: 'post',
      data: {
        dislike: dislike,
        postId: postId
        },
      success: function(response){
        var newNr = parseInt(likesNr) - 1
        $("#dislike"+postId).next().html(newNr)

        var userName = $(".container").attr("data-currentUser")
        var oldTooltip = $("#dislike"+postId).next().attr('data-original-title')
        var currentUser = userName + "<br>"
        var res = oldTooltip.replace(currentUser, '')
        
        $("#dislike"+postId).next().attr('data-original-title', `${res}`)
        $("#dislike"+postId).after(`<i class='far fa-thumbs-up' id='like${postId}' onclick='like(${postId})'></i>`)
        $("#dislike"+postId).remove()
        $(`div[data-userLike="${userName}"]`).remove()
      }
    });
  }

  // Upvote
  function upvote(postId) {
    var postId = postId
    var upvote = "upvote"
    var upvotesNr = $("#upvote"+postId).next().html()

    // AJAX request
    $.ajax({
      url: 'inc/pages/home/contestScript.inc.php',
      type: 'post',
      data: {
        upvote: upvote,
        postId: postId
        },
      success: function(response){
        var newNr = parseInt(upvotesNr) + 1
        $("#upvote"+postId).next().html(newNr)

        var profileImg = $(".container").attr("data-profileImg")
        var userId = $(".container").attr("data-userId")

        var oldTooltip = $("#upvote"+postId).next().attr('data-original-title')
        var userName = $(".container").attr("data-currentUser")
        var currentUser = userName + "<br>"
        var res = currentUser + oldTooltip

        $("#upvote"+postId).next().attr('data-original-title', `${res}`)
        $("#upvote"+postId).after(`<i class='fas fa-star' id='downvote${postId}' onclick='downvote(${postId})'></i>`)
        $("#upvote"+postId).remove()
        $("#displayContest"+postId).append(`<div class='d-flex justify-content-start align-items-center mt-3' data-userContest='${userName}'><img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile(${userId})' src='img/${profileImg}' style='width:35px; height:35px;' alt='User Image'><h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile(${userId})'>${userName}</h6></div>`)
      }
    });
  }
  // Downvote
  function downvote(postId) {
    var postId = postId
    var downvote = "downvote"
    var downvoteNr = $("#downvote"+postId).next().html()

    // AJAX request
    $.ajax({
      url: 'inc/pages/home/contestSCript.inc.php',
      type: 'post',
      data: {
        downvote: downvote,
        postId: postId
        },
      success: function(response){
        var newNr = parseInt(downvoteNr) - 1
        $("#downvote"+postId).next().html(newNr)

        var oldTooltip = $("#downvote"+postId).next().attr('data-original-title')
        var userName = $(".container").attr("data-currentUser")
        var currentUser = userName + "<br>"
        var res = oldTooltip.replace(currentUser, '')
        
        $("#downvote"+postId).next().attr('data-original-title', `${res}`)
        $("#downvote"+postId).after(`<i class='far fa-star' id='upvote${postId}' onclick='upvote(${postId})'></i>`)
        $("#downvote"+postId).remove()
        $(`div[data-userContest="${userName}"]`).remove()
      }
    });
  }

  // Edit Modal
  $('.editPost').click(function(){
    var postId = $(this).attr('data-postId');
    console.log('hi')
    // AJAX request
    $.ajax({
      url: 'inc/pages/home/editModalScript.inc.php',
      type: 'post',
      data: {postId: postId},
      success: function(response){
        // Add response in Modal body
        $('.modal-body').html(response);
      }
    });
  });
</script>