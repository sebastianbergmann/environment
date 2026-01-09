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

use const PHP_SAPI;
use const PHP_VERSION;
use function assert;
use function extension_loaded;
use function in_array;
use function ini_get;
use function is_array;
use function xdebug_info;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;

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

    public function testGetCurrentSettingsReturnsEmptyDiffIfNoValuesArePassed(): void
    {
        $this->assertSame([], (new Runtime)->getCurrentSettings([]));
    }

    #[RequiresPhpExtension('xdebug')]
    public function testGetCurrentSettingsReturnsCorrectDiffIfXdebugValuesArePassed(): void
    {
        if (ini_get('xdebug.mode') === '') {
            $this->markTestSkipped('xdebug.mode must not be set to "off"');
        }

        $this->assertArrayHasKey('xdebug.mode', (new Runtime)->getCurrentSettings(['xdebug.mode']));
    }

    public function testGetCurrentSettingsWillSkipSettingsThatIsNotSet(): void
    {
        $this->assertSame([], (new Runtime)->getCurrentSettings(['allow_url_include']));
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
