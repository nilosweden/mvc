<?php
function write($string, $encoding='UTF-8')
{
    echo htmlspecialchars($string, ENT_QUOTES, $encoding);
}
