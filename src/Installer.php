<?php

/*
 * This file is part of the dotfiles project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was disstributed with this source code.
 */

namespace Dotfiles\Plugins\Vundle;
use Dotfiles\Core\Config\Config;
use Dotfiles\Core\Util\CommandProcessor;
use Dotfiles\Core\Util\Filesystem;
use Dotfiles\Core\Util\Toolkit;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Class Installer
 *
 * @package Dotfiles\Plugins\Vundle
 */
class Installer
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var CommandProcessor
     */
    private $processor;

    public function __construct(
        Config $config,
        LoggerInterface $logger,
        OutputInterface $output,
        CommandProcessor $processor
    )
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->output = $output;
        $this->processor = $processor;
    }

    public function run()
    {
        if(!$this->ensureVimInstalled()){
            $this->output->writeln('VIM is not installed, skipping');
            return;
        }

        $this->debug('begin install');

        $config = $this->config;
        $targetDir = $config->get('vundle.target_dir');

        $this->copyVundle($targetDir);
        $this->debug('end install');

        $this->doVimPluginInstall();
    }

    private function copyVundle(string $targetDir)
    {
        Toolkit::ensureDir($targetDir);
        $origin = realpath(__DIR__.'/../vendor/vim/vundle');

        $finder = Finder::create()
            ->in($origin)
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->exclude(['doc','test'])
            ->notName('*.md')
        ;

        if(!is_dir($origin)){
            $this->output('Vundle source is not found, please execute <comment>composer install</comment> first before installing vundle');
            return;
        }
        $fs = new Filesystem();
        $fs->mirror($origin,$targetDir,$finder,['override' => true]);
    }

    private function ensureVimInstalled()
    {
        $process = $this->processor->create('vim --version');
        $process->run();
        $output = $process->getOutput();
        return false !== strpos($output,'VIM - Vi IMproved') ? true:false;
    }

    private function doVimPluginInstall()
    {
        $process = $this->processor->create('vim +PluginInstall +qall');
        $process->run();
    }

    private function debug($message,$context=array())
    {
        $message = "<comment>vundle:</comment> $message";
        $this->logger->debug($message,$context);
    }
}