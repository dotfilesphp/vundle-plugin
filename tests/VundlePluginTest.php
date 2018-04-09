<?php

/*
 * This file is part of the dotfiles project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was disstributed with this source code.
 */

namespace Dotfiles\Plugins\Vundle\Tests;

use Dotfiles\Core\ApplicationFactory;
use Dotfiles\Plugins\Vundle\VundlePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class VundlePluginTest
 *
 * @package Dotfiles\Plugins\Vundle\Tests
 * @covers \Dotfiles\Plugins\Vundle\VundlePlugin
 */
class VundlePluginTest extends TestCase
{
    public function testConfiguration()
    {
        $plugin = new VundlePlugin();
        $this->assertEquals('vundle',$plugin->getName());
    }

    public function testLoad()
    {
        $factory = new ApplicationFactory();
        $factory->boot();
        $container = $factory->getContainer();
        $this->assertTrue($factory->hasPlugin('vundle'));
        $this->assertTrue($container->has('vundle.installer'));
    }
}
