<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Elements\OutputDataConfigToolkit;

use Elements\OutputDataConfigToolkit\OutputDefinition\Dao;
use \Exception;

class Plugin extends \Pimcore\API\Plugin\AbstractPlugin implements \Pimcore\API\Plugin\PluginInterface {

    protected static $config;

    public static function initPlugin() {


    }

    private static function getConfigFile() {
        return \Pimcore\Config::locateConfigFile("outputdataconfig/config.php");
    }

    public static function getConfig() {
        $file = self::getConfigFile();
        if(file_exists($file)) {
            $config = include($file);
        } else {
            self::setConfig([]);
            $config = [];
        }
        return $config;
    }

    public static function setConfig($config) {
        $configFile = $file = self::getConfigFile();
        \Pimcore\File::put($configFile, to_php_data_file_format($config));
        return $config;
    }


    public static function getChannels() {
        $config = self::getConfig();

        $channels = [];
        $channelsConfig = $config['channels'];
        if($channelsConfig) {
            foreach($channelsConfig as $c) {
                $channels[] = (string)$c;
            }
        }
        return $channels;
    }



    /**
    *  install function
    * @return string $message statusmessage to display in frontend
    */
	public static function install(){

        if(!file_exists(PIMCORE_WEBSITE_PATH . "/config/outputdataconfig")) {
            \Pimcore\File::mkdir(PIMCORE_WEBSITE_PATH . "/config/outputdataconfig");
            copy(PIMCORE_PLUGINS_PATH . "/Elements_OutputDataConfigToolkit/install/config.php", PIMCORE_WEBSITE_PATH . "/config/outputdataconfig/config.php");
        }

        $db = \Pimcore\Db::get();
        $db->query("CREATE TABLE `" . Dao::TABLE_NAME . "` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `o_id` int(11) NOT NULL,
              `o_classId` int(11) NOT NULL,
              `channel` varchar(255) COLLATE utf8_bin NOT NULL,
              `configuration` longtext CHARACTER SET latin1,
              PRIMARY KEY (`id`),
              UNIQUE KEY `Unique` (`o_id`,`o_classId`,`channel`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ");

        $db->query("INSERT INTO users_permission_definitions (`key`) VALUES ('plugin_outputDataConfigToolkit');");

		if(self::isInstalled()){
			$statusMessage = "installed"; // $translate->_("plugin_objectassetfolderrelation_installed_successfully");
		} else {
			$statusMessage = "not installed"; // $translate->_("plugin_objectassetfolderrelation_could_not_install");
		}
		return $statusMessage;
    }

    /**
    *
    * @return boolean
    */
    public static function needsReloadAfterInstall(){
        return true;
    }

    /**
    *  indicates wether this plugins is currently installed
    * @return boolean
    */
	public static function isInstalled(){
        $result = null;
		try{
			$result = self::getDb()->describeTable(Dao::TABLE_NAME);
		} catch(Exception $e){}
		return !empty($result);
    }

    /**
    * uninstall function
    * @return string $messaget status message to display in frontend
    */
	public static function uninstall(){
        $db = \Pimcore\Db::get();
        $db->query("DROP TABLE IF EXISTS `" . Dao::TABLE_NAME . "`;");

        $db->query("DELETE FROM users_permission_definitions WHERE `key` = 'plugin_outputDataConfigToolkit'");
		if(!self::isInstalled()){
			$statusMessage = "uninstalled successfully"; //  $translate->_("plugin_objectassetfolderrelation_uninstalled_successfully");
		} else {
			$statusMessage = "did not uninstall"; // $translate->_("plugin_objectassetfolderrelation_could_not_uninstall");
		}
		return $statusMessage;

    }


    /**
     * @return string $jsClassName
     */
    public static function getJsClassName(){
        return "pimcore.plugin.outputDataConfigToolkit.Plugin";
    }

    /**
    *
    * @param string $language
    * @return string path to the translation file relative to plugin direcory
    */
	public static function getTranslationFile($language){
            if($language=="de"){
                return "/Elements_OutputDataConfigToolkit/texts/de.csv";
            } else if($language=="en"){
                return "/Elements_OutputDataConfigToolkit/texts/en.csv";
            } else {
                return null;
            }
            return null;

        }
 
}
