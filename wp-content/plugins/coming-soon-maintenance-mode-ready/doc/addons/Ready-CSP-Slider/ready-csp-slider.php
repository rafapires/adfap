<?php
/**
 * Plugin Name: Ready! Comming Soon Page - Background Slider
 * Description: Ready! Comming Soon Page - Background Slider Module.
 * Plugin URI: http://readyshoppingcart.com/
 * Author: readyshoppingcart.com
 * Author URI: http://readyshoppingcart.com
 * Version: 0.1.0
 **/
    register_activation_hook(__FILE__, array('modInstallerCsp', 'check'));
    register_deactivation_hook(__FILE__, array('modInstallerCsp', 'deactivate'));
    register_uninstall_hook(__FILE__, array('modInstallerCsp', 'uninstall'));