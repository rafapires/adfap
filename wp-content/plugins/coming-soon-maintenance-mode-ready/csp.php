<?php
/**
 * Plugin Name: Coming Soon / Maintenance mode Ready!
 * Plugin URI: http://readyshoppingcart.com/coming-soon-plugin/
 * Description: Coming Soon or Under Construction page for your Website while maintenance mode on. Get E-mails, Likes and tweets from the visitors
 * Version: 0.0.4
 * Author: coming soon
 * Author URI: http://readyshoppingcart.com
 **/
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
    importClassCsp('dbCsp');
    importClassCsp('installerCsp');
    importClassCsp('baseObjectCsp');
    importClassCsp('moduleCsp');
    importClassCsp('modelCsp');
    importClassCsp('viewCsp');
    importClassCsp('controllerCsp');
    importClassCsp('helperCsp');
    importClassCsp('tabCsp');
    importClassCsp('dispatcherCsp');
    importClassCsp('fieldCsp');
    importClassCsp('tableCsp');
    importClassCsp('frameCsp');
    importClassCsp('langCsp');
    importClassCsp('reqCsp');
    importClassCsp('uriCsp');
    importClassCsp('htmlCsp');
    importClassCsp('responseCsp');
    importClassCsp('fieldAdapterCsp');
    importClassCsp('validatorCsp');
    importClassCsp('errorsCsp');
    importClassCsp('utilsCsp');
    importClassCsp('modInstallerCsp');
    importClassCsp('wpUpdater');
	importClassCsp('toeWordpressWidgetCsp');
	importClassCsp('installerDbUpdaterCsp');
	importClassCsp('templateModuleCsp');
	importClassCsp('templateViewCsp');
	importClassCsp('fileuploaderCsp');
	importClassCsp('recapcha',			CSP_HELPERS_DIR. 'recapcha.php');
	importClassCsp('mobileDetect',		CSP_HELPERS_DIR. 'mobileDetect.php');

    installerCsp::update();
    errorsCsp::init();
    
    dispatcherCsp::doAction('onBeforeRoute');
    frameCsp::_()->parseRoute();
    dispatcherCsp::doAction('onAfterRoute');

    dispatcherCsp::doAction('onBeforeInit');
    frameCsp::_()->init();
    dispatcherCsp::doAction('onAfterInit');

    dispatcherCsp::doAction('onBeforeExec');
    frameCsp::_()->exec();
    dispatcherCsp::doAction('onAfterExec');