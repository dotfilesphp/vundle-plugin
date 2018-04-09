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

use Dotfiles\Core\Config\Config;
use Dotfiles\Core\Tests\BaseTestCase;
use Dotfiles\Core\Util\CommandProcessor;
use Dotfiles\Plugins\Vundle\Installer;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallerTest extends BaseTestCase
{
    /**
     * @var MockObject
     */
    private $config;

    /**
     * @var MockObject
     */
    private $logger;

    /**
     * @var MockObject
     */
    private $output;

    /**
     * @var MockObject
     */
    private $processor;

    /**
     * @var string
     */
    private $temp;

    protected function setUp(): void/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->config = $this->createMock(Config::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->processor = $this->createMock(CommandProcessor::class);
        $this->temp = sys_get_temp_dir().'/dotfiles/tests/vundle';
        static::cleanupTempDir();
    }

    public function testRun(): void
    {
        $this->logger->expects($this->exactly(2))
            ->method('debug')
            ->withConsecutive(
                array($this->stringContains('begin install')),
                array($this->stringContains('end install'))
            )
        ;

        $process = $this->createMock(Process::class);
        $process->expects($this->exactly(1))
            ->method('getOutput')
            ->willReturnOnConsecutiveCalls(
                'VIM - Vi IMproved test'
            )
        ;

        $this->processor->expects($this->exactly(2))
            ->method('create')
            ->willReturn($process)
        ;

        $temp = $this->temp;
        $installer = $this->getSUT();
        $installer->run();

        $this->assertFileExists($temp.'/autoload/vundle.vim');
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
        $this->processor->expects($this->exactly(1))
            ->method('create')
            ->willReturn($process)
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('VIM is not installed'))
        ;

        $installer = $this->getSUT();
        $installer->run();
    }

    private function getSUT()
    {
        $retConfig = array(
            'target_dir' => $this->temp,
        );
        $this->config->expects($this->any())
            ->method('get')
            ->willReturnMap(array(
                array('vundle.target_dir', $retConfig['target_dir']),
                array('dotfiles.base_dir', __DIR__.'/../'),
            ))
        ;

        return new Installer($this->config, $this->logger, $this->output, $this->processor);
    }
}
