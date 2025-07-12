<?php

$submit = 0;
$attack = 0;
$err = 0;

session_start();
if (!isset($_SESSION['student_id'])) {
    header('location:login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "connect.php";
    include "secrets.php"; // Load tokens securely

    $student_id = $_SESSION['student_id'];
    $item_name = $_POST["item_name"];
    $item_location = $_POST["item_location"];
    $item_date = $_POST["item_date"];
    $contact = $_POST["contact"];

    if ($item_name == "" || $item_location == "") {
        $attack = 1;
    }

    if (isset($_FILES["item_image"]) && $_FILES["item_image"]["error"] == 0) {
        $filename = $_FILES["item_image"]["name"];
        $filetmpname = $_FILES["item_image"]["tmp_name"];

        $fp = fopen($filetmpname, 'rb');
        $size = filesize($filetmpname);

        // Upload to Dropbox
        $cheaders = [
            'Authorization: Bearer ' . DROPBOX_ACCESS_TOKEN,
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: {"path":"/Apps/Lost_Found/' . $filename . '", "mode":"add"}'
        ];

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        fclose($fp);

        if ($response === false) {
            $err = 1;
        } else {
            $data = json_decode($response, true);

            if (isset($data['path_lower'])) {
                $fileMetadata = ['path' => $data['path_lower']];

                $ch = curl_init('https://api.dropboxapi.com/2/files/get_temporary_link');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . DROPBOX_ACCESS_TOKEN,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fileMetadata));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                if ($response === false) {
                    $err = 1;
                } else {
                    $data = json_decode($response, true);

                    if (isset($data['link'])) {
                        $fileUrl = $data['link'];

                        // Shorten URL with Cutt.ly
                        $cuttlyUrl = 'https://cutt.ly/api/api.php?key=' . CUTTLY_API_KEY . '&short=' . urlencode($fileUrl);
                        $cuttlyResponse = file_get_contents($cuttlyUrl);
                        $cuttlyData = json_decode($cuttlyResponse, true);

                        if ($cuttlyData['url']['status'] == 7) {
                            $shortUrl = $cuttlyData['url']['shortLink'];

                            $sql = "INSERT INTO reportdetails (student_id, item_name, item_location, item_date, contact, image_url) 
                                    VALUES ('$student_id', '$item_name', '$item_location', '$item_date', '$contact', '$shortUrl')";
                            $result = mysqli_query($con, $sql);

                            if ($result) {
                                $submit = 1;
                            } else {
                                $err = 1;
                            }
                        } else {
                            $err = 1;
                        }
                    } else {
                        $err = 1;
                    }
                }
                curl_close($ch);
            } else {
                $err = 1;
            }
        }
    } else {
        echo "Image Error";
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
if($err)
{
  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error</strong> Unable to upload image!
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>

<div class="row vh-100 w-100">
  <div class="btnContainer">
    <form action="report.php" method="POST" enctype="multipart/form-data">
      <h2 class="d-flex justify-content-center">REPORT</h2>
      <hr class="mb-2 mt-1">
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Item Name</label>
        <input type="text" class="form-control bg-custom" id="exampleFormControlInput1" placeholder="Item Name" name="item_name">
      </div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Location of Found Item</label>
        <input type="text" class="form-control bg-custom" id="exampleFormControlInput1" placeholder="Location" name="item_location">
      </div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Date of Item Found</label>
        <input type="date" class="form-control bg-custom" id="exampleFormControlInput1" name="item_date">
      </div>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Contact (active)</label>
        <input type="text" class="form-control bg-custom" id="exampleFormControlInput1" placeholder="Contact Detail" name="contact">
      </div>
      <label for="formFile" class="form-label">Item Image</label>
      <div class="mb-3">
        <input type="file" class="bg-custom" accept="image/png, image/jpeg" id="item_image" name="item_image" required>
      </div>

      <button type="submit" class="btn btn-outline-secondary w-100">Report</button>
    </form>
    <a href="home.php"><button class="btn btn-outline-secondary w-100">Back</button></a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>