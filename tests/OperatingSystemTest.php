<?php
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SebastianBergmann\Environment;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Environment\OperatingSystem
 */
class OperatingSystemTest extends TestCase
{
    /**
     * @var \SebastianBergmann\Environment\OperatingSystem
     */
    private $os;

    protected function setUp()
    {
        $this->os = new OperatingSystem;
    }

    /**
     * @requires os Linux
     */
    public function testFamilyCanBeRetrieved(): void
    {
        $this->assertEquals('Linux', $this->os->getFamily());
    }
}
