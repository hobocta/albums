<?
use Hbc\Album\Db;
use Hbc\Album\LastFm;
use Hbc\Album\Log;
use Hbc\Album\Email;

require_once sprintf('%s/../classes/Log.php', dirname(__FILE__));
require_once sprintf('%s/../classes/Db.php', dirname(__FILE__));
require_once sprintf('%s/../classes/Email.php', dirname(__FILE__));
require_once sprintf('%s/../classes/LastFm.php', dirname(__FILE__));

require_once sprintf('%s/../config/config.php', dirname(__FILE__));

Log::log('Info: start');

if (empty($config)) {
    Log::log('Error: empty config');

    return;
}

$api = new LastFm($config['lastFmApiKey'], $config['lastFmLogin']);

$db = new Db($config['dbFilePath']);

$dbAlbums = $db->get();

foreach ($api->getArtists() as $artist) {
    if (!empty($artist['mbid'])) {
        foreach ($api->getAlbums($artist['mbid']) as $album) {
            if (!empty($album['mbid'])) {
                if (
                    isset($dbAlbums[$artist['mbid']])
                    && in_array($album['mbid'], $dbAlbums[$artist['mbid']])
                ) {
                    Log::log(sprintf(
                        'Skip: artist "%s" already have album "%s"',
                        $artist['name'],
                        $album['name']
                    ));
                } else {
                    if ($db->put($artist['mbid'], $album['mbid'])) {
                        Email::send($artist, $album);

                        Log::log(sprintf(
                            'Success: added to artist "%s" new album "%s"',
                            $artist['name'],
                            $album['name']
                        ));
                    } else {
                        Log::log(sprintf(
                            'Error: unable to add to artist "%s" new album "%s"',
                            $artist['name'],
                            $album['name']
                        ));
                    }
                }
            }
        }
    }
}

Log::log('Info: finish');
