<?
namespace Hbc\Album;

use SQLite3;

final class Db
{
	var $db = null;

	public function __construct($dbPath)
	{
		$this->db = new SQLite3($this->getDbPath($dbPath));
	}

	private function getDbPath($dbPath)
	{
		if (file_exists(dirname($dbPath))) {
			return $dbPath;
		} else {
			return sprintf('%s/../../%s', dirname(__FILE__), $dbPath);
		}
	}

	public function install()
	{
		return $this->db->exec(
			'CREATE TABLE IF NOT EXISTS albums (artist TEXT, album TEXT, createAt INTEGER)'
		);
	}

	public function get()
	{
		$result = $this->db->query('SELECT * FROM albums');

		$albums = array();

		while ($row = $result->fetchArray()) {
			if (!isset($albums[$row['artist']])) {
				$albums[$row['artist']] = array();
			}

			$albums[$row['artist']][] = $row['album'];
		}

		return $albums;
	}

	public function put($artistId, $albumId)
	{
		return $this->db->exec(sprintf(
			"INSERT INTO albums (artist, album, createAt) VALUES ('%s', '%s', %s)",
			$artistId,
			$albumId,
			time()
		));
	}
}
