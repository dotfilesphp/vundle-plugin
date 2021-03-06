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

namespace Dotfiles\Plugins\Vundle\Command;

use Dotfiles\Core\Command\Command;
use Dotfiles\Plugins\Vundle\Installer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var Installer
     */
    private $installer;

    public function __construct(?string $name = null, Installer $installer)
    {
        parent::__construct($name);
        $this->installer = $installer;
    }

    public function configure(): void
    {
        $this->setName('vundle:install');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->installer->run();
    }
}
