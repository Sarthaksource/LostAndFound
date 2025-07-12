<?php

  session_start();
  if(!isset($_SESSION['student_id']))
  {
    header('location:login.php');
    exit;
  }  

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit;
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
    <h2 class="text-center my-4">Welcome <?php echo $_SESSION['student_id'];?></h2>
    <div class="btnContainer">
      <div class="d-flex flex-column justify-content-center">
        <a href="report.php"><button type="submit" class="btn btn-outline-secondary w-100">Report a Found Item</button></a>
        <a href="search.php"><button type="submit" class="btn btn-outline-secondary w-100">Search for Lost Item</button></a>
        <form action="home.php" method="POST">
          <button type="submit" class="btn btn-outline-secondary w-100" name="logout">Log out</button>
        </form>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>