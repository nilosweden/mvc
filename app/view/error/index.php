<?php
    $exception = $data;
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'] ?? '';
    $currentLink = 'http://' . $host . $uri;
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
    <h1>Oops, something went wrong!</h1>
    <p>
        There was a problem processing: <span style="color: green;"><?php write($currentLink); ?></span>
        as <span style="color: green;"><b><?php write($method); ?></b></span> request
    </p>
    <p>
        <b>Message:</b> <?php write($exception->getMessage()); ?>
        <br>
        <b>Exception:</b> <?php write(get_class($exception)); ?>
        <br>
        <b>File:</b> <?php write($exception->getFile()); ?>
        <br>
        <b>Line:</b> <?php write((string)$exception->getLine()); ?>
        <br>
        <br>
        <b>Trace:</b>
        <pre><?php write($exception->getTraceAsString()); ?></pre>
    </p>
  </div>
</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
