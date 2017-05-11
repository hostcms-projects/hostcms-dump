<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Класс для отладки.
 */
class Core_Dump
{
	/**
	 * Обертка для вывода содержимого переменных.
	 *
	 * @return void
	 */
	static public function d()
	{
		if (!Core_Auth::logged())
		{
			return;
		}

		self::_dump(func_get_args());
	}

	/**
	 * Обертка для вывода содержимого переменных и завершения работы.
	 *
	 * @return void
	 */
	static public function dd()
	{
		if (!Core_Auth::logged())
		{
			return;
		}

		self::_dump(func_get_args());
		exit;
	}

	/**
	 * Выводит содержимое переменных.
	 *
	 * @param  array  $aVariables
	 * @return void
	 */
	static protected function _dump($aVariables)
	{
		print '<pre>';

		foreach ($aVariables as $variable)
		{
			var_dump($variable);
		}

		print '</pre>';
	}
}