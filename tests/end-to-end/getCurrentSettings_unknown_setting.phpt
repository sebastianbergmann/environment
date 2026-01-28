--TEST--
getCurrentSettings returns empty array for nonexistent setting
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings(['nonexistent_setting_xyz']);

var_dump($result);
?>
--EXPECT--
array(0) {
}
