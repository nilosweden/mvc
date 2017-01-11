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
    <h1>404 - <?= $response['data']['message']; ?></h1>
    <p>There was a problem processing: <span style="color: green;"><?= $response['info']['url']; ?></span> as <span style="color: green;"><b><?= $response['info']['method']; ?></b></span> request</p>
    <p>This controller/method does not exist: <b><?= htmlspecialchars($response['data']['method'], ENT_QUOTES, 'utf-8'); ?></b></p>
    <p>Check the URL and try again. If you think this url should work, report it as a bug to jira.</p>
    <p>
      <a class="btn btn-lg btn-primary" href="#jira" role="button">Report this bug in Jira &raquo;</a>
    </p>
  </div>

</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
