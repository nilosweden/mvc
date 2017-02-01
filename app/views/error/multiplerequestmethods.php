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
    <h1>Multiple request methods</h1>
    <p>There was a problem processing: <span style="color: green;"><?php write($responseData['info']['url']); ?></span> as <span style="color: green;"><b><?php write($responseData['info']['method']); ?></b></span> request</p>
    <p>
    We are unable to handle this request due to multiple request methods or the way the arguments was sent.<br>
    This problem is due to one of the following reasons since this is a <?php write(mb_strtolower($responseData['info']['method'])); ?> request:
    </p>
<?php
    if ($responseData['info']['method'] == 'GET') {
?>
    <ul>
        <li>Arguments can be sent either with /controller/method/arg1/arg2 etc...</li>
        <li>or by named arguments: /controller/method/?arg1=something&amp;arg2=something etc...</li>
    </ul>
    <p>You are not allowed to use both methods of sending arguments!</p>
<?php
    }
    else {
?>
    <ul>
        <li>You sent some data as GET request, example: /controller/method/arg1 and some data as POST</li>
    </ul>
    <p>You should either send all arguments with POST request or GET request!</p>
<?php
    }
?>
  </div>
</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
