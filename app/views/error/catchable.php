<?php
    $responseData = $response->getData();
?>
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
    <h1>Oops, something went wrong</h1>
    <p><?= $responseData['data']['errstr']; ?></p>
    <h2>In file</h2>
    <p><?= $responseData['data']['errfile']; ?></p>
    <h2>On line</h2>
    <p><?= $responseData['data']['errline']; ?></p>
    <p>
      <a class="btn btn-lg btn-primary" href="#jira" role="button">Report this bug in Jira &raquo;</a>
    </p>
  </div>
</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
