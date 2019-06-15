<div class="container my-5 pt-3">
  <div class="row pt-3"><!-- First Row Start -->
    <div class="col-md-2"></div>
    <div class="col-md-8"><!-- col-md-8 Start -->

      <form method="POST" action="inc/pages/post/functionScript.inc.php" enctype="multipart/form-data">
        <!-- Description -->
        <div class="form-group">
          <!-- <h5><label for="validationTextarea">Description</label></h5> -->
          <textarea class="form-control" name="description" placeholder="Post description..." rows="4"><?php
          if (isset($_SESSION['description'])) {
            if($_SESSION['description'] != ''){ 
              echo $_SESSION['description'];
            }
          }
          ?></textarea>
        </div>
        <!-- Add Tags -->          
        <div class="d-flex bd-highlight mb-3">
          <div class="bd-highlight">
            <select class="form-control selectpicker w-auto" id="selectTag" data-live-search="true">
              <option value="" hidden>Add Tags</option>
              <?php 
                $sql = "SELECT * FROM tag";
                $result = $db->query($sql);
                while ($row = $result->fetch_assoc()) {
                  echo "<option data-tokens='{$row['tag']}' data-tagId='{$row['id']}'>{$row['tag']}</option>";
                }
              ?>
            </select>
          </div>
          <div class="d-inline-block my-auto ml-2">
            <!-- <h5 class="d-inline">Tags:</h5> -->
            <i class="fas fa-2x fa-hashtag d-inline"></i>
            <div class="d-inline" id="tagList">
                <?php 
                if (isset($_SESSION['tag'])) {
                  foreach ($_SESSION['tag'] as $key => $value) {
                    $sql = "SELECT tag FROM tag WHERE id = $value";
                    $result= $db->query($sql);
                    $row = $result->fetch_assoc();
                    echo "<input type='checkbox' name='tag[]' value='$value' hidden checked><span class='text-link bg-dark px-1 rounded font-weight-normal tag ml-1 d-inline-block' data-toggle='tooltip' data-placement='top' title='Click to remove' onclick='removeTag(\"{$row['tag']}\",$value)' data-id='$value' data-tag='{$row['tag']}'>#{$row['tag']}</span>";
                  }
                }
                ?>
            </div>
          </div>
        </div>

        <!-- Create New Tag -->
        <div class="ml-auto mb-2 bd-highlight d-flex">
          <input type="text" class="form-control w-auto d-inline" id="newTag" name="newTag" placeholder="Create New Tag">
          <p class="btn btn-outline-dark text-link-normal w-auto px-4 ml-2 my-auto cursor-pointer" id="addTag">Add</p>
          <div class="invalid-feedback">
            No spaces allowed!
          </div>
        </div>

        <!-- Post Image -->        
        <div class="d-block" id="imgBox" data-contest="<?=$_SESSION['contest']?>">
          <label class="m-0" id="imgLabel" for="postImg">
            <i class="fas fa-2x fa-image mr-1" id='defaultImg'></i>
            <!-- <h5 class="d-inline mb-5">Add Image</h5> -->
          </label>
          <input class="mx-auto" type="file" id="postImg" name="postImg" accept=".jpg,.png,.jpeg" hidden>
        </div>

        <!-- Submit Button -->
        <hr class="my-2">
        <button type="submit" class="btn bg-dark text-link w-auto float-right px-4" id="submit">Submit</button>
      </form>

    </div><!-- col-md-8 End -->
    <div class="col-md-2 mt-5"></div>
  </div><!-- First Row End -->
</div><!-- Container End -->


<script>  
  var tagArray = {};
  var tempTag = {};

  // store tags in objects
  $("#tagList > span").each(function(i, obj){
    var tagId = $(this).attr("data-id")
    var tag = $(this).attr("data-tag")
    tempTag[tagId] = tag
    tagArray[tagId] = tag
  })

  // remove tag
  function removeTag(tag,tagId) {
    $('span[data-tag='+tag+']').remove()
    $('input[value='+tagId+']').remove()
    $(".tooltip").remove()

    delete tagArray[tagId]
  }

  // Tooltips
  $("#tagList").hover(function(){
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  });


  // check if tag is already selected
  $("#selectTag").on('change', function () {
    var tag = $(this).find(":selected").text();  
    var tagId = $(this).find(":selected").attr("data-tagId");
    var flag = false;

    $.each(tagArray, function(key, value) {
      if (key == tagId) {
        flag = true
      }
    })

    if (!flag) {
      tagArray[tagId] = tag;
      // console.log("sestieeeeeee")
      $("#tagList").append(`<input type='checkbox' name='tag[]' value='${tagId}' hidden checked><span class='text-link bg-dark px-1 rounded font-weight-normal tag ml-1 d-inline-block' data-toggle='tooltip' data-placement='top' title='Click to remove' onclick='removeTag("${tag}",${tagId})'  data-id='${tagId}' data-tag='${tag}'>#${tag}</span>`);
    }
    $(this).prop('selectedIndex',0);
  });
  
  // Remove the default image when you select your profile image
  function tempImg(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $("#postImgTemp").remove();
        $("#alertNewImage").remove(); 
        $("#imgBox").after(`<img class='d-block mx-auto img-fluid' style='width:500px; max-height:100%;' src='${e.target.result}' alt='User Image' id='postImgTemp'>`); 
        var contest = $("#imgBox").attr("data-contest");
        if (contest != 1) {
        $("#joinContest").remove();
        $("#imgLabel").after('<div class="custom-control custom-checkbox my-2" id="joinContest"><input type="checkbox" class="custom-control-input text-link" id="customControlAutosizing" name="contest"><label class="custom-control-label" for="customControlAutosizing">Join Contest <i>(you can only join once a week)</i></label></div>');
        }
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  // When the file input is being changed execute tempImg function
  $("#postImg").change(function() {
    tempImg(this);
    if ($("#postImg").val() == '') {
      // console.log("nothing");
      $("#joinContest").remove();
      $("#postImgTemp").remove();
    }
  });

  // Add new tag
  $("#addTag").on('click', function(e) {    
    var newTag = $("#newTag").val();
    var formData = new FormData($('form')[0]);    
    if (newTag.indexOf(' ') !== -1) {
      $("#newTag").remove("is-invalid");
      $("#newTag").addClass("is-invalid");
      $(".invalid-feedback").html("No spaces allowed!");
    } else {
      
      // check if tag already exists
      var allTags = [];
      $('#selectTag option').each(function(){
          allTags.push($(this).val());
      });
      var exists = false;
      for (let i = 0; i < allTags.length; i++) {
        if (newTag == allTags[i]) {
          var exists = true
        }        
      }

      // AJAX request
      if (exists == false) {
        $.ajax({
          url: 'inc/pages/post/newTagScript.inc.php',
          type: 'post',
          data: formData,
          success: function(response){
            // console.log(response)
            if (response == "success") {
              $(location).attr('href', 'post.php')
            }
          },
          contentType: false,
          processData: false
        });
      } else {
        $("#newTag").remove("is-invalid");
        $("#newTag").addClass("is-invalid");
        $(".invalid-feedback").html("Tag already exists!");
      }
    }
  })

  // Submit post
  $('#submit').click(function(e){
    e.preventDefault();
    var desc = $("textarea").val()
    var img = $("#postImg").val()
    // console.log("DESC:"+desc+" and IMG:"+img)
    if (desc != '' || img != '') {
      // console.log("hi")
      var formData = new FormData($('form')[0]);
      // AJAX request
      $.ajax({
        url: 'inc/pages/post/functionScript.inc.php',
        type: 'post',
        data: formData,
        success: function(response){
          // console.log(response)
          if (response == "post") {
            $(location).attr('href', 'home.php')
          }
          // if there are any errors coming from the script the following alerts will be displayed
          if (response == 'error1') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger my-3 w-auto text-center' role='alert' id='alertNewImage'>Your file is too big! Your Image should be less than 2MB!</div>");  
          }
          if (response == 'error2') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>There was an error uploading your file!</div>");  
          }
          if (response == 'error3') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Invalid file type! Only jpg, jpeg, and png files are allowed!</div>");  
          }
          if (response == 'password') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Passwords don't match!</div>");  
          }
          if (response == 'email') {
            $("#alertNewImage").remove();     
            $("#imgBox").after("<div class='alert alert-danger mb-4 w-auto text-center' role='alert' id='alertNewImage'>Email already in use!</div>");  
          }
        },
        contentType: false,
        processData: false
      });
    }
  });
</script>
<?php 
unset($_SESSION['description']); 
unset($_SESSION['tag']);
?>