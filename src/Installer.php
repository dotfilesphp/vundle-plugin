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

namespace Dotfiles\Plugins\Vundle;

use Dotfiles\Core\DI\Parameters;
use Dotfiles\Core\Util\CommandProcessor;
use Dotfiles\Core\Util\Filesystem;
use Dotfiles\Core\Util\Toolkit;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class Installer.
 */
class Installer
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @var CommandProcessor
     */
    private $processor;

    public function __construct(
        Parameters $parameters,
        LoggerInterface $logger,
        OutputInterface $output,
        CommandProcessor $processor
    ) {
        $this->parameters = $parameters;
        $this->logger = $logger;
        $this->output = $output;
        $this->processor = $processor;
    }

    public function run(): void
    {
        if (!$this->ensureVimInstalled()) {
            $this->output->writeln('VIM is not installed, skipping');

            return;
        }

        $this->debug('begin install');

        $config = $this->parameters;
        $targetDir = $config->get('vundle.target_dir');

        $this->copyVundle($targetDir);
        $this->debug('end install');

        $this->doVimPluginInstall();
    }

    private function copyVundle(string $targetDir): void
    {
        Toolkit::ensureDir($targetDir);
        $base = $this->parameters->get('dotfiles.base_dir');
        $origin = realpath($base.'/vendor/vim/vundle');

        $finder = Finder::create()
            ->in($origin)
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->exclude(array('doc', 'test'))
            ->notName('*.md')
        ;

        //@codeCoverageIgnoreStart
        if (!is_dir($origin)) {
            $this->output('Vundle source is not found, please execute <comment>composer install</comment> first before installing vundle');

            return;
        }
        //@codeCoverageIgnoreEnd
        $fs = new Filesystem();
        $fs->mirror($origin, $targetDir, $finder, array('override' => true));
    }

    private function debug($message, $context = array()): void
    {
        $message = "<comment>vundle:</comment> $message";
        $this->logger->debug($message, $context);
    }

    private function doVimPluginInstall(): void
    {
        $process = $this->processor->create('vim +PluginInstall +qall');
        $process->setTimeout(600);
        //@codeCoverageIgnoreStart
        $process->run(function ($type, $buffer): void {
            $pattern = '/Processing.*\'(.*)\'/im';
            $match = preg_match_all($pattern, $buffer, $match);
            if (preg_match_all($pattern, $buffer, $match)) {
                $bundle = $match[1][0];
                $this->output->writeln("Processing <comment>$bundle</comment>");
            }
        });
        //@codeCoverageIgnoreEnd
    }

    private function ensureVimInstalled()
    {
        $process = $this->processor->create('vim --version');
        $process->run();
        $output = $process->getOutput();

        return false !== strpos($output, 'VIM - Vi IMproved') ? true : false;
    }
}
