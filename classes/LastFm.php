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

        $url = sprintf(
            '%s?method=%s&api_key=%s&user=%s&format=json',
            self::API_URL,
            $method,
            $this->apiKey,
            $this->login
        );

        $response = json_decode(file_get_contents($url), true);

        return !empty($response['artists']['artist']) ? $response['artists']['artist'] : array();
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

        return !empty($response['topalbums']['album']) ? $response['topalbums']['album'] : array();
    }
}