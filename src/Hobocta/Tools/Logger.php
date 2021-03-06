<?php
namespace Hobocta\Tools;

final class Logger
{
    public static function log($message)
    {
        echo sprintf('%s %s%s', date('Y.m.d H:i:s'), $message, PHP_EOL);
    }
}
