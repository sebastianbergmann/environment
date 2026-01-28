--TEST--
getCurrentSettings escapes backslashes in values containing special characters
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

ini_set('error_prepend_string', 'a\\b');

$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings(['error_prepend_string']);

print $result['error_prepend_string'];
?>
--EXPECT--
error_prepend_string="a\\b"
