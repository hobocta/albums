<?
use Hbc\Album\Db;
use Hbc\Album\Log;

require_once sprintf('%s/../classes/Log.php', dirname(__FILE__));
require_once sprintf('%s/../classes/Db.php', dirname(__FILE__));

require_once sprintf('%s/../config/config.php', dirname(__FILE__));

Log::log('Info: start');

if (empty($config)) {
    Log::log('Error: empty config');
    return;
}

$db = new Db($config['dbFilePath']);

if ($dbAlbums = $db->install()) {
    Log::log('Success');
} else {
    Log::log('Error');
}

Log::log('Info: finish');
