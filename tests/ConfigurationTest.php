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

use Dotfiles\Core\Tests\BaseTestCase;
use Dotfiles\Plugins\Vundle\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Process\Process;

class ConfigurationTest extends BaseTestCase
{
    public function testTree()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(),array());

        $this->assertContains('bundle/Vundle.vim',$config['target_dir']);
    }
}
