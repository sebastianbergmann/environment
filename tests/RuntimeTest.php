<?php declare(strict_types=1);
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Environment;

use const PHP_BINARY;
use const PHP_SAPI;
use const PHP_VERSION;
use function assert;
use function dirname;
use function extension_loaded;
use function in_array;
use function is_array;
use function json_decode;
use function proc_close;
use function proc_open;
use function sprintf;
use function stream_get_contents;
use function var_export;
use function xdebug_info;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[CoversClass(Runtime::class)]
final class RuntimeTest extends TestCase
{
    public function testCannotCollectCodeCoverageWhenNeitherXdebugNorPcovAreAvailable(): void
    {
        $this->markTestSkippedWhenXdebugCanCollectCoverage();
        $this->markTestSkippedWhenPcovIsLoaded();
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertFalse((new Runtime)->canCollectCodeCoverage());
    }

    public function testCanCollectCodeCoverageWhenXdebugCanCollectCoverage(): void
    {
        $this->markTestSkippedWhenXdebugCannotCollectCoverage();
        $this->markTestSkippedWhenPcovIsLoaded();
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->canCollectCodeCoverage());
    }

    public function testCanCollectCodeCoverageWhenPcovCanCollectCoverage(): void
    {
        $this->markTestSkippedWhenPcovIsNotLoaded();
        $this->markTestSkippedWhenXdebugCanCollectCoverage();
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->canCollectCodeCoverage());
    }

    public function testCanCollectCodeCoverageWhenRunningOnPhpdbg(): void
    {
        $this->markTestSkippedWhenNotRunningOnPhpdbg();
        $this->markTestSkippedWhenXdebugCanCollectCoverage();
        $this->markTestSkippedWhenPcovIsLoaded();

        $this->assertTrue((new Runtime)->canCollectCodeCoverage());
    }

    public function testBinaryCanBeRetrieved(): void
    {
        $this->assertNotEmpty((new Runtime)->getBinary());
    }

    public function testRawBinaryCanBeRetrieved(): void
    {
        $this->assertNotEmpty((new Runtime)->getRawBinary());
    }

    public function testIsPhpReturnsTrueWhenRunningOnPhp(): void
    {
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->isPHP());
    }

    #[RequiresPhpExtension('pcov')]
    public function testPCOVCanBeDetected(): void
    {
        $this->assertTrue((new Runtime)->hasPCOV());
    }

    public function testPhpdbgCanBeDetected(): void
    {
        $this->markTestSkippedWhenNotRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->hasPHPDBGCodeCoverage());
    }

    #[RequiresPhpExtension('xdebug')]
    public function testXdebugCanBeDetected(): void
    {
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->hasXdebug());
    }

    public function testNameAndVersionCanBeRetrieved(): void
    {
        $this->assertNotEmpty((new Runtime)->getNameWithVersion());
    }

    public function testGetNameReturnsPhpdbgWhenRunningOnPhpdbg(): void
    {
        $this->markTestSkippedWhenNotRunningOnPhpdbg();

        $this->assertSame('PHPDBG', (new Runtime)->getName());
    }

    public function testGetNameReturnsPhpdbgWhenRunningOnPhp(): void
    {
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertSame('PHP', (new Runtime)->getName());
    }

    public function testNameAndCodeCoverageDriverCanBeRetrieved(): void
    {
        $this->assertNotEmpty((new Runtime)->getNameWithVersionAndCodeCoverageDriver());
    }

    public function testGetVersionReturnsPhpVersionWhenRunningPhp(): void
    {
        $this->assertSame(PHP_VERSION, (new Runtime)->getVersion());
    }

    public function testGetVendorUrlReturnsPhpDotNetWhenRunningPhp(): void
    {
        $this->assertSame('https://www.php.net/', (new Runtime)->getVendorUrl());
    }

    public function testSettingsChangedViaCliDFlagAreDetected(): void
    {
        $stdout = $this->runChildPhp(
            'disable_functions=phpinfo',
            'echo json_encode((new SebastianBergmann\Environment\Runtime)->getSettingsNotChangeableAtRuntime());',
        );

        $result = json_decode($stdout, true);

        assert(is_array($result));
        assert(isset($result['disable_functions']));

        $this->assertSame('disable_functions=phpinfo', $result['disable_functions']);
    }

    public function testGetCurrentSettingsReturnsEmptyDiffIfNoValuesArePassed(): void
    {
        $this->assertSame([], (new Runtime)->getCurrentSettings([]));
    }

    public function testGetCurrentSettingsWillSkipSettingsThatIsNotSet(): void
    {
        $this->assertSame([], (new Runtime)->getCurrentSettings(['allow_url_include']));
    }

    public function testGetCurrentSettingsReturnsValuesVerbatim(): void
    {
        $value = 'phpstorm://open?file=%f&line=%l';

        $stdout = $this->runChildPhp(
            'error_log="' . $value . '"',
            'echo json_encode((new SebastianBergmann\Environment\Runtime)->getCurrentSettings(["error_log"]));',
        );

        $result = json_decode($stdout, true);

        assert(is_array($result));
        assert(isset($result['error_log']));

        $this->assertSame('error_log=' . $value, $result['error_log']);
    }

    public function testGetCurrentSettingsSkipsValuesThatEqualTheCompiledInDefaultWhenNoIniFileIsLoaded(): void
    {
        $stdout = $this->runChildPhpWithFlags(
            ['-n'],
            'echo json_encode((new SebastianBergmann\Environment\Runtime)->getCurrentSettings(["precision"]));',
        );

        $result = json_decode($stdout, true);

        assert(is_array($result));

        $this->assertArrayNotHasKey('precision', $result);
    }

    public function testGetCurrentSettingsReportsEmptyValueThatDiffersFromTheCompiledInDefault(): void
    {
        $stdout = $this->runChildPhpWithFlags(
            ['-n', '-d', 'display_errors=Off'],
            'echo json_encode((new SebastianBergmann\Environment\Runtime)->getCurrentSettings(["display_errors"]));',
        );

        $result = json_decode($stdout, true);

        assert(is_array($result));

        $this->assertSame('display_errors=', $result['display_errors'] ?? null);
    }

    #[Ticket('https://github.com/sebastianbergmann/environment/issues/99')]
    public function testGetCurrentSettingsLeavesAloneEmptyValueAbsentFromIniFilesAndCompiledInDefaults(): void
    {
        // A setting whose runtime value is the empty string but that is absent from both the
        // loaded ini files and the compiled-in defaults (e.g. an ini setting of an extension
        // loaded only via php.ini, which the `php -n` probe does not load) must not be
        // forwarded as an empty `-d key=` override to child processes.
        $stdout = $this->runChildPhpWithFlags(
            ['-n', '-d', 'error_log='],
            '$property = new ReflectionProperty(SebastianBergmann\Environment\Runtime::class, "compiledDefaults");' .
            '$property->setValue(null, ["precision" => "14"]);' .
            'echo json_encode((new SebastianBergmann\Environment\Runtime)->getCurrentSettings(["error_log"]));',
        );

        $result = json_decode($stdout, true);

        assert(is_array($result));

        $this->assertArrayNotHasKey('error_log', $result);
    }

    public function testCompiledInDefaultsAreCachedAcrossInstances(): void
    {
        $property = new ReflectionProperty(Runtime::class, 'compiledDefaults');
        $original = $property->getValue();

        try {
            $property->setValue(null, null);

            (new Runtime)->getCurrentSettings(['precision']);

            $afterFirstCall = $property->getValue();

            $this->assertIsArray($afterFirstCall);
            $this->assertNotEmpty($afterFirstCall);

            $sentinel = ['__sentinel__' => 'cached'];
            $property->setValue(null, $sentinel);

            (new Runtime)->getCurrentSettings(['precision']);

            $this->assertSame($sentinel, $property->getValue());
        } finally {
            $property->setValue(null, $original);
        }
    }

    private function runChildPhp(string $iniOverride, string $code): string
    {
        return $this->runChildPhpWithFlags(['-d', $iniOverride], $code);
    }

    /**
     * @param list<string> $flags
     */
    private function runChildPhpWithFlags(array $flags, string $code): string
    {
        $code = sprintf(
            'require %s; %s',
            var_export(dirname(__DIR__) . '/vendor/autoload.php', true),
            $code,
        );

        $process = proc_open(
            [PHP_BINARY, ...$flags, '-r', $code],
            [1 => ['pipe', 'w']],
            $pipes,
        );

        assert($process !== false);
        assert(isset($pipes[1]));

        $stdout = stream_get_contents($pipes[1]);

        assert($stdout !== false);

        proc_close($process);

        return $stdout;
    }

    private function markTestSkippedWhenPcovIsLoaded(): void
    {
        if (extension_loaded('pcov')) {
            $this->markTestSkipped('PHP extension pcov is loaded');
        }
    }

    private function markTestSkippedWhenPcovIsNotLoaded(): void
    {
        if (!extension_loaded('pcov')) {
            $this->markTestSkipped('PHP extension pcov is not loaded');
        }
    }

    private function markTestSkippedWhenXdebugIsLoaded(): void
    {
        if (extension_loaded('xdebug')) {
            $this->markTestSkipped('PHP extension xdebug is loaded');
        }
    }

    private function markTestSkippedWhenXdebugCanCollectCoverage(): void
    {
        $this->markTestSkippedWhenXdebugIsLoaded();

        if (!extension_loaded('xdebug')) {
            return;
        }

        $xdebugMode = xdebug_info('mode');

        assert(is_array($xdebugMode));

        if (in_array('coverage', $xdebugMode, true)) {
            $this->markTestSkipped('xdebug.mode=coverage must not be set');
        }
    }

    private function markTestSkippedWhenXdebugCannotCollectCoverage(): void
    {
        $this->markTestSkippedWhenXdebugIsNotLoaded();

        $xdebugMode = xdebug_info('mode');

        assert(is_array($xdebugMode));

        if (!in_array('coverage', $xdebugMode, true)) {
            $this->markTestSkipped('xdebug.mode=coverage must be set');
        }
    }

    private function markTestSkippedWhenXdebugIsNotLoaded(): void
    {
        if (!extension_loaded('xdebug')) {
            $this->markTestSkipped('PHP extension xdebug is not loaded');
        }
    }

    private function markTestSkippedWhenRunningOnPhpdbg(): void
    {
        if (!$this->isRunningOnPhpdbg()) {
            return;
        }

        $this->markTestSkipped('PHPDBG must not be used');
    }

    private function markTestSkippedWhenNotRunningOnPhpdbg(): void
    {
        if ($this->isRunningOnPhpdbg()) {
            return;
        }

        $this->markTestSkipped('PHPDBG must be used');
    }

    private function isRunningOnPhpdbg(): bool
    {
        return PHP_SAPI === 'phpdbg';
    }
}
