<?
namespace Hbc\Album;

use Exception;

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

            $response = $this->request($url);

            if (
                empty($response['artists']['artist'])
                || empty($response['artists']['@attr']['totalPages'])
            ) {
                throw new Exception('Empty response');
            }

            $totalPages = !empty($response['artists']['@attr']['totalPages'])
                ? $response['artists']['@attr']['totalPages']
                : $page;

            $page++;

            foreach ($response['artists']['artist'] as $artist) {
                if (!empty($artist['mbid']) && !isset($artists[$artist['mbid']])) {
                    $artists[$artist['mbid']] = array(
                        'name' => $artist['name'],
                    );
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

        $response = $this->request($url);

        if (empty($response['topalbums']['album'])) {
            throw new Exception('Empty response');
        }

        $albums = array();

        foreach ($response['topalbums']['album'] as $album) {
            if (!empty($album['mbid']) && !isset($albums[$album['mbid']])) {
                $albums[$album['mbid']] = array(
                    'name' => $album['name'],
                );
            }
        }

        return $albums;
    }

    private function request($url)
    {
        $response = json_decode(file_get_contents($url), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Response is not json format');
        }

        return $response;
    }
}
