<?php declare(strict_types=1);
function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function write($string, $encoding='UTF-8')
{
    echo htmlspecialchars($string, ENT_QUOTES, $encoding);
}
