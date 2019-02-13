<?php

namespace OutputDataConfigToolkitBundle\Tools;

use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Pimcore\Log\Simple;

/**
 * Class Updater
 * @package OutputDataConfigToolkitBundle\Tools
 */
class Updater
{
    const DIR_PREFIX = PIMCORE_PRIVATE_VAR . '/config/output_data-config-toolkit/update/';
    const VERSION_FILE = self::DIR_PREFIX . 'version.txt';
    const VERSION_EXECUTED_DIR = self::DIR_PREFIX . 'executed';

    /**
     * Save pre version
     *
     * @param Event $event
     */
    public static function preUpdate(Event $event)
    {
        Simple::log("output_data_config_toolkit", $event->getName());

        if (!in_array($event->getName(), [
            ScriptEvents::PRE_UPDATE_CMD,
            ScriptEvents::PRE_PACKAGE_UPDATE,
        ])) {
            return;
        }

        $currentVersion = $event->getComposer()->getPackage()->getVersion();

        $versionFilePath = self::getVersionFilePath();
        file_put_contents($versionFilePath, $currentVersion);
        Simple::log("output_data_config_toolkit", "Current Version: {$currentVersion}");
    }

    /**
     * @param Event $event
     */
    public static function postUpdate(Event $event)
    {
        Simple::log("output_data_config_toolkit", $event->getName());

        if (!in_array($event->getName(), [
            ScriptEvents::POST_UPDATE_CMD,
            ScriptEvents::POST_PACKAGE_UPDATE,
        ])) {
            return;
        }

        $currentVersion = $event->getComposer()->getPackage()->getVersion();
        Simple::log("output_data_config_toolkit", "Current Version: {$currentVersion}");

        $versionFilePath = self::getVersionFilePath();
        $previousVersion = file_get_contents($versionFilePath);
        Simple::log("output_data_config_toolkit", "Previous Version: {$previousVersion}");

        self::executeVersionUpdates($previousVersion, $currentVersion);

        unlink(self::VERSION_FILE);
    }

    /**
     * @param string $previousVersion
     * @param string $currentVersion
     */
    private static function executeVersionUpdates($previousVersion, $currentVersion): void
    {
        $currentVersionNumber = self::getNumberFromVersion($currentVersion);
        $previousVersionNumber = self::getNumberFromVersion($previousVersion);

        for ($i = $previousVersionNumber; $i <= $currentVersionNumber; $i++) {
            VersionScript::execute($currentVersionNumber);
        }
    }

    /**
     * @param $currentVersion
     * @return string
     */
    private static function getNumberFromVersion($currentVersion): string
    {
        preg_match_all('!\d+!', $currentVersion, $matches);
        return implode("", $matches);
    }

    /**
     * @return bool|string
     */
    private static function getVersionFilePath()
    {
        $versionFilePath = realpath(self::VERSION_FILE);
        $versionFileDir = dirname($versionFilePath);

        if (!is_dir($versionFileDir)) {
            mkdir($versionFileDir);
        }
        return $versionFilePath;
    }

    /**
     * @return string
     */
    public static function getVersionExecutedDir()
    {
        $versionExecutedDir = realpath(self::VERSION_EXECUTED_DIR);

        if (!is_dir($versionExecutedDir)) {
            mkdir($versionExecutedDir);
        }
        return $versionExecutedDir;
    }

}