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

use Dotfiles\Core\Processor\ProcessRunner;
use Dotfiles\Core\Tests\Helper\BaseTestCase;
use Dotfiles\Plugins\Vundle\Installer;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

class InstallerTest extends BaseTestCase
{
    /**
     * @var MockObject
     */
    private $runner;

    protected function setUp(): void/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->runner = $this->createMock(ProcessRunner::class);
    }

    public function testRun(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())
            ->method('getOutput')
            ->willReturn('VIM - Vi IMproved')
        ;

        $this->runner->expects($this->at(0))
            ->method('run')
            ->with($this->stringContains('vim --version'))
            ->willReturn($process);

        $callback = function (): void {
        };
        $this->runner->expects($this->at(1))
            ->method('run')
            ->with($this->stringContains('vim +PluginINstall +qall'))
            ->willReturn($this->returnCallback($callback))
        ;
        $temp = $this->getParameters()->get('dotfiles.home_dir');
        $installer = $this->getSUT();
        $installer->run();

        $this->assertFileExists($temp.'/.vim/bundle/Vundle.vim/autoload/vundle.vim');
    }

    public function testRunWhenVimNotInstalled(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->exactly(1))
            ->method('getOutput')
            ->willReturnOnConsecutiveCalls(
                'some error'
            )
        ;
        $this->runner->expects($this->exactly(1))
            ->method('run')
            ->willReturn($process)
        ;

        $installer = $this->getSUT();
        $installer->run();

        $display = $this->getDisplay();
        $this->assertContains('VIM is not installed', $display);
    }

    private function getSUT()
    {
        return new Installer(
            $this->getParameters(),
            $this->getService('dotfiles.logger'),
            $this->getService('dotfiles.output'),
            $this->runner
        );
    }
}
