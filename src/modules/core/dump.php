<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Класс для отладки.
 */
class Core_Dump
{
	/**
	 * 
	 */
	static public $depth = 3;

	/**
	 * 
	 */
	static public $protected = TRUE;

	/**
	 * 
	 */
	static public $private = TRUE;

	/**
	 * 
	 */
	static public $stringLength = 128;

	/**
	 * 
	 */
	static public $arrayWidth = 5;

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
	 * @see https://www.leaseweb.com/labs/2013/10/smart-alternative-phps-var_dump-function/
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
				$output .= '';
			break;

			case 'unknown type':
				$output .= '???';
			break;

			case 'string':
				$length = strlen($variable);

				// TODO: замена непечатаемых символов на печатаемые

				if ($length > self::$stringLength)
				{
					$string = substr($variable, 0, self::$stringLength);
					$string = '"' . $string . '"...';
				}
				else
				{
					$string = '"' . $variable . '"';
				}

				$output .= 'string(' . $length . '): ' . $string;
			break;

			case 'array':
				$length = count($variable);

				if (!$length)
				{
					$array = '';
				}
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

				if ($objectId !== FALSE)
				{
					$output .= get_class($variable) . '#' . ($objectId + 1).' {...}';
				}
				elseif ($level == self::$depth)
				{
					$output .= get_class($variable).' {...}';
				}
				else
				{
					$objectId = array_push($aObjects, $variable);

					$array = (array) $variable;
					$tabs = str_repeat(' ', $level * 2);

					$output .= get_class($variable) . "#$objectId\n" . $tabs . '{';
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
}