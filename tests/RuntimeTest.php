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
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Environment\Runtime
 */
final class RuntimeTest extends TestCase
{
    /**
     * @requires extension xdebug
     */
    public function testCanCollectCodeCoverageWhenXdebugExtensionIsEnabled(): void
    {
        $this->assertTrue((new Runtime)->canCollectCodeCoverage());
    }

    /**
     * @requires extension pcov
     */
    public function testCanCollectCodeCoverageWhenPcovExtensionIsEnabled(): void
    {
        $this->assertTrue((new Runtime)->canCollectCodeCoverage());
    }

    public function testCanCollectCodeCoverageWhenRunningOnPhpdbg(): void
    {
        $this->markTestSkippedWhenNotRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->canCollectCodeCoverage());
    }

    public function testBinaryCanBeRetrieved(): void
    {
        $this->assertNotEmpty((new Runtime)->getBinary());
    }

    public function testIsPhpReturnsTrueWhenRunningOnPhp(): void
    {
        $this->markTestSkippedWhenRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->isPHP());
    }

    /**
     * @requires extension pcov
     */
    public function testPCOVCanBeDetected(): void
    {
        $this->assertTrue((new Runtime)->hasPCOV());
    }

    public function testPhpdbgCanBeDetected(): void
    {
        $this->markTestSkippedWhenNotRunningOnPhpdbg();

        $this->assertTrue((new Runtime)->hasPHPDBGCodeCoverage());
    }

    /**
     * @requires extension xdebug
     */
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

    /**
     * @requires extension xdebug
     */
    public function testGetCurrentSettingsReturnsCorrectDiffIfXdebugValuesArePassed(): void
    {
        $this->assertIsArray((new Runtime)->getCurrentSettings(['xdebug.mode']));
        $this->assertArrayHasKey('xdebug.mode', (new Runtime)->getCurrentSettings(['xdebug.mode']));
    }

    public function testGetCurrentSettingsWillSkipSettingsThatIsNotSet(): void
    {
        $this->assertSame([], (new Runtime)->getCurrentSettings(['allow_url_include']));
    }

    private function markTestSkippedWhenNotRunningOnPhpdbg(): void
    {
        if ($this->isRunningOnPhpdbg()) {
            return;
        }

        $this->markTestSkipped('PHPDBG is required.');
    }

    private function markTestSkippedWhenRunningOnPhpdbg(): void
    {
        if (!$this->isRunningOnPhpdbg()) {
            return;
        }

        $this->markTestSkipped('Cannot run on PHPDBG');
    }

    private function isRunningOnPhpdbg(): bool
    {
        return PHP_SAPI === 'phpdbg';
    }
}
