<?php
use Hobocta\Album\Email;
use Hobocta\Tools\Logger;

/** @noinspection PhpIncludeInspection */
require_once sprintf('%s/../src/Hobocta/bootstrap.php', dirname(__FILE__));

/** @noinspection PhpIncludeInspection */
$config = require sprintf('%s/../config/main.php', dirname(__FILE__));

if (empty($config) || !is_array($config)) {
    Logger::log('Error: empty config');
    return;
}

$isEmailSent = Email::send(
    $config['email'],
    array('name' => sprintf('Артист %s', uniqid())),
    array('name' => sprintf('Альбом %s', uniqid()))
);

if ($isEmailSent) {
    Logger::log('Success: email sent');
} else {
    throw new Exception('Unable to send email');
}
