# Philer

## Installation
Install the latest version with [Composer](https://getcomposer.org). **PHP 7.0** or above is required.
```
$ composer require xy2z/philer
```

## Basic Usage
```php
use xy2z\Tools\Philer;

$philer = new Philer('list.txt');
$philer->write(3.14);
$philer->write(array('a', 'b', 'c'));
echo $philer->read();
/* Result:
3.14
Array
(
    [0] => a
    [1] => b
    [2] => c
)
*/


// Load a new file and change options
$philer->load('new-list.txt');
$philer->set_option('var_dump', true);
$philer->write(3.14);
$philer->write(array('a', 'b', 'c'));
/* Result in 'new-list.txt':
float(3.14)
array(3) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [2]=>
  string(1) "c"
}
*/
```

## Options
Coming soon.
