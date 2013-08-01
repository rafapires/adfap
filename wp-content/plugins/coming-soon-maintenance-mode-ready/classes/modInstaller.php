<?php
class modInstallerCsp {
    static private $_current = array();
    /**
     * Install new moduleCsp into plugin
     * @param string $module new moduleCsp data (@see classes/tables/modules.php)
     * @param string $path path to the main plugin file from what module is installed
     * @return bool true - if install success, else - false
     */
    static public function install($module, $path) {
        $exPlugDest = explode('plugins', $path);
        if(!empty($exPlugDest[1])) {
            $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
        }
        $path = $path. DS. $module['code'];
        if(!empty($module) && !empty($path) && is_dir($path)) {
            if(self::isModule($path)) {
                $filesMoved = false;
                if(empty($module['ex_plug_dir']))
                    $filesMoved = self::moveFiles($module['code'], $path);
                else
                    $filesMoved = true;     //Those modules doesn't need to move their files
                if($filesMoved) {
                    if(frameCsp::_()->getTable('modules')->exists($module['code'], 'code')) {
                        frameCsp::_()->getTable('modules')->delete(array('code' => $module['code']));
                    }
                    frameCsp::_()->getTable('modules')->insert($module);
                    self::_runModuleInstall($module);
                    self::_installTables($module);
                    return true;
                    /*if(frameCsp::_()->getTable('modules')->insert($module)) {
                        self::_installTables($module);
                        return true;
                    } else {
                        errorsCsp::push(langCsp::_(array('Install', $module['code'], 'failed ['. mysql_error(). ']')), errorsCsp::MOD_INSTALL);
                    }*/
                } else {
                    errorsCsp::push(langCsp::_(array('Move files for', $module['code'], 'failed')), errorsCsp::MOD_INSTALL);
                }
            } else
                errorsCsp::push(langCsp::_(array($module['code'], 'is not plugin module')), errorsCsp::MOD_INSTALL);
        }
        return false;
    }
    static protected function _runModuleInstall($module) {
        $moduleLocationDir = CSP_MODULES_DIR;
        if(!empty($module['ex_plug_dir']))
            $moduleLocationDir = utilsCsp::getPluginDir( $module['ex_plug_dir'] );
        if(is_dir($moduleLocationDir. $module['code'])) {
            importClassCsp($module['code'], $moduleLocationDir. $module['code']. DS. 'mod.php');
            $moduleClass = toeGetClassNameCsp($module['code']);
            $moduleObj = new $moduleClass($m);
            if($moduleObj) {
                $moduleObj->install();
            }
        }
    }
    /**
     * Check whether is or no module in given path
     * @param string $path path to the module
     * @return bool true if it is module, else - false
     */
    static public function isModule($path) {
        return true;
    }
    /**
     * Move files to plugin modules directory
     * @param string $code code for module
     * @param string $path path from what module will be moved
     * @return bool is success - true, else - false
     */
    static public function moveFiles($code, $path) {
        if(!is_dir(CSP_MODULES_DIR. $code)) {
            if(mkdir(CSP_MODULES_DIR. $code)) {
                utilsCsp::copyDirectories($path, CSP_MODULES_DIR. $code);
                return true;
            } else 
                errorsCsp::push(langCsp::_('Can not create module directory. Try to set permission to '. CSP_MODULES_DIR. ' directory 755 or 777'), errorsCsp::MOD_INSTALL);
        } else
            return true;
            //errorsCsp::push(langCsp::_(array('Directory', $code, 'already exists')), errorsCsp::MOD_INSTALL);
        return false;
    }
    static private function _getPluginLocations() {
        $locations = array();
        $plug = reqCsp::getVar('plugin');
        if(empty($plug)) {
            $plug = reqCsp::getVar('checked');
            $plug = $plug[0];
        }
        $locations['plugPath'] = plugin_basename( trim( $plug ) );
        $locations['plugDir'] = dirname(WP_PLUGIN_DIR. DS. $locations['plugPath']);
        $locations['xmlPath'] = $locations['plugDir']. DS. 'install.xml';
        return $locations;
    }
    static private function _getModulesFromXml($xmlPath) {
        if($xml = utilsCsp::getXml($xmlPath)) {
            if(isset($xml->modules) && isset($xml->modules->mod)) {
                $modules = array();
                $xmlMods = $xml->modules->children();
                foreach($xmlMods->mod as $mod) {
                    $modules[] = $mod;
                }
                if(empty($modules))
                    errorsCsp::push(langCsp::_('No modules were found in XML file'), errorsCsp::MOD_INSTALL);
                else
                    return $modules;
            } else
                errorsCsp::push(langCsp::_('Invalid XML file'), errorsCsp::MOD_INSTALL);
        } else
            errorsCsp::push(langCsp::_('No XML file were found'), errorsCsp::MOD_INSTALL);
        return false;
    }
    /**
     * Check whether modules is installed or not, if not and must be activated - install it
     * @param array $codes array with modules data to store in database
     * @param string $path path to plugin file where modules is stored (__FILE__ for example)
     * @return bool true if check ok, else - false
     */
    static public function check($extPlugName = '') {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsCsp::xmlNodeAttrsToArr($m);
                if(!empty($modDataArr)) {
                    if(frameCsp::_()->moduleExists($modDataArr['code'])) { //If module Exists - just activate it
                        self::activate($modDataArr);
                    } else {                                           //  if not - install it
                        if(!self::install($modDataArr, $locations['plugDir'])) {
                            errorsCsp::push(langCsp::_(array('Install', $modDataArr['code'], 'failed')), errorsCsp::MOD_INSTALL);
                        }
                    }
                }
            }
        } else
            errorsCsp::push(langCsp::_('Error Activate module'), errorsCsp::MOD_INSTALL);
        if(errorsCsp::haveErrors(errorsCsp::MOD_INSTALL)) {
            self::displayErrors();
            return false;
        }
        return true;
    }
    /**
     * Deactivate module after deactivating external plugin
     */
    static public function deactivate() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsCsp::xmlNodeAttrsToArr($m);
                if(frameCsp::_()->moduleActive($modDataArr['code'])) { //If module is active - then deacivate it
                    if(frameCsp::_()->getModule('options')->getModel('modules')->put(array(
                        'id' => frameCsp::_()->getModule($modDataArr['code'])->getID(),
                        'active' => 0,
                    ))->error) {
                        errorsCsp::push(langCsp::_('Error Deactivation module'), errorsCsp::MOD_INSTALL);
                    }
                }
            }
        }
        if(errorsCsp::haveErrors(errorsCsp::MOD_INSTALL)) {
            self::displayErrors(false);
            return false;
        }
        return true;
    }
    static public function activate($modDataArr) {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsCsp::xmlNodeAttrsToArr($m);
                if(!frameCsp::_()->moduleActive($modDataArr['code'])) { //If module is not active - then acivate it
                    if(frameCsp::_()->getModule('options')->getModel('modules')->put(array(
                        'code' => $modDataArr['code'],
                        'active' => 1,
                    ))->error) {
                        errorsCsp::push(langCsp::_('Error Activating module'), errorsCsp::MOD_INSTALL);
                    }
                }
            }
        }
    } 
    /**
     * Display all errors for module installer, must be used ONLY if You realy need it
     */
    static public function displayErrors($exit = true) {
        $errors = errorsCsp::get(errorsCsp::MOD_INSTALL);
        foreach($errors as $e) {
            echo '<b style="color: red;">'. $e. '</b><br />';
        }
        if($exit) exit();
    }
    static public function uninstall() {
        $locations = self::_getPluginLocations();
        if($modules = self::_getModulesFromXml($locations['xmlPath'])) {
            foreach($modules as $m) {
                $modDataArr = utilsCsp::xmlNodeAttrsToArr($m);
                self::_uninstallTables($modDataArr);
                frameCsp::_()->getModule('options')->getModel('modules')->delete(array('code' => $modDataArr['code']));
                utilsCsp::deleteDir(CSP_MODULES_DIR. $modDataArr['code']);
            }
        }
    }
    static protected  function _uninstallTables($module) {
        if(is_dir(CSP_MODULES_DIR. $module['code']. DS. 'tables')) {
            $tableFiles = utilsCsp::getFilesList(CSP_MODULES_DIR. $module['code']. DS. 'tables');
            if(!empty($tableNames)) {
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameCsp::_()->getTable($tableName))
                        frameCsp::_()->getTable($tableName)->uninstall();
                }
            }
        }
    }
    static public function _installTables($module) {
        $modDir = empty($module['ex_plug_dir']) ? 
            CSP_MODULES_DIR. $module['code']. DS : 
            utilsCsp::getPluginDir($module['ex_plug_dir']). $module['code']. DS; 
        if(is_dir($modDir. 'tables')) {
            $tableFiles = utilsCsp::getFilesList($modDir. 'tables');
            if(!empty($tableFiles)) {
                frameCsp::_()->extractTables($modDir. 'tables'. DS);
                foreach($tableFiles as $file) {
                    $tableName = str_replace('.php', '', $file);
                    if(frameCsp::_()->getTable($tableName))
                        frameCsp::_()->getTable($tableName)->install();
                }
            }
        }
    }
}
