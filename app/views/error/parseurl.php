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
    <h1>Error parsing url</h1>
    <p>A error occurred when trying to parse the following URL:<br><span style="color: green;"><?= urldecode($response['info']['url']); ?></span></p>
    <br>
    <h2>This part of the url is causing this error</h2>
    <p><span style="color: green;"><?= $response['data']['arguments']; ?></span></p>
    <?php
    if (!empty($response['data']['message'])) {
        echo '<p>Error message: ' . $response['data']['message'] . '</p>';
    }
    else {
        echo '<p>This should be valid json data, are you missing a bracket?</p>';
    }
    ?>
    <br>
    <p>
      <a class="btn btn-lg btn-primary" href="#jira" role="button">Report this bug in Jira &raquo;</a>
    </p>
  </div>

</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
