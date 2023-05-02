<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OutputDataConfigToolkitBundle\Tools;

use OutputDataConfigToolkitBundle\OutputDefinition\Dao;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Symfony\Component\Filesystem\Filesystem;

class Installer extends AbstractInstaller
{
    public function install(): void
    {
        $fileSystem = new Filesystem();

        if (!file_exists(PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY . '/outputdataconfig')) {
            $fileSystem->mkdir(PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY . '/outputdataconfig', 0775);
            copy(__DIR__ . '/../../install/config.php', PIMCORE_CUSTOM_CONFIGURATION_DIRECTORY . '/outputdataconfig/config.php');
        }

        $db = \Pimcore\Db::get();
        $db->executeQuery('CREATE TABLE `' . Dao::TABLE_NAME . '` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `objectId` int(11) NOT NULL,
              `classId` varchar(50) NOT NULL,
              `channel` varchar(255) COLLATE utf8_bin NOT NULL,
              `configuration` longtext CHARACTER SET latin1,
              PRIMARY KEY (`id`),
              UNIQUE KEY `Unique` (`objectId`,`classId`,`channel`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ');

        $db->executeQuery("INSERT INTO users_permission_definitions (`key`) VALUES ('bundle_outputDataConfigToolkit');");
    }

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    public function isInstalled(): bool
    {
        try {
            $check = \Pimcore\Db::get()->fetchOne('SELECT `id` FROM ' . Dao::TABLE_NAME . ' LIMIT 1;');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function canBeInstalled(): bool
    {
        return !$this->isInstalled();
    }

    public function canBeUninstalled(): bool
    {
        return true;
    }

    public function uninstall(): void
    {
        $db = \Pimcore\Db::get();
        $db->executeQuery('DROP TABLE IF EXISTS `' . Dao::TABLE_NAME . '`;');

        $db->executeQuery("DELETE FROM users_permission_definitions WHERE `key` = 'bundle_outputDataConfigToolkit'");
        if (self::isInstalled()) {
            throw new InstallationException('Could not be uninstalled.');
        }
    }
}
