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

use const STDOUT;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Console::class)]
final class ConsoleTest extends TestCase
{
    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanDetectIfStdoutIsInteractiveByDefault(): void
    {
        $this->assertIsBool((new Console)->isInteractive());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanDetectIfFileDescriptorIsInteractive(): void
    {
        $this->assertIsBool((new Console)->isInteractive(STDOUT));
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanDetectColorSupport(): void
    {
        $this->assertIsBool((new Console)->hasColorSupport());
    }

    /**
     * @todo Now that this component is PHP 7-only and uses return type declarations
     * this test makes even less sense than before
     */
    public function testCanDetectNumberOfColumns(): void
    {
        $this->assertIsInt((new Console)->getNumberOfColumns());
    }
}
