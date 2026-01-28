--TEST--
getCurrentSettings returns empty array when setting is not changed at runtime
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings(['display_errors']);

var_dump($result);
?>
--EXPECT--
array(0) {
}
