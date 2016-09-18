<?
namespace Hbc;

final class AutoLoader
{
	private static $lastLoadedFilePath;

	public static function loadPackages($className)
	{
		$pathParts = explode('\\', str_replace('Hbc/', '', str_replace('\\', '/', $className)));
		self::$lastLoadedFilePath = sprintf(
			'%s/%s',
			dirname(__FILE__),
			sprintf('%s.php', implode(DIRECTORY_SEPARATOR, $pathParts))
		);

		require_once(self::$lastLoadedFilePath);
	}

	public static function loadPackagesAndLog($className)
	{
		self::loadPackages($className);
		printf("Class %s was loaded from %s", $className, self::$lastLoadedFilePath);
	}
}
