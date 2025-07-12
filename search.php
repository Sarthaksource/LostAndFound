<?php

session_start();

$submit = 0;
$attack = 0;

$student_id = $_SESSION['student_id'];

if(!isset($_SESSION['student_id']))
{
  header('location:login.php');
  exit;
}

if($_SERVER["REQUEST_METHOD"]=="POST")
{
  include "connect.php";

  $item_name = mysqli_real_escape_string($con, $_POST['item_name']);
  $item_location = mysqli_real_escape_string($con, $_POST['item_location']);

  if($item_name=="")
  {
    $attack = 1;
  }
  else
  {
    // $sql = "SELECT * FROM reportdetails WHERE item_name = '$item_name' AND item_location = '$item_location'";

    $sql = "SELECT * FROM reportdetails WHERE item_name = '$item_name'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
      $fetched_result = array();

      while ($row = mysqli_fetch_assoc($result)) {
          $rep_id = $row['student_id'];
          $rep_item_name = $row['item_name'];
          $rep_item_location = $row['item_location'];
          $rep_item_date = $row['item_date'];
          $rep_contact = $row['contact'];
          $rep_image_url = $row['image_url'];
          array_push($fetched_result, array($rep_id, $rep_item_name, $rep_item_location, $rep_item_date, $rep_contact, $rep_image_url));            
      }
    }
    else
    {
      $fetched_result = [];
    }
  }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link href="styles.css" rel="stylesheet">
</head>
<body>

  <?php
    if($submit)
    {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
      Thank you for reporting!
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
    if($attack)
    {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error</strong> Invalid data!
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
  ?>

  <div class="row vh-100 w-100">
    <div class="btnContainer">
      <form action="search.php" method="POST">
        <h2 class="d-flex justify-content-center">SEARCH</h2>
        <hr class="mb-2 mt-1">
        <div class="mb-3"> 
          <label for="exampleFormControlInput1" class="form-label">Item Name</label>
          <input type="text" class="form-control bg-custom" id="exampleFormControlInput1" placeholder="Item Name" name="item_name" required>
        </div>
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Location of Lost Item</label>
          <input type="text" class="form-control bg-custom" id="exampleFormControlInput1" placeholder="Location" name="item_location">
        </div>

        <label for="formFile" class="form-label">Item Image</label>

          <div id="image-container" class="imgContainer mb-3"></div>

        <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
      </form>
      <a href="home.php"><button class="btn btn-outline-secondary w-100">Back</button></a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

  <script>
      let fetched_result = <?php echo json_encode($fetched_result); ?>;
      let div = document.getElementById('image-container');

      if(fetched_result.length>0) {
        fetched_result.forEach(item => {
          const img = document.createElement('img');
          img.src = item[5];
          img.alt = item[1];
          img.title = item[4];

          div.appendChild(img);
        });
      }
      else
      {
        div.innerHTML = '<h6>No results found</h6>';
      }
  </script>
</body>
</html>