<?php

/**
 * Тест для класса Core_Dump.
 */
class Core_DumpTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var Site_Model
	 */
	protected $site;

	/**
	 * Настройка.
	 *
	 * @return void
	 */
	public function setUp()
	{
		// Инициализируем базовые константы для работы HostCMS
		Testing_Bootstrap::defineConstants();

		// Кастомный конфиг для БД
		Testing_Core_Config::setCustomConfig(array(
			'core_database' => array(
				'default' => array (
					'driver' => 'pdo',
					'host' => 'localhost',
					'username' => 'hostcms',
					'password' => 'hostcms',
					'database' => 'hostcms'
				)
			))
		);

		// Инциализируем ядро
		Testing_Core::init();
	}

	/**
	 * Тестирование основных возможностей дампа.
	 *
	 * @return void
	 */
	public function testDump()
	{
		// Настройки дампера
		Core_Dump::$depth = 3;
		Core_Dump::$stringLength = 32;
		Core_Dump::$arrayWidth = 20;

		$resource = fopen('php://input', 'r');
		$unknown = fopen('php://input', 'r');
		fclose($unknown);

		$object = new StdClass;
		$object->string = 'string';
		$object->integer = 1000;
		$object->boolean = true;
		$object->array = array(
			array(
				$object
			)
		);

		$oModule = Core_Entity::factory('Module');
		$oModule->name = "test module";
		$oModule->path = "test";

		$source = array(
			true,
			500,
			1000.0,
			$resource,
			NULL,
			$unknown,
			'Lorem Ipsum is simply dummy',
			'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
			array(
				array(
					0, 1, 2, 3, 4
				),
				array(
					array(
						0, 1, 2
					)
				)
			),
			$object,
			$oModule
		);

		$expected = <<<EOT
array(11) {
  [0] => true
  [1] => 500
  [2] => 1000
  [3] => [resource]
  [4] => null
  [5] => ???
  [6] => string(27): "Lorem Ipsum is simply dummy"
  [7] => string(74): "Lorem Ipsum is simply dummy text"...
  [8] => array(2) {
    [0] => array(5) {
      [0] => 0
      [1] => 1
      [2] => 2
      [3] => 3
      [4] => 4
    }
    [1] => array(1) {
      [0] => array(3) {...}
    }
  }
  [9] => stdClass#1 {
    [string] => string(6): "string"
    [integer] => 1000
    [boolean] => true
    [array] => array(1) {
      [0] => array(1) {...}
    }
  }
  [10] => Module_Model#2 {
    [id] => null
    [name] => string(11): "test module"
    [description] => null
    [active] => string(1): "1"
    [indexing] => string(1): "1"
    [path] => string(4): "test"
    [sorting] => 0
    [user_id] => 0
    [deleted] => 0
  }
}

EOT;

		$this->assertEquals($expected, Core_Dump::export($source));
	}

	/**
	 * Тестирование ограничения размера массива.
	 *
	 * @return void
	 */
	public function testArrayWidth()
	{
		Core_Dump::$arrayWidth = 5;

		$source = array(
			0, 1, 2, 3, 4, 5, 6, 7, 8, 9
		);

		$expected = <<<EOT
array(10) {
  [0] => 0
  [1] => 1
  [2] => 2
  [3] => 3
  [4] => 4
  ...
}

EOT;

		$this->assertEquals($expected, Core_Dump::export($source));
	}
}