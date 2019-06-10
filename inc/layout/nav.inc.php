<nav class="navbar navbar-expand-sm navbar-light bg-dark d-flex justify-content-center fixed-top">

  
  <?php 
  if (!isset($_SESSION['loggedIn'])) {
    $_SESSION["loggedIn"] = 0;     
    $_SESSION['alert-login'] = 0; ?>
    <a class='navbar-brand text-center d-block mx-auto <?= echoActiveClassIfRequestMatches("home");?>' href='home.php'><?=$config['appName']?></a>
    <?php echo "</nav>";
  } else if ($_SESSION['loggedIn'] == 0) { ?>
    <a class='navbar-brand text-center d-block mx-auto <?= echoActiveClassIfRequestMatches("home");?>' href='home.php'><?=$config['appName']?></a>
    <?php echo "</nav>";
  } else if ($_SESSION['loggedIn'] == 1) {
    echo "<div class='d-flex' style='width:1110px;'>";
  ?>
  <a class="nav-link text-warning d-sm-none" style="width:75px;" href="market.php"><i class="fas fa-2x fa-store"></i></a>

  <a class='navbar-brand d-block mx-auto align-self-center <?= echoActiveClassIfRequestMatches("home");?>' href='home.php'><?=$config['appName']?></a>

  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
        <a class="nav-link mr-3 <?= echoActiveClassIfRequestMatches("post");?>" href="post.php"><i class="far fa-image mr-2"></i>Post</a>
      </li>
      <li class="nav-item">
        <a class="nav-link mr-3 <?= echoActiveClassIfRequestMatches("search");?>" href="search.php"><i class="fas fa-search mr-2"></i>Search</a>
      </li>
      <li class="nav-item">
        <a class="nav-link mr-3 <?= echoActiveClassIfRequestMatches("profile");?>" href="profile.php"><i class="fas fa-user mr-2"></i>Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link mr-3 text-warning" href="market.php"><i class="fas fa-store mr-2"></i>Market</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-danger" href="?loggedOut"><i class="fas fa-power-off mr-2"></i>Log out</a>
      </li>      
    </ul>
  </div>

  <a class="nav-link text-danger d-flex d-sm-none" style="width:75px;" href="?loggedOut"><i class="fas fa-2x fa-power-off ml-auto"></i></a>

  </div>
</nav>


<!-- Mobile Navbar -->
<nav class="navbar fixed-bottom d-sm-none navbar-light bg-dark px-0">

  <li class="d-inline-block mx-auto">
    <a class="nav-link text-light py-0 <?= echoActiveClassIfRequestMatches("search");?>" href="search.php"><i class="fas fa-2x fa-search"></i></a>
  </li>
  <li class="d-inline-block mx-auto">
    <a class="nav-link text-light py-0 <?= echoActiveClassIfRequestMatches("post");?>" href="post.php"><i class="far fa-2x fa-image"></i></a>
  </li>
  <li class="d-inline-block mx-auto">
    <a class="nav-link text-light py-0 <?= echoActiveClassIfRequestMatches("profile");?>" href="profile.php"><i class="fas fa-2x fa-user"></i></a>
  </li>

</nav>

<?php } ?>