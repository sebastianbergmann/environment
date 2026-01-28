--TEST--
getCurrentSettings does not quote plain alphanumeric values
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', '1');

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings(['display_errors']);

var_dump($result['display_errors']);
?>
--EXPECT--
string(16) "display_errors=1"
