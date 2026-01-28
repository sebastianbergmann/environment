--TEST--
getCurrentSettings merges multiple INI files and does not produce duplicates
--ENV--
PHP_INI_SCAN_DIR={PWD}/_fixture
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

// display_errors is set to 0 in both INI files and the runtime value
// matches, so it must not appear in the diff
$result = (new SebastianBergmann\Environment\Runtime)->getCurrentSettings([
    'display_errors',
    'log_errors',
    'error_reporting',
]);

// None of these should appear because runtime values match file values
var_dump($result);
?>
--EXPECT--
array(0) {
}
