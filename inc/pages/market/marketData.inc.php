<div class="container my-5 pt-3">
  <div id="alertPurchase">
    <?php 
    if (isset($_SESSION['purchase']) && $_SESSION['purchase'] == 1) {
      echo "<div class='alert alert-success alert-dismissible fade show mt-2 mb-1' role='alert'>Successful purchase! Enjoy your Badge!!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
      $_SESSION['purchase'] = 0;
    }
    ?>
  </div>
  <div class="row pt-3"><!-- First Row Start -->

    <div class="col-lg-8"><!-- col-lg-8 Start -->
      <ul class="nav nav-pills mb-3 d-flex justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item pr-1">
          <a class="nav-link text-dark active" id="badges-tab" data-toggle="pill" href="#badges" role="tab" aria-controls="badges" aria-selected="true">Badges</a>
        </li>
        <li class="nav-item pr-1">
          <a class="nav-link text-dark" id="upgrade-tab" data-toggle="pill" href="#upgrade" role="tab" aria-controls="upgrade" aria-selected="false">Upgrade Limits</a>
        </li>
      </ul>
      <hr>
      <div class="tab-content" id="pills-tabContent">
        <!-- ********************badges******************* -->
        <div class="tab-pane fade show active" id="badges" role="tabpanel" aria-labelledby="badges-tab">
          <div class='d-flex flex-wrap justify-content-center justify-content-md-start'>
          <?php 
            $sql = "SELECT * FROM market WHERE id NOT IN (SELECT badge_id FROM user_badge WHERE user_id = {$_SESSION['id']}) AND tag = 'badge'";
            $result = $db->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card mr-3 mb-3' style='width:140px;'>";
                  echo "<div class='card-body p-3'>";
                    echo "<i class='fas fa-2x pb-3 d-inline-block w-100 text-center {$row['item']}'></i><br>";
                    echo "<button class='btn bg-gray text-center w-100' data-toggle='modal' data-target='#exampleModal' data-item='{$row['id']}' data-tag='badge'><img src='icons/contestToken.png' class='token' alt='Contest Token'><strong>{$row['price']}</strong></button>";
                  echo "</div>";
                echo "</div>";
            }
            if (!mysqli_num_rows($result)) {
              echo "<div class='jumbotron py-3 mx-auto' id='noResult'><h3 class='text-center'>More Badges Coming Soon! :)</h3></div>";
            }
          ?>
          </div>
        </div>

        <!-- *******************upgrade limits******************* -->
        <div class="tab-pane fade" id="upgrade" role="tabpanel" aria-labelledby="upgrade-tab">
          <div class='d-flex flex-wrap justify-content-center justify-content-md-start'>
            <?php 
              $sql = "SELECT market.id, item, price, user.bio_limit FROM market JOIN user WHERE user.id = {$_SESSION['id']} AND tag = 'upgrade'";
              $result = $db->query($sql);
              while ($row = $result->fetch_assoc()) {
                $itemName = '';
                if ($row['item'] == 'bio') {
                  $itemName = 'bioLimit';
                } else if ($row['item'] == 'comingSoon') {
                  $itemName = 'comingSoon';
                }
                  echo "<div class='card mr-3 mb-3' style='width:200px;'>";
                    echo "<div class='card-header text-center'>
                            <strong>Increase Bio Limit</strong>
                          </div>";
                    echo "<div class='card-body p-3'>";
                      echo "<h3 class='text-center m-0 font-weight-normal'>{$row['bio_limit']} <span class='font-weight-bold'>+20</span></h3><br>";
                      echo "<button class='btn bg-gray text-center w-100' data-toggle='modal' data-target='#exampleModal' data-item='{$row['id']}' data-tag='$itemName'><img src='icons/contestToken.png' class='token' alt='Contest Token'><strong>{$row['price']}</strong></button>";
                    echo "</div>";
                  echo "</div>";
              }
              if (!mysqli_num_rows($result)) {
                echo "<div class='jumbotron py-3 mx-auto' id='noResult'><h3 class='text-center'>More Badges Coming Soon! :)</h3></div>";
              }
            ?>
          </div>
        </div>
      </div>

    </div><!-- end col-lg-8 -->

    <div class="col-lg-4 d-none d-lg-block">
      <?php sidePost($db) ?>
    </div>

  </div>
</div>

<!-- Buy Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">LivIt Market</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to buy this?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark text-link" data-dismiss="modal" id="buy">Yes</button>
        <button type="button" class="btn btn-outline-dark text-danger" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

<script>
$("button").on('click', function(){

  var itemId = $(this).attr("data-item")
  var itemTag = $(this).attr("data-tag")

  $("#buy").on('click', function(){
    $.ajax({
      type: "post",
      url: "inc/pages/market/buyScript.inc.php",
      data: {
        itemId: itemId,
        itemTag: itemTag
      },
      success: function (response) {
        console.log(response)
        if (response == 1) {
          $(location).attr('href', 'market.php')
        } else {
          $("#alertPurchase").html("<div class='alert alert-danger alert-dismissible fade show mt-2 mb-1' role='alert'>Not enough Contest Tokens! Try again later!<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>")
        }
      }
    });
  })

})
</script>