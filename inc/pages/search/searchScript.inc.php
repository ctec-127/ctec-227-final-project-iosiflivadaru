<div class="container mb-5">
  <div class="row mt-4 mb-5"><!-- First Row Start -->
    <div class="col-md-12"><!-- col-md-12 Start -->
      <form class="d-flex align-items-start" action="inc/pages/search/getUsersScript.inc.php" method="POST" enctype="multipart/form-data">
        <!-- Search -->
        <div class="form-group">
          <input type="text" class="form-control w-auto" id="search" name="search" placeholder="Search User">
        </div>
        <!-- Submit Button -->
        <button type="submit" class="btn bg-dark text-link w-auto px-4 ml-3" id="submit">Search</button>
      </form>
    </div><!-- col-md-12 End -->

  </div><!-- First Row End -->
</div><!-- Container End -->

<script>
  function profile(userId) {
    console.log(userId);
    $(location).attr('href', `profile.php?userId=${userId}`);
  }

  $('#submit').click(function(e){
    e.preventDefault();
    var formData = new FormData($('form')[0]);
    // AJAX request
    $.ajax({
      url: 'inc/pages/search/getUsersScript.inc.php',
      type: 'post',
      data: formData,
      success: function(response){
        // console.log(response)
        if (response == "none") {
          $("#noResult").remove();
          $(".searchUsers").remove();
          $("form").after("<div class='jumbotron py-3' id='noResult'><h3 class='text-center'>No Users Found!</h3></div>");
        } else if (response != "empty") {
          $("#noResult").remove();
          $(".searchUsers").remove();
          $("form").after(response);          
        }
      },
      contentType: false,
      processData: false
    });
  });
</script>