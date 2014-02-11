<?php
/**
 * Environment
 *
 * Copyright (c) 2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Environment
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright  2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.github.com/sebastianbergmann/environment
 */

namespace SebastianBergmann\Environment;

use PHPUnit_Framework_TestCase;

class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \SebastianBergmann\Environment\Environment
     */
    private $env;

    protected function setUp()
    {
        $this->env = new Environment;
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::canCollectCodeCoverage
     */
    public function testAbilityToCollectCodeCoverageCanBeAssessed()
    {
        $this->assertInternalType('boolean', $this->env->canCollectCodeCoverage());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::getBinary
     */
    public function testBinaryCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getBinary());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::isHHVM
     */
    public function testRuntimeCanBeDetected()
    {
        $this->assertInternalType('boolean', $this->env->isHHVM());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::isPHP
     */
    public function testRuntimeCanBeDetected2()
    {
        $this->assertInternalType('boolean', $this->env->isPHP());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::hasXdebug
     */
    public function testXdebugCanBeDetected()
    {
        $this->assertInternalType('boolean', $this->env->hasXdebug());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::getRuntimeNameWithVersion
     */
    public function testRuntimeNameAndVersionCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getRuntimeNameWithVersion());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::getRuntimeName
     */
    public function testRuntimeNameCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getRuntimeName());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::getRuntimeVersion
     */
    public function testRuntimeVersionCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getRuntimeVersion());
    }

    /**
     * @covers \SebastianBergmann\Environment\Environment::getRuntimeVendorUrl
     */
    public function testRuntimeVendorUrlCanBeRetrieved()
    {
        $this->assertInternalType('string', $this->env->getRuntimeVendorUrl());
    }
}
