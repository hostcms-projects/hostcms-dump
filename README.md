# hostcms-dump

В отличие от стандартной функции `var_dump` представляет содержимое переменных в читаемом виде, дамп выводится в тегах `<pre />`, дополнительно выводится длина строк и массивов, объемные переменные обрезаются, для моделей HostCMS выводится только набор свойста.

Дамп переменных выводится только для авторизованных в ЦА пользователей, обычные посетители сайта дамп не увидят.

Пример использования:

```
// Выводит дамп переменных
Core_Dump::dump($source1, $source2); 

// Выводит дамп переменных и завершает работу
Core_Dump::dd($source1, $source2);

// Возвращает дамп переменной
$varExport = Core_Dump::export($source1);
```

Пример дампа: 
```
array(12) {
  [0] => true
  [1] => 500
  [2] => 1000
  [3] => [resource]
  [4] => null
  [5] => ???
  [6] => string(27): "Lorem Ipsum is simply dummy"
  [7] => string(74): "Lorem Ipsum is simply dummy text of"...
  [8] => string(77): "Lorem\\n\\r Ipsum is simply dummy\\t text"...
  [9] => array(2) {
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
  [10] => stdClass#1 {
    [string] => string(6): "string"
    [integer] => 1000
    [boolean] => true
    [array] => array(1) {
      [0] => array(1) {...}
    }
  }
  [11] => Module_Model#2 {
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
```

## Тесты

Для запуска тестов:

```$ vendor/bin/phpunit --test-suffix="test.php" tests/```