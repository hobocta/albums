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
		try {
			$albums = $this->api->getAlbums($artistId);
		} catch(Exception $e) {
			Log::log($e->getMessage());
		}

		if (isset($albums)) {
			$skippedAlbums = array();

			foreach ($albums as $albumId => $album) {
				$result = $this->processAlbum($artistId, $artist, $albumId, $album);

				if (!is_null($result['skip'])) {
					$skippedAlbums[] = $result['skip'];
				}
			}

			if (!empty($skippedAlbums)) {
				Log::log(sprintf(
					'Skip: artist "%s" already have albums count: %s',
					$artist['name'],
					count($skippedAlbums)
				));
			}
		}
	}

	private function processAlbum($artistId, $artist, $albumId, $album)
	{
		$result = array('skip' => null);

		if (
			isset($this->dbAlbums[$artistId])
			&& in_array($albumId, $this->dbAlbums[$artistId])
		) {
			$result['skip'] = $album['name'];
		} else {
			$isEmail = !empty($this->config['email']);
			$isEmailSent = false;

			if ($isEmail) {
				$isEmailSent = Email::send($this->config['email'], $artist, $album);

				if ($isEmailSent) {
					Log::log(sprintf(
						'Success: artist "%s" new album "%s" email sent',
						$artist['name'],
						$album['name']
					));
				} else {
					throw new Exception('Unable to send email');
				}
			}

			if (!$isEmail || $isEmailSent) {
				if ($this->db->put($artistId, $albumId)) {
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

		return $result;
	}
}
