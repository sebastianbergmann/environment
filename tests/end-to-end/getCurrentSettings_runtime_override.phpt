--TEST--
getCurrentSettings detects setting changed at runtime via ini_set
--INI--
display_errors=0
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', '1');

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings(['display_errors']);

var_dump(isset($result['display_errors']));
?>
--EXPECT--
bool(true)
