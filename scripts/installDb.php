<?php
use Hobocta\Album\Db;
use Hobocta\Tools\Logger;

/** @noinspection PhpIncludeInspection */
require_once sprintf('%s/../src/Hobocta/bootstrap.php', dirname(__FILE__));

/** @noinspection PhpIncludeInspection */
$config = require sprintf('%s/../config/main.php', dirname(__FILE__));

Logger::log('Info: start');

if (empty($config) || !is_array($config)) {
    Logger::log('Error: empty config');
    return;
}

$db = new Db($config['dbFilePath']);

if ($dbAlbums = $db->install()) {
    Logger::log('Success');
} else {
    Logger::log('Error');
}

Logger::log('Info: finish');
