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

namespace Dotfiles\Plugins\Vundle\Tests\Command;

use Dotfiles\Core\Tests\CommandTestCase;
use Dotfiles\Core\Tests\Helper\LoggerOutputTrait;
use Dotfiles\Core\Util\CommandProcessor;
use Dotfiles\Plugins\Vundle\Command\InstallCommand;
use Dotfiles\Plugins\Vundle\Installer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallCommandTest extends CommandTestCase
{
    use LoggerOutputTrait;

    public function testExecute(): void
    {
        $tester = $this->getTester('vundle:install');
        $tester->execute(array(), array('verbosity' => OutputInterface::VERBOSITY_DEBUG));
        $output = $this->getDisplay(true);

        $homeDir = $this->getParameters()->get('dotfiles.home_dir');
        $this->assertDirectoryExists($homeDir.'/.vim/bundle/Vundle.vim');
        $this->assertContains('begin install', $output);
    }

    protected function configureCommand(): void
    {
        $this->setUpLogger();
        $process = $this->createMock(Process::class);
        $process->expects($this->any())
            ->method('getOutput')
            ->willReturnOnConsecutiveCalls(
                'VIM - Vi IMproved'
            );
        $processor = $this->createMock(CommandProcessor::class);
        $processor->expects($this->any())
            ->method('create')
            ->willReturn($process)
        ;
        $installer = new Installer(
            $this->getService('dotfiles.parameters'),
            $this->logger,
            $this->output,
            $processor
        );
        $this->command = new InstallCommand(
            null,
            $installer,
            $processor
        );
    }
}
