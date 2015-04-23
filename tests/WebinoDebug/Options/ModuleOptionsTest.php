<?php
/**
 * Webino (http://webino.sk/)
 *
 * @link        https://github.com/webino/WebinoDebug/ for the canonical source repository
 * @copyright   Copyright (c) 2014-2015 Webino, s. r. o. (http://webino.sk/)
 * @license     BSD-3-Clause
 */

namespace WebinoDebug\Options;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-10-18 at 00:35:27.
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertDefaultOptions()
    {
        $options = new ModuleOptions;
        $this->assertInstanceOf(DebuggerOptions::class, $options);
        $this->assertFileExists($options->getTemplateMap()['error/index']);
    }

    public function testSettersAndGetters()
    {
        $cfg = [
            'templateMap' => ['test' => 'example'],
        ];

        $options = new ModuleOptions($cfg);
        $this->assertInstanceOf(DebuggerOptions::class, $options);
        $this->assertSame($cfg['templateMap'], $options->getTemplateMap());
    }
}
