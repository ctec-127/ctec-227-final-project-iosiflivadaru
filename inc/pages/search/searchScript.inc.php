<div class="container my-5 pt-3">
  <div class="row pt-3"><!-- First Row Start -->
    <div class="col-md-8"><!-- col-md-8 Start -->
      <div class="d-flex align-items-start justify-content-center justify-content-md-start after">
        <!-- Search -->
        <div class="form-group w-50">
          <input type="text" class="form-control" id="search" name="search" placeholder="Search User or Tag (use '#' before a tag)">
        </div>
        <!-- Submit Button -->
        <button class="btn bg-dark text-link w-auto px-4 ml-3" id="submit">Search</button>
      </div>
      <?php 
        if (isset($_GET['tag'])) {
          posts($db, $_SESSION['id'], 0, 0);
        }
      ?>
    </div><!-- col-md-8 End -->
    <div class="col-md-4"><?=sidePost($db)?></div>
  </div><!-- First Row End -->
</div><!-- Container End -->
<?php contestInfo() ?>
<script>
  function profile(userId) {
    console.log(userId);
    $(location).attr('href', `profile.php?userId=${userId}`);
  }

  $(".form-control").keyup(function(event) {
    if (event.keyCode === 13) {
        $("#submit").click();
    }
  });
  console.log("hello")
  $('#submit').click(function(e){
    var search = $("#search").val()
    if (search != '' && search.replace(/\s/g, '').length) {
      // AJAX request
      $.ajax({
        url: 'inc/pages/search/getUsersScript.inc.php',
        type: 'post',
        data: {search: search},
        success: function(response){
          console.log(response)
          if (response == "noTag") {
            $("#noResult").remove();
            $(".searchUsers").remove();
            $(".after").after(`<div class='jumbotron py-3' id='noResult'><h3 class='text-center'>No Posts Found with the <strong><i>${search}</i></strong> tag!</h3></div>`);
          } else if (response == "none") {
            $("#noResult").remove();
            $(".searchUsers").remove();
            $(".after").after("<div class='jumbotron py-3' id='noResult'><h3 class='text-center'>No Users Found!</h3></div>");
          } else if (response != "empty" && !$.isNumeric(response) ) {
            $("#noResult").remove();
            $(".searchUsers").remove();
            $(".after").after(response);   
          }
          if ($.isNumeric(response)) {
            $(location).attr('href', `search.php?tag=${response}`); 
          }
          
        }
      });
    }
  });
</script>