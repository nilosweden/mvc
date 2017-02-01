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
    <h1>Unnecessary arguments</h1>
    <p>
        There was a problem processing:
        <span style="color: green;">
            <?php write($responseData['info']['url']); ?></span> as <span style="color: green;"><b><?php write($responseData['info']['method']); ?></b>
        </span> request
    </p>
    <p>
        The following arguments is not needed when doing the request:
        <b><?php write(implode(', ', array_keys($responseData['data']['arguments']))); ?></b>
    </p>
  </div>

</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
