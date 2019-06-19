function profile(userId) {
  // console.log(userId);
  $(location).attr('href', `profile.php?userId=${userId}`);
}

// Like
function like(postId, a) {
  var postId = postId
  var a = a;
  var like = "like"
  var likesNr = $("#like"+postId+a).next().html()

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
      $("#like"+postId+a).next().html(newNr)

      var profileImg = $(".container").attr("data-profileImg")
      var userId = $(".container").attr("data-userId")

      var userName = $(".container").attr("data-currentUser")

      var oldTooltip = $("#like"+postId+a).next().attr('data-original-title')
      var currentUser = $(".container").attr("data-currentUser") + "<br>"
      var res = currentUser + oldTooltip

      $("#like"+postId+a).next().attr('data-original-title', `${res}`)
      $("#like"+postId+a).after(`<i class='fas fa-thumbs-up' id='dislike${postId+a}' onclick='dislike(${postId},\"${a}\")'></i>`)
      $("#like"+postId+a).remove()
      $("#displayLikes"+postId+a).append(`<div class='d-flex justify-content-start align-items-center mt-3' data-userLike='${userName}'><img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile(${userId})' src='img/${profileImg}' style='width:35px; height:35px;' alt='User Image'><h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile(${userId})'>${userName}</h6></div>`)
    }
  });
}
// Dislike
function dislike(postId, a) {
  var postId = postId
  var a = a
  var dislike = "dislike"
  var likesNr = $("#dislike"+postId+a).next().html()

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
      $("#dislike"+postId+a).next().html(newNr)

      var userName = $(".container").attr("data-currentUser")
      var oldTooltip = $("#dislike"+postId+a).next().attr('data-original-title')
      var currentUser = userName + "<br>"
      var res = oldTooltip.replace(currentUser, '')
      
      $("#dislike"+postId+a).next().attr('data-original-title', `${res}`)
      $("#dislike"+postId+a).after(`<i class='far fa-thumbs-up' id='like${postId+a}' onclick='like(${postId},\"${a}\")'></i>`)
      $("#dislike"+postId+a).remove()
      $(`div[data-userLike="${userName}"]`).remove()
    }
  });
}

// Upvote
function upvote(postId,a) {
  var postId = postId
  var a = a
  var upvote = "upvote"
  var upvotesNr = $("#upvote"+postId+a).next().html()

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
      $("#upvote"+postId+a).next().html(newNr)

      var profileImg = $(".container").attr("data-profileImg")
      var userId = $(".container").attr("data-userId")

      var oldTooltip = $("#upvote"+postId+a).next().attr('data-original-title')
      var userName = $(".container").attr("data-currentUser")
      var currentUser = userName + "<br>"
      var res = currentUser + oldTooltip

      $("#upvote"+postId+a).next().attr('data-original-title', `${res}`)
      $("#upvote"+postId+a).after(`<i class='fas fa-star' id='downvote${postId+a}' onclick='downvote(${postId},\"${a}\")'></i>`)
      $("#upvote"+postId+a).remove()
      $("#displayContest"+postId+a).append(`<div class='d-flex justify-content-start align-items-center mt-3' data-userContest='${userName}'><img class='rounded-circle float-left profile d-inline-block cursor-pointer' onclick='profile(${userId})' src='img/${profileImg}' style='width:35px; height:35px;' alt='User Image'><h6 class='d-inline m-0 ml-2 cursor-pointer' onclick='profile(${userId})'>${userName}</h6></div>`)
    }
  });
}
// Downvote
function downvote(postId,a) {
  var postId = postId
  var a = a
  var downvote = "downvote"
  var downvoteNr = $("#downvote"+postId+a).next().html()

  // AJAX request
  $.ajax({
    url: 'inc/pages/home/contestScript.inc.php',
    type: 'post',
    data: {
      downvote: downvote,
      postId: postId
      },
    success: function(response){
      var newNr = parseInt(downvoteNr) - 1
      $("#downvote"+postId+a).next().html(newNr)

      var oldTooltip = $("#downvote"+postId+a).next().attr('data-original-title')
      var userName = $(".container").attr("data-currentUser")
      var currentUser = userName + "<br>"
      var res = oldTooltip.replace(currentUser, '')
      
      $("#downvote"+postId+a).next().attr('data-original-title', `${res}`)
      $("#downvote"+postId+a).after(`<i class='far fa-star' id='upvote${postId+a}' onclick='upvote(${postId},\"${a}\")'></i>`)
      $("#downvote"+postId+a).remove()
      $(`div[data-userContest="${userName}"]`).remove()
    }
  });
}


// ******************************* Post Script *******************************
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

// Edit Modal
$('.editPost').click(function(){
  var postId = $(this).attr('data-postId');
  // console.log('hi')
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
// ***************************************************************************