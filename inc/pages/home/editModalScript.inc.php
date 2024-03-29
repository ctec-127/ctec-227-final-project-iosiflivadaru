<?php 
require_once '../../shared/db_connect.php';
session_start();
$currentPage = $_SESSION['currentEditPage'];
$postId = $_POST['postId'];

$sql = "SELECT user_id, description FROM post WHERE id = $postId";
$result = $db->query($sql);
$row = $result->fetch_assoc();

if ($_SESSION['id'] == $row['user_id']) { 
  $description = $row['description'];
?>


<form action="inc/pages/home/editPostScript.inc.php" method="POST" enctype="multipart/form-data" id="postEdit" data-postId="<?= $postId ?>">
  <!-- Description -->
  <div class="mb-3">
    <label for="validationTextarea">Description</label>
    <textarea class="form-control" name="description" id="desc" placeholder="<?= ($description == '')? "Post description..." : '' ?>" ><?= ($description != '')? $description : '' ?></textarea>
  </div>
  <!-- Add Tags -->          
  <div class="d-flex mr-auto bd-highlight">
    <select class="form-control w-auto" id="selectTag" data-live-search="true">
      <option value="" hidden>Add Tags</option>
      <?php 
        $sql = "SELECT * FROM tag";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<option data-tokens='{$row['tag']}' data-tagId='{$row['id']}'>{$row['tag']}</option>";
        }
      ?>
    </select>
    <div class="my-auto ml-2">
      <i class="fas fa-2x fa-hashtag d-inline"></i>
      <p class="d-inline" id="tagList">
      <?php 
        $sql = "SELECT tag, tag.id FROM tag JOIN post_tag ON post_tag.tag_id = tag.id WHERE post_id = $postId";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          $id = $row['id'];
          $tag = $row['tag'];
          echo "<input type='checkbox' name='tag[]' value='$id' hidden checked><span class='text-link bg-dark px-1 rounded font-weight-normal tag ml-1 d-inline-block' data-toggle='tooltip' data-placement='top' title='Click to remove' onclick='removeTag(\"$tag\",$id)' data-id='$id' data-tag='$tag'>#$tag</span>";
        }        
      ?>
      </p>
    </div>

  </div>      
  <!-- Submit Button -->
  <hr>
  <div class="d-flex flex-row-reverse">
    <a class="btn btn-outline-dark  text-danger delete" href="inc/pages/home/editPostScript.inc.php?delete=<?=$postId?>">Delete</a>
    <button type="reset" class="btn btn-outline-dark  text-link-normal mr-2" id="resetForm">Reset</button>
    <a  class="btn bg-dark text-link w-auto px-5 float-right mr-2" onclick="submitPost('<?=$currentPage?>')">Submit</a>
  </div>
</form>    


<?php
} 
?>

<script>
$(function () {
  $('select').selectpicker();
});

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
    console.log("sestieeeeeee")
    $("#tagList").append(`<input type='checkbox' name='tag[]' value='${tagId}' hidden checked><span class='text-link bg-dark px-1 rounded font-weight-normal tag ml-1 d-inline-block' data-toggle='tooltip' data-placement='top' title='Click to remove' onclick='removeTag("${tag}",${tagId})'  data-id='${tagId}' data-tag='${tag}'>#${tag}</span>`);
  }
  $(this).prop('selectedIndex',0);
});

// Delete post
var elems = document.getElementsByClassName('delete');
var confirmIt = function (e) {
    if (!confirm('Are you sure you want to permanently delete this post?')) e.preventDefault();
};
for (var i = 0, l = elems.length; i < l; i++) {
    elems[i].addEventListener('click', confirmIt, false);
}

// Reset form
$("#resetForm").on('click', function(){
  $.each(tagArray, function(key, value) {
    removeTag(value, key)
  })

  $.each(tempTag, function(key, value) {
    $(`input[value=${key}]`).remove()
    $(`span[data-id=${key}]`).remove()
  })

  $.each(tempTag, function(key, value) {
    $("#tagList").append(`<input type='checkbox' name='tag[]' value='${key}' hidden checked><span class='text-link bg-dark px-1 rounded font-weight-normal tag ml-1 d-inline-block' data-toggle='tooltip' data-placement='top' title='Click to remove' onclick='removeTag("${value}",${key})'  data-id='${key}' data-tag='${value}'>#${value}</span>`);
  })

});

// Submit post
function submitPost(page) {
  // e.preventDefault();
  // console.log("hi")
  var postId = $("form").attr("data-postId");
  // console.log(postId)
  var description = $("#desc").val()
  // console.log(description)
  var formData = new FormData($('#postEdit')[0]);
  formData.append('postId', postId)
  // console.log(formData)
  // AJAX request
  $.ajax({
    url: 'inc/pages/home/editPostScript.inc.php',
    type: 'post',
    data: formData,
    success: function(response){
      // console.log(response)
      if (response == "post") {
        $(location).attr('href', `${page}`)
      }
    },
    contentType: false,
    processData: false
  });
}
</script>