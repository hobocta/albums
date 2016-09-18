<?
use Hbc\Album\Service;
use Hbc\Tools\Log;

require_once sprintf('%s/../classes/bootstrap.php', dirname(__FILE__));

require_once sprintf('%s/../config/config.php', dirname(__FILE__));

if (empty($config)) {
    Log::log('Error: empty config');
    return;
}

$service = new Service($config);

$service->checkAlbums();
