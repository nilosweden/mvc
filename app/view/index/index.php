<?php
core\Session::set('test', 'randomKey');
function curl_del($apiurl, $type, $args)
{
    $host = $_SERVER['HTTP_HOST'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . $host . $apiurl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($type));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
$type = $_GET['type'] ?? '';
$args = $_GET['args'] ?? '';
$apiurl = $_GET['apiurl'] ?? '';
$result = '';
if ($type != '') {
    $result = curl_del($apiurl, $type, $args);
}
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
    <h1>Make POST request</h1>
    <p>URL: /mvc/userapi/add</p>
    <form method="post" action="/mvc/userapi/add">
    <label>Arg1:</label>
    <input type="text" name="arg1" placeholder="some values">
    <br>
    <label>Arg2:</label>
    <input type="text" name="arg2" placeholder="json array or object" value='["key1", "key2", 15]'>
    <br>
    <input type="submit">
    </form>
  </div>
  <div class="jumbotron">
    <h1>Make CUSTOM request</h1>
    <form method="get" action="?">
    <label>URL:</label>
    <input type="text" name="apiurl" placeholder="url to api" value="/mvc/userapi/remove">
    <br>
    <label>Request type:</label>
    <select name="type">
    <option value="DELETE">DELETE</option>
    <option value="PUT">PUT</option>
    <option value="POST">POST</option>
    <option value="SOMETHING">SOMETHING</option>
    </select>
    <br>
    <label>Arguments:</label>
    <input type="text" name="args" placeholder="arguments">
    <br>
    <input type="submit">
    </form>
  </div>
  <div class="jumbotron">
  <?= $result; ?>
  </div>
</div>
<script src="/mvc/lib/jquery/jquery.min.js"></script>
<script src="/mvc/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
