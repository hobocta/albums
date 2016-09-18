<?
namespace Hbc\Album;

use Exception;

final class LastFm
{
	private $apiKey = null;

	const API_URL = 'http://ws.audioscrobbler.com/2.0/';

	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
	}

	public function getArtists($user, $limit)
	{
		$artists = array();

		$page = 1;

		while (
			count($artists) < $limit
			&& (
				!isset($totalPages)
				|| $page <= $totalPages
			)
		) {
			$url = $this->getUrl('library.getartists', array(
				'user' => $user,
				'page' => $page,
			));

			$response = $this->request($url);

			if (!isset($response['artists']['artist'])) {
				throw new Exception('Empty artists list in response');
			}

			if (empty($response['artists']['@attr']['totalPages'])) {
				throw new Exception('Empty "totalPages" key in response');
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

		if (count($artists) > $limit) {
			$artists = array_slice($artists, 0, $limit);
		}

		return $artists;
	}

	public function getAlbums($artistId)
	{
		$url = $this->getUrl('artist.getTopAlbums', array('mbid' => $artistId));

		$response = $this->request($url);

		if (!empty($response['error'])) {
			throw new Exception(sprintf(
				'Warning: error="%s", message="%s"',
				$response['error'],
				$response['message']
			));
		}

		if (!isset($response['topalbums']['album'])) {
			throw new Exception('Empty albums list in response');
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
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		curl_close($curl);

		$response = json_decode($response, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('Response is not json format');
		}

		return $response;
	}

	private function getUrl($method, array $params = array())
	{
		$params = array_merge($params, array('format' => 'json'));

		$url = sprintf(
			'%s?method=%s&api_key=%s',
			self::API_URL,
			$method,
			$this->apiKey
		);

		foreach ($params as $key => $value) {
			$url .= sprintf('&%s=%s', $key, $value);
		}

		return $url;
	}
}
