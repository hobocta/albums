<?
namespace Hbc\Album;

final class LastFm
{
    private $apiKey = null;
    private $login = null;

    const API_URL = 'http://ws.audioscrobbler.com/2.0/';

    public function __construct($apiKey, $login)
    {
        $this->apiKey = $apiKey;

        $this->login = $login;
    }

    public function getArtists()
    {
        $method = 'library.getartists';

        $artists = array();

        $limit = 1000;

        $page = 1;

        while (!isset($totalPages) || $page <= $totalPages) {
            $url = sprintf(
                '%s?method=%s&api_key=%s&user=%s&limit=%s&page=%s&format=json',
                self::API_URL,
                $method,
                $this->apiKey,
                $this->login,
                $limit,
                $page
            );

            $response = json_decode(file_get_contents($url), true);

            $totalPages = !empty($response['artists']['@attr']['totalPages'])
                ? $response['artists']['@attr']['totalPages']
                : $page;

            $page++;

            if (!empty($response['artists']['artist'])) {
                foreach ($response['artists']['artist'] as $artist) {
                    if (!empty($artist['mbid']) && !isset($artists[$artist['mbid']])) {
                        $artists[$artist['mbid']] = array(
                            'name' => $artist['name'],
                        );
                    }
                }
            }
        }

        return $artists;
    }

    public function getAlbums($artistId)
    {
        $method = 'artist.getTopAlbums';

        $url = sprintf(
            '%s?method=%s&api_key=%s&mbid=%s&format=json',
            self::API_URL,
            $method,
            $this->apiKey,
            $artistId
        );

        $response = json_decode(file_get_contents($url), true);

        $albums = array();

        if (!empty($response['topalbums']['album'])) {
            foreach ($response['topalbums']['album'] as $album) {
                if (!empty($album['mbid']) && !isset($albums[$album['mbid']])) {
                    $albums[$album['mbid']] = array(
                        'name' => $album['name'],
                    );
                }
            }
        }

        return $albums;
    }
}
