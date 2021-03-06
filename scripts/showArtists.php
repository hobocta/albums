<?php
use Hobocta\Album\Service;
use Hobocta\Tools\Logger;

/** @noinspection PhpIncludeInspection */
require_once sprintf('%s/../src/Hobocta/bootstrap.php', dirname(__FILE__));

/** @noinspection PhpIncludeInspection */
$config = require sprintf('%s/../config/main.php', dirname(__FILE__));

if (empty($config) || !is_array($config)) {
    Logger::log('Error: empty config');
    return;
}

$service = new Service($config);

try {
    $service->showArtists();
} catch (Exception $e) {
    echo $e->getMessage();
}
