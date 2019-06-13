<?php 
  $currentPage = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
  $_SESSION['currentEditPage'] = $currentPage;
?>
<div class="container my-5 pt-3" data-currentUser="<?= $_SESSION['firstName'].' '.$_SESSION['lastName'] ?>" data-userId="<?=$_SESSION['id']?>" data-profileImg="<?=$_SESSION['profileImg']?>">
  <div class="row pt-3"><!-- First Row Start -->

    <!-- col-lg-8 Start -->
    <div class="col-lg-8"><?=posts($db, $_SESSION['id'], 0, 0)?></div>
    <!-- col-lg-8 End -->

    <!-- col-lg-4 Start -->
    <div class="col-lg-4 d-none d-lg-block"><?=sidePost($db)?></div>
    <!-- col-lg-4 End -->

  </div><!-- First Row End -->
</div><!-- Container End -->

<!-- Modal -->
<?=editModal()?>
<?=contestInfo()?>

<script src="js/functions.js"></script>