<?php

declare(strict_types=1);

/*
 * This file is part of the dotfiles project.
 *
 *     (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotfiles\Plugins\Vundle\Tests;

use Dotfiles\Core\Tests\Helper\BaseTestCase;
use Dotfiles\Plugins\Vundle\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends BaseTestCase
{
    public function testTree(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array());

        $this->assertContains('%dotfiles.home_dir%/.vim/bundle/Vundle.vim', $config['target_dir']);
    }
}
