<?php

$login = 0;
$invalid = 0;
$attack = 0;


session_start();

if (isset($_SESSION['student_id'])) {
    header("location: home.php");
    exit;
}


if($_SERVER["REQUEST_METHOD"]=="POST")
{
  include "connect.php";

  $student_id = $_POST["student_id"];
  $password = $_POST["password"];

  if($student_id=="" || $password=="")
  {
    $attack = 1;
  }
  else
  {
    $sql = "SELECT * from registration where student_id = '$student_id' and password = '$password'";
    $result = mysqli_query($con, $sql);
    if($result)
    {
      $num = mysqli_num_rows($result);
      if($num>0)
      {
        //echo "Login Successful!";
        $login = 1;

        session_start();
        $_SESSION['student_id'] = $student_id;
        header('location:home.php');
      }
      else
      {
          // echo "Invalid Data";
        $invalid = 1;
      }
    }
  }  
}

?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link href="styles.css" rel="stylesheet">
</head>
<body>

  <?php
  if($invalid || $attack)
  {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error</strong> Invalid data!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
  }
  if($login)
  {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Viola!</strong> Login successful
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
  }
  ?>

  <h2 class="text-center" id="topic">Login</h2>
  <div class="container my-4">
    <form action="login.php" method="POST">
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Student Id</label>
        <input type="text" class="form-control bg-custom" placeholder="Enter your student id" name="student_id">
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" class="form-control bg-custom" placeholder="Enter your password" name="password">
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <label class="form-check-label mt-3" for="flexCheckDefault">
        Don't have an account, <a href="signup.php">signup</a>
      </label>
    </form>
  </div>

  <!-- <script src="style.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>