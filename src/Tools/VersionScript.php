<?php
/**
 * Created by PhpStorm.
 * User: jraab
 * Date: 12.02.2019
 * Time: 11:53
 */

namespace OutputDataConfigToolkitBundle\Tools;

use Doctrine\DBAL\DBALException;
use OutputDataConfigToolkitBundle\OutputDefinition\Dao;
use Pimcore\Db;

class VersionScript
{

    private static function isExecuted($versionString)
    {
        return file_exists(Updater::getVersionExecutedDir() . "/" . $versionString . ".txt");
    }

    private static function setExecuted($versionString, $data = "")
    {
        return file_put_contents(Updater::getVersionExecutedDir() . "/" . $versionString . ".txt", $data);
    }

    /**
     * @param $versionNumber
     */
    public static function execute($versionNumber)
    {
        $version = (string)$versionNumber;
        $versionMethod = "version__{$version[0]}_{$version[1]}_{$version[2]}";

        if (method_exists(__CLASS__, $versionMethod)) {
            if (!self::isExecuted($versionMethod)) {
                $versionLog = call_user_func(__CLASS__ . "::" . $versionMethod);
                self::setExecuted($versionMethod, $versionLog);
            }
        }
    }

    /**
     * @return string
     */
    private static function version__2_5_6()
    {
        $db = Db::get();
        try {
            $db->exec("ALTER TABLE " . Dao::TABLE_NAME . " MODIFY `o_classId` varchar(50);");
            $versionLog = "successfully executed";
        } catch (DBALException $e) {
            $versionLog = $e->getMessage() . ", " . $e->getFile() . ", " . $e->getLine();
        }
        return $versionLog;
    }
}