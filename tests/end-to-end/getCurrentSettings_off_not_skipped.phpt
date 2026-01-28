--TEST--
getCurrentSettings does not skip settings whose runtime value is an empty string
--INI--
display_errors=1
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', '');

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings(['display_errors']);

var_dump(isset($result['display_errors']));
?>
--EXPECT--
bool(true)
