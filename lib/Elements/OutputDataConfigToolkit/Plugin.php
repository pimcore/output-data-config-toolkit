<?php
namespace Elements\OutputDataConfigToolkit;

use \Pimcore_API_Plugin_Abstract;
use \Pimcore_API_Plugin_Interface;
use \Exception;

class Plugin extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface{

    protected static $config;

    public static function initPlugin() {

        define("OUTPUTDATACONFIG_WEBSITE_PATH", PIMCORE_WEBSITE_PATH . "/var/plugins/Elements_OutputDataConfigToolkit");
        if (!@is_dir(OUTPUTDATACONFIG_WEBSITE_PATH) && !@is_link(OUTPUTDATACONFIG_WEBSITE_PATH)) {
            mkdir(OUTPUTDATACONFIG_WEBSITE_PATH, 0755, true);
        }

    }

    /**
     * @static
     * @param bool $allowModifications
     * @return \Zend_Config_Xml
     */
    public static function getConfig($allowModifications = false) {
        Plugin::initPlugin();

        if (!self::$config) {

            if(!file_exists(OUTPUTDATACONFIG_WEBSITE_PATH . "/config.xml")) {
                self::setConfig(new \Zend_Config(array()));
            }

            self::$config = new \Zend_Config_Xml(OUTPUTDATACONFIG_WEBSITE_PATH . "/config.xml", null, $allowModifications);
        }

        return self::$config;
    }

    /**
     * @static
     * @param \Zend_Config_Xml $config
     * @return void
     */
    public static function setConfig($config) {
        Plugin::initPlugin();
        $writer = new \Zend_Config_Writer_Xml();
        $writer->write(OUTPUTDATACONFIG_WEBSITE_PATH . "/config.xml", $config);
    }


    public static function getChannels() {
        $config = self::getConfig(true);

        $channelsConfig = $config->channels->channel;
        if(is_string($channelsConfig)) {
            $channels = array($channelsConfig);
        } else {
            $channels = array();
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
        $db = \Pimcore_Resource::get();
        $db->query("CREATE TABLE `" . \Elements_OutputDataConfigToolkit_OutputDefinition_Resource::TABLE_NAME . "` (
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
			$result = Pimcore_API_Plugin_Abstract::getDb()->describeTable(\Elements_OutputDataConfigToolkit_OutputDefinition_Resource::TABLE_NAME);
		} catch(Exception $e){}
		return !empty($result);
    }

    /**
    * uninstall function
    * @return string $messaget status message to display in frontend
    */
	public static function uninstall(){
        $db = \Pimcore_Resource::get();
        $db->query("DROP TABLE IF EXISTS `" . \Elements_OutputDataConfigToolkit_OutputDefinition_Resource::TABLE_NAME . "`;");

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
