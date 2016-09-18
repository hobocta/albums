<?
namespace Hbc\Album;

final class Email
{
	public static function send($email, array $artist, array $album)
	{
		$headers = sprintf('From: %s', $email);

		return mail(
			$email,
			sprintf('Новый альбом: %s - %s', $artist['name'], $album['name']),
			sprintf(
				'https://rutracker.org/forum/tracker.php?nm=%s',
				urlencode(sprintf('%s %s', $artist['name'], $album['name']))
			),
			$headers
		);
	}
}
