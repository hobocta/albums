<?
namespace Hbc;

final class AutoLoader
{
	private static $lastLoadedFilePath;

	public static function loadPackages($className)
	{
		$pathParts = explode('\\', str_replace('Hbc/', '', str_replace('\\', '/', $className)));
		static::$lastLoadedFilePath = sprintf(
			'%s/%s',
			dirname(__FILE__),
			sprintf('%s.php', implode(DIRECTORY_SEPARATOR, $pathParts))
		);

		require_once(static::$lastLoadedFilePath);
	}

	public static function loadPackagesAndLog($className)
	{
		static::loadPackages($className);
		printf("Class %s was loaded from %s", $className, static::$lastLoadedFilePath);
	}
}
