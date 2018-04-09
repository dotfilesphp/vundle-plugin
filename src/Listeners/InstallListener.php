<?php

/*
 * This file is part of the dotfiles project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was disstributed with this source code.
 */

namespace Dotfiles\Plugins\Vundle\Listeners;


use Dotfiles\Plugins\Vundle\Installer;

class InstallListener
{
    /**
     * @var Installer
     */
    private $installer;

    public function __construct(Installer $installer)
    {
        $this->installer = $installer;
    }

    public function onInstallEvent()
    {
        $this->installer->run();
    }
}