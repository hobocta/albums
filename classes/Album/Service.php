<?
namespace Hbc\Album;

use Exception;

use Hbc\Tools\Log;

final class Service
{
    var $config = array();

    var $api = null;

    var $db = null;

    var $dbAlbums = array();

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->api = new LastFm($this->config['lastFmApiKey']);

        $this->db = new Db($this->config['dbFilePath']);
    }

    public function checkAlbums()
    {
        Log::log('Info: start');
        
        $this->dbAlbums = $this->db->get();

        Log::log(sprintf('Info: from db loaded artists count: %s', count($this->dbAlbums)));

        $artists = $this->api->getArtists(
            $this->config['lastFmUser'],
            $this->config['artistsLimit']
        );

        Log::log(sprintf('Info: from api loaded artists count: %s', count($artists)));

        foreach ($artists as $artistId => $artist) {
            $this->processArtist($artistId, $artist);
        }

        Log::log('Info: finish');
    }

    private function processArtist($artistId, $artist)
    {
        foreach ($this->api->getAlbums($artistId) as $albumId => $album) {
            $this->processAlbum($artistId, $artist, $albumId, $album);
        }
    }

    private function processAlbum($artistId, $artist, $albumId, $album)
    {
        if (
            isset($this->dbAlbums[$artistId])
            && in_array($albumId, $this->dbAlbums[$artistId])
        ) {
            Log::log(sprintf(
                'Skip: artist "%s" already have album "%s"',
                $artist['name'],
                $album['name']
            ));
        } else {
            if ($this->db->put($artistId, $albumId)) {
                if (!Email::send($this->config['email'], $artist, $album)) {
                    throw new Exception('Unable to send email');
                }

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
