<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FSX Framework</title>
    <link href="/mvc/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<br>
<div class="container">
  <div class="jumbotron">
    <h1>Missing argument in request</h1>
    <p>There was a problem processing: <span style="color: green;"><?= $response['info']['url']; ?></span> as <span style="color: green;"><b><?= $response['info']['method']; ?></b></span> request</p>
    <?php
    if (!empty($response['data']['argument'])) {
    ?>
    <p>The following argument needs to be provided and should not be empty: <b><?= $response['data']['argument']; ?></b></p>
    <?php
    }
    else {
    ?>
    <p>You are missing some required arguments, this request cannot be processed</p>
    <?php
    }
    ?>
  </div>

</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
