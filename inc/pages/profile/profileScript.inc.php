<?php 
  if (isset($_GET['userId'])) {
    $searchUser = $_GET['userId'];
    $sql = "SELECT * FROM user WHERE id = $searchUser";
  } else {
    $sql = "SELECT * FROM user WHERE id = {$_SESSION['id']} AND first_name = '{$_SESSION['firstName']}'";
  }
  $result = $db->query($sql);
  $row = $result->fetch_assoc();
  $currentPage = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
  $_SESSION['currentEditPage'] = $currentPage;
?>

<div class="container my-5 pt-3" data-currentUser="<?= $_SESSION['firstName'].' '.$_SESSION['lastName'] ?>" data-userId="<?=$_SESSION['id']?>" data-profileImg="<?=$_SESSION['profileImg']?>">
  <?php 
    if (isset($_GET['update'])) {
      echo "<div class='alert alert-success mt-3 w-auto text-center alertNewImage updateAlert' role='alert' >Account successfully updated!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
    }
  ?>
  <div class="row pt-3"><!-- First Row Start -->

    <div class="col-md-6"><!-- col-md-6 Start -->

      <div class="d-flex align-items-center flex-column bd-highlight">
        <div class="p-2 bd-highlight">
          <img class='rounded-circle profile' src='img/<?= $row['profile_img']; ?>' alt='User Image' id='profileImgReg'>
        </div>

        <div class="bd-highlight">
          <h4 class="m-2"><?= $row['first_name']." ".$row['last_name'];?></h4>
        </div>
        
        <div class="p-2-5 bd-highlight">
          <?php 
            // get number of followers
            $sql = "SELECT * FROM follow WHERE following = {$row['id']}";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) > 0) {
              $followers = 0;
              while ($followRow = $result->fetch_assoc()) {
                $followers += 1;
              }
              if ($followers == 1) {
                if (isset($_GET['userId'])) {
                  echo "<p class='text-white d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='followers' data-user='{$_GET['userId']}' data-toggle='modal' data-target='#exampleModalLong'>$followers&nbsp;Follower</p> ";
                } else {
                  echo "<p class='text-white d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='followers' data-user='{$_SESSION['id']}' data-toggle='modal' data-target='#exampleModalLong'>$followers&nbsp;Follower</p> ";
                }
              } else {
                if (isset($_GET['userId'])) {
                echo "<p class='text-white d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='followers' data-user='{$_GET['userId']}' data-toggle='modal' data-target='#exampleModalLong'>$followers&nbsp;Followers</p> ";
                } else {
                  echo "<p class='text-white d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='followers' data-user='{$_SESSION['id']}' data-toggle='modal' data-target='#exampleModalLong'>$followers&nbsp;Followers</p> ";
                }
              }
            } else {
              echo "<p class='text-white d-inline bg-dark rounded py-1 px-2'>0 Followers</p> ";
            }

            // get number of how many people you are following
            $sql = "SELECT * FROM follow WHERE user_id = {$row['id']}";
            $result = $db->query($sql);
            if (mysqli_num_rows($result) > 0) {
              $following = 0;
              while ($followRow = $result->fetch_assoc()) {
                $following += 1;
              }
              if (isset($_GET['userId'])) {
                echo "<p class='text-white d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='following' data-user='{$_GET['userId']}' data-toggle='modal' data-target='#exampleModalLong'>$following&nbsp;Following</p>";
              } else {
                echo "<p class='text-white d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='following' data-user='{$_SESSION['id']}' data-toggle='modal' data-target='#exampleModalLong'>$following&nbsp;Following</p>";
              }
            } else {
              echo "<p class='text-white d-inline bg-dark rounded py-1 px-2'>0 Following</p>";
            }
          ?>    
        </div>

        <div class="bd-highlight">
          <?php
            // follow or unfollow
            if (isset($_GET['userId'])) {
              $currentUser = $_SESSION['id'];
              $userIdProfile = $_GET['userId'];
              $sql = "SELECT * FROM follow WHERE user_id = $currentUser AND following = $userIdProfile";
              $result = $db->query($sql);
              if (mysqli_num_rows($result) == 0) {
                if ($currentUser != $userIdProfile) {
                  echo "<a class='text-link d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='follow' data-user='$userIdProfile' href='inc/pages/profile/modalScript.inc.php?userId=$userIdProfile&action=follow'>Follow</a>";
                }
              } else {
                echo "<a class='text-danger d-inline bg-dark rounded py-1 px-2 cursor-pointer' id='unfollow' data-user='$userIdProfile' href='inc/pages/profile/modalScript.inc.php?userId=$userIdProfile&action=unfollow'>Unfollow</a>";
              }
            }
          ?>
          </div>
      </div>

    </div><!-- col-md-6 End -->

    <div class="col-md-6 pt-3"><!-- col-md-6 Start -->

      <div class="bd-highlight h-auto">
        <div class="d-flex">
          <p class="font-weight-bold mb-1 justify-content-start">Bio:</p>
            <!-- Edit icon -->
            <?php 
            if (isset($_GET['userId'])) {
              if ($_GET['userId'] == $_SESSION['id']) {
                echo "<div class='ml-auto justify-content-end'>";
                  echo "<i class='fas fa-lg fa-edit cursor-pointer text-dark' id='edit' data-toggle='modal' data-target='#exampleModalLong'></i>";
                echo "</div>";
              }
            } else {
              echo "<div class='ml-auto justify-content-end'>";
                echo "<i class='fas fa-lg fa-edit cursor-pointer text-dark' id='edit' data-toggle='modal' data-target='#exampleModalLong'></i>";
              echo "</div>";
            }
          ?>
          </div>
          <!-- Bio -->
          <div class="pr-4" style="overflow-wrap: break-word;">
            <?php 
            if ($row['bio'] == '') {
              echo "<span class='text-muted font-italic'>Add a Bio</span>";
            } else {
              echo "<span class='text-muted font-italic'>{$row['bio']}</span>";
            }
            ?>
          </div>
          <!-- Favourite Tags -->
          <!-- <p class="font-weight-bold d-inline">Favourite <span class="font-italic">#tags</span>: 
            <?php 
              $sql = "SELECT tag.tag FROM tag JOIN fav_tag ON fav_tag.tag_id = tag.id WHERE fav_tag.user_id = {$row['id']}";
              $result = $db->query($sql);
              if (mysqli_num_rows($result) > 0) {
                while ($tagRow = $result->fetch_assoc()) {
                  echo "<span class='text-link bg-dark px-1 rounded font-weight-normal tag'>#{$tagRow['tag']}</span>";
                }
              } else {
                echo "<span class='text-muted font-weight-normal font-italic d-inline'>Add a Favourite Tag</span>";
              }
            ?>            
          </p> -->
          <?php 
            if (isset($_GET['userId'])) {
              if ($_GET['userId'] == $_SESSION['id']) {
                echo "<div class='mt-2'>";
                  echo "<img src='icons/contestToken.png' class='token' alt=''><strong class='badge badge-warning'>{$row['contest_tokens']}</strong>";
                echo "</div>";
              }
            } else {
              echo "<div class='mt-2'>";
                echo "<img src='icons/contestToken.png' class='token' alt=''><strong class='badge badge-warning'>{$row['contest_tokens']}</strong>";
              echo "</div>";
            }
          ?>


          <!-- Badges -->
          <div class="mt-2">
            <p class="font-weight-bold d-inline">Badges:</p>
          </div>
          <?php 
              $sql = "SELECT market.item FROM market JOIN user_badge ON user_badge.badge_id = market.id WHERE user_badge.user_id = {$row['id']}";
              $result = $db->query($sql);
              if (mysqli_num_rows($result) > 0) {
                echo "<div class='mt-2'>";
                while ($badgeRow = $result->fetch_assoc()) {
                  echo "<i class='fas fa-2x pr-2 {$badgeRow['item']}'></i>";
                }
                echo "</div>";
              }
            ?>
      </div>

    </div><!-- col-md-6 End -->
    
  </div><!-- First Row End -->

  <div class="row mt-3"><!-- Second Row Start -->
    <div class="col-12">

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
      <li class="nav-item pr-1">
        <a class="nav-link text-dark active" id="all-tab" data-toggle="pill" href="#all" role="tab" aria-controls="all" aria-selected="true">Posts</a>
      </li>
      <li class="nav-item pr-1">
        <a class="nav-link text-dark" id="contest-tab" data-toggle="pill" href="#contest" role="tab" aria-controls="contest" aria-selected="false">Contest</a>
      </li>
    </ul>
    <hr>
    <div class="tab-content" id="pills-tabContent">
      <!-- ********************All the posts as cards******************* -->
      <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <?php 
          if (isset($_GET['userId'])) {
            posts($db, $_GET['userId'], 1, 0);
          } else {
            posts($db, $_SESSION['id'], 1, 0);
          }        
        ?>
      </div>

      <!-- *******************Contest******************* -->
      <div class="tab-pane fade" id="contest" role="tabpanel" aria-labelledby="contest-tab">
        <?php 
          if (isset($_GET['userId'])) {
            posts($db, $_GET['userId'], 2, 1);
          } else {
            posts($db, $_SESSION['id'], 2, 1);
          }        
        ?>
      </div>
    </div>

    </div>
  </div><!-- Second Row End -->
</div><!-- Container End -->

<?=editModal()?>
<script src="js/functions.js"></script>
<script>
  $('#followers').click(function(){
    var userId = $(this).attr('data-user');
    // AJAX request
    $.ajax({
      url: 'inc/pages/profile/modalScript.inc.php',
      type: 'post',
      data: {
        modal: 'follow',
        userId: userId
        },
      success: function(response){
        // Add response in Modal body
        var modalTitle = "Followers";
        $('.modal-title').html(modalTitle);
        $('.modal-body').html(response);
      }
    });
  });

  $('#following').click(function(){
    var userId = $(this).attr('data-user');
    // AJAX request
    $.ajax({
      url: 'inc/pages/profile/modalScript.inc.php',
      type: 'post',
      data: {
        modal: 'following',
        userId: userId
        },
      success: function(response){
        // Add response in Modal body
        var modalTitle = "Following";
        $('.modal-title').html(modalTitle);
        $('.modal-body').html(response);
      }
    });
  });

  $('#edit').click(function(){
    // AJAX request
    $.ajax({
      url: 'inc/pages/profile/modalScript.inc.php',
      type: 'post',
      data: {modal: 'edit'},
      success: function(response){
        // Add response in Modal body
        var modalTitle = "Edit Profile";
        $('.modal-title').html(modalTitle);
        $('.modal-body').html(response);
      }
    });
  });

  window.setTimeout(function() {
    $(".updateAlert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
  }, 4000);
</script>