<?
namespace Hbc\Album;

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

        $this->api = new LastFm($this->config['lastFmApiKey'], $this->config['lastFmLogin']);

        $this->db = new Db($this->config['dbFilePath']);
    }

    public function checkAlbums()
    {
        Log::log('Info: start');
        
        $this->dbAlbums = $this->db->get();

        $artistsCount = 0;

        foreach ($this->api->getArtists() as $artistId => $artist) {
            if (++$artistsCount <= $this->config['artistsLimit']) {
                $this->processArtist($artistId, $artist);
            }
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
                Email::send($this->config['email'], $artist, $album);

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
