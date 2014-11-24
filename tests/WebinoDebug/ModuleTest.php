<?php
/**
 * Webino (http://webino.sk/)
 *
 * @link        https://github.com/webino/WebinoDebug/ for the canonical source repository
 * @copyright   Copyright (c) 2014 Webino, s. r. o. (http://webino.sk/)
 * @license     BSD-3-Clause
 */

namespace WebinoDebug;

use Tracy\Debugger;
use Zend\ModuleManager\ModuleEvent;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-10-15 at 13:08:08.
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Module
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Module;
    }

    /**
     * @covers WebinoDebug\Module::getConfig
     */
    public function testGetConfig()
    {
        $config = $this->object->getConfig();
        $this->assertTrue(is_array($config));
    }

    /**
     * @covers WebinoDebug\Module::init
     */
    public function testInitDisabled()
    {
        $events  = $this->getMock('Zend\EventManager\EventManager', [], [], '', false);
        $modules = $this->getMock('Zend\ModuleManager\ModuleManager', [], [], '', false);

        $modules->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($events));

        $events->expects($this->once())
            ->method('attach')
            ->will($this->returnCallback(function ($eventName, $callback) {
                $this->assertSame(ModuleEvent::EVENT_LOAD_MODULES_POST, $eventName);
                $this->assertInstanceOf('closure', $callback);

                $event    = new ModuleEvent;
                $services = $this->getMock('Zend\ServiceManager\ServiceManager');
                $options  = $this->getMock('WebinoDebug\Options\ModuleOptions');
                $event->setParam('ServiceManager', $services);

                $services->expects($this->once())
                    ->method('get')
                    ->with('WebinoDebug\Options\ModuleOptions')
                    ->will($this->returnValue($options));

                $options->expects($this->once())
                    ->method('isEnabled')
                    ->will($this->returnValue(false));

                foreach (['hasBar', 'getMode', 'getLog', 'getEmail', 'isStrict',
                    'getMaxDepth', 'getMaxLen', 'getTemplateMap'] as $method) {

                    $options->expects($this->never())->method($method);
                }

                $callback($event);
            }));

        $this->object->init($modules);
        $this->assertFalse(Debugger::isEnabled());
    }

    /**
     * @covers WebinoDebug\Module::init
     */
    public function testInitEnabled()
    {
        $options = new Options\ModuleOptions([
            'enabled'     => true,
            'mode'        => false,
            'bar'         => false,
            'log'         => 'data',
            'email'       => 'test@example.com',
            'strict'      => false,
            'maxDepth'    => 2,
            'maxLen'      => 9,
            'templateMap' => ['test' => 'example'],
        ]);

        $events  = $this->getMock('Zend\EventManager\EventManager', [], [], '', false);
        $modules = $this->getMock('Zend\ModuleManager\ModuleManager', [], [], '', false);

        $modules->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($events));

        $events->expects($this->once())
            ->method('attach')
            ->will($this->returnCallback(function ($eventName, $callback) use ($options) {

                $this->assertSame(ModuleEvent::EVENT_LOAD_MODULES_POST, $eventName);
                $this->assertInstanceOf('closure', $callback);

                $event    = new ModuleEvent;
                $services = $this->getMock('Zend\ServiceManager\ServiceManager');
                $event->setParam('ServiceManager', $services);

                $templateMapResolver = $this->getMock('Zend\View\Resolver\TemplateMapResolver');
                $services->expects($this->exactly(2))
                    ->method('get')
                    ->withConsecutive(
                        ['WebinoDebug\Options\ModuleOptions'],
                        ['ViewTemplateMapResolver']
                    )
                    ->will($this->onConsecutiveCalls(
                        $this->returnValue($options),
                        $this->returnValue($templateMapResolver)
                    ));

                $templateMapResolver->expects($this->once())
                    ->method('merge')
                    ->with($options->getTemplateMap());

                $callback($event);
            }));

        $this->object->init($modules);
        $this->assertTrue(Debugger::isEnabled());
        $this->assertFalse(Debugger::$productionMode);
        $this->assertSame('WebinoDebug\Tracy\Workaround\DisabledBar', get_class(Debugger::getBar()));
        $this->assertSame($options->getLog(), Debugger::$logDirectory);
        $this->assertSame($options->getEmail(), Debugger::$email);
        $this->assertSame($options->isStrict(), Debugger::$strictMode);
        $this->assertSame($options->getMaxDepth(), Debugger::$maxDepth);
        $this->assertSame($options->getMaxLen(), Debugger::$maxLen);
    }
}