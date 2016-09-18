<?
use Hbc\Album\Email;
use Hbc\Tools\Log;

require_once sprintf('%s/../classes/bootstrap.php', dirname(__FILE__));

require_once sprintf('%s/../config/config.php', dirname(__FILE__));

if (empty($config)) {
    Log::log('Error: empty config');
    return;
}

$isEmailSent = Email::send(
    $config['email'],
    array('name' => sprintf('Артист %s', uniqid())),
    array('name' => sprintf('Альбом %s', uniqid()))
);

if ($isEmailSent) {
    Log::log('Success: email sent');
} else {
    throw new Exception('Unable to send email');
}
