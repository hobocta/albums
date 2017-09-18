<?
use Hobocta\Album\Db;
use Hobocta\Tools\Log;

/** @noinspection PhpIncludeInspection */
require_once sprintf('%s/../src/Hobocta/bootstrap.php', dirname(__FILE__));

/** @noinspection PhpIncludeInspection */
$config = require sprintf('%s/../config/main.php', dirname(__FILE__));

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
