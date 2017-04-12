<?
namespace Hobocta\Album;

final class Email
{
	public static function send($email, array $artist, array $album)
	{
		$searchString = urlencode(sprintf('%s %s', $artist['name'], $album['name']));

		$links = array(
			sprintf('https://rutracker.org/forum/tracker.php?nm=%s', $searchString),
			sprintf('https://www.google.ru/#newwindow=1&q=%s', $searchString),
		);

		$mail = array(
			'headers' => sprintf('From: %s', $email),
			'title'   => sprintf('Новый альбом: %s - %s', $artist['name'], $album['name']),
			'body'    => implode(PHP_EOL, $links),
		);

		return mail($email, $mail['title'], $mail['body'], $mail['headers']);
	}
}
