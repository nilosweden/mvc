<?php
function write($string, $encoding='UTF-8')
{
    echo htmlspecialchars($string, ENT_QUOTES, $encoding);
}

function backtrace(Exception $e, array $seen=[])
{
    $starter = '';
    $result = [];

    if ($seen) {
        $starter = 'Caused by: ';
    }

    $trace = $e->getTrace();
    $prev = $e->getPrevious();
    $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
    $file = $e->getFile();
    $line = $e->getLine();

    while (true) {
        $current = "$file:$line";
        if (in_array($current, $seen)) {
            $result[] = sprintf(' ... %d more', count($trace) + 1);
            break;
        }
        $class = '';
        $method = '';
        $function = '(main)';

        if (count($trace) && array_key_exists('class', $trace[0])) {
            $class = $trace[0]['class'];
        }

        if (count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0])) {
            $method = $trace[0]['type'];
        }

        if (count($trace) && array_key_exists('function', $trace[0])) {
            $function = $trace[0]['function'];
        }

        $result[] = sprintf(
            '    at %s%s%s (%s%s%s)',
            $class,
            $method,
            $function,
            $line === null ? $file : basename($file),
            $line === null ? '' : ':',
            $line === null ? '' : $line
        );

        $seen[] = "$file:$line";
        if (!count($trace)) {
            break;
        }

        $file = 'Unknown source';
        if (array_key_exists('file', $trace[0])) {
            $file = $trace[0]['file'];
        }

        $line = null;
        if (array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line']) {
            $line = $trace[0]['line'];
        }

        array_shift($trace);
    }

    $result = join("\n", $result);

    if ($prev)
    {
        $result .= "\n" . backtrace($prev, $seen);
    }

    return $result;
}
