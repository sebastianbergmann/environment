<?php
/*
 * This file is part of the Environment package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Environment;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Environment\Console
 */
class ConsoleTest extends TestCase
{
    /**
     * @var \SebastianBergmann\Environment\Console
     */
    private $console;

    protected function setUp()
    {
        $this->console = new Console;
    }

    public function testCanDetectIfStdoutIsInteractiveByDefault()
    {
        $this->assertInternalType('boolean', $this->console->isInteractive());
    }

    public function testCanDetectIfFileDescriptorIsInteractive()
    {
        $this->assertInternalType('boolean', $this->console->isInteractive(STDOUT));
    }

    public function testCanDetectColorSupport()
    {
        $this->assertInternalType('boolean', $this->console->hasColorSupport());
    }

    public function testCanDetectNumberOfColumns()
    {
        $this->assertInternalType('integer', $this->console->getNumberOfColumns());
    }
}
