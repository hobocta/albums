<?
namespace Hobocta\Tools;

final class Env
{
	public static function getRootDir()
	{
		return sprintf('%s/../..', dirname(__FILE__));
	}
}
