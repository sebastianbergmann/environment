--TEST--
getCurrentSettings returns empty array when no values are passed
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings([]);

var_dump($result);
?>
--EXPECT--
array(0) {
}
