<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Класс для отладки.
 */
class Core_Dump
{
	/**
	 * Глубина вывода переменных.
	 *
	 * @var integer
	 */
	static public $depth = 3;

	/**
	 * Количество выводимых символов в строках.
	 *
	 * @var integer
	 */
	static public $stringLength = 128;

	/**
	 * Количество выводимых элементов в массиве.
	 *
	 * @var integer
	 */
	static public $arrayWidth = 5;

	/**
	 * Обертка для вывода содержимого переменных.
	 *
	 * @return void
	 */
	static public function dump()
	{
		if (!Core_Auth::logged())
		{
			return;
		}

		self::_dumpVars(func_get_args());
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

		self::_dumpVars(func_get_args());
		exit;
	}

	/**
	 * Возвращает дамп переменной.
	 *
	 * @param  mixed  $variable
	 * @return string
	 */
	static public function export($variable)
	{
		return self::_getDumpVar($variable);
	}

	/**
	 * Выводит содержимое переменных.
	 *
	 * @param  array  $aVariables
	 * @return void
	 */
	static protected function _dumpVars($aVariables)
	{
		$aObjects = array();

		print '<pre>';

		foreach ($aVariables as $variable)
		{
			print self::_getDumpVar($variable, 0, $aObjects);
		}

		print '</pre>';
	}

	/**
	 * 
	 *
	 * @see https://www.leaseweb.com/labs/2013/10/smart-alternative-phps-var_dump-function/
	 *
	 * @param  mixed  $variable
	 * @param  integer  $level
	 * @param  array  $aObjects
	 * @return string
	 */
	static protected function _getDumpVar($variable, $level = 0, &$aObjects = array())
	{
		$output = '';

		switch (gettype($variable))
		{
			case 'boolean':
				$output .= $variable ? 'true' : 'false';
			break;

			case 'integer':
			case 'double':
				$output .= $variable;
			break;

			case 'resource':
				$output .= '[resource]';
			break;

			case 'NULL':
				$output .= 'null';
			break;

			case 'unknown type':
				$output .= '???';
			break;

			case 'string':
				$length = strlen($variable);

				// Обрезаем строку до максимальной длины
				$string = substr($variable, 0, self::$stringLength);
				
				// Замена непечатаемых символов на печатаемые
				$string = str_replace(
					array("\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v"),
					array('\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v'),
					$string
				);

				$output .= 'string(' . $length . '): "' . $string . '"';

				// Добавляем признак обрезанной строки
				if ($length > self::$stringLength)
				{
					$output .= '...';
				}
			break;

			case 'array':
				$length = count($variable);

				// Массив пустой
				if (!$length)
				{
					$array = '';
				}
				// Преодолена максимальная вложенность
				elseif ($level == self::$depth)
				{
					$array  = '...';
				}
				else
				{
					$array = '';
					$tabs = str_repeat(' ', $level * 2);
					$countKeys = 0;

					foreach ($variable as $key => $value)
					{
						// Образем массив до максимального количества элементов
						if ($countKeys == self::$arrayWidth)
						{
							$array .= "\n" . $tabs . '  ...';
							break;
						}

						$array .=  "\n" . $tabs . '  [' . $key . '] => ';
						$array .=  self::_getDumpVar($value, $level + 1, $aObjects);
						$countKeys++;
					}

					$array .= "\n" . $tabs;
				}

				$output .= 'array(' . $length . ') {' . $array . '}';
			break;

			case 'object':
				$objectId = array_search($variable, $aObjects);

				// Если объект выводился ранее, то не раскрываем его 
				if ($objectId !== FALSE)
				{
					$output .= get_class($variable) . '#' . ($objectId + 1).' {...}';
				}
				// Преодолена максимальная вложенность
				elseif ($level == self::$depth)
				{
					$output .= get_class($variable) . ' {...}';
				}
				else
				{
					$objectId = array_push($aObjects, $variable);

					$tabs = str_repeat(' ', $level * 2);
					$output .= get_class($variable) . "#$objectId {";

					$array = self::_getObjectProperties($variable);
					$aProperties = array_keys($array);

					foreach ($aProperties as $property)
					{
						$output .= "\n" . $tabs . "  [$property] => ";
						$output .= self::_getDumpVar($array[$property], $level + 1, $aObjects);
					}

					$output .= "\n" . $tabs . '}';
				}
			break;
		}

		if ($level == 0)
		{
			$output .= "\n";
		}

		return $output;
	}

	/**
	 * Возвращет свойства объекта.
	 *
	 * @param  object  $object
	 * @return void
	 */
	static protected function _getObjectProperties($object)
	{
		if ($object instanceof Core_Orm)
		{
			return $object->toArray();
		}
		else
		{
			return (array) $object;
		}
	}
}