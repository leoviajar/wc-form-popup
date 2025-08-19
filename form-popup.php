<?php
/**
 * Plugin Name: Form Popup
 * Description: Exibe um popup com coleta de nome/e-mail e exibição de cupom/texto.
 * Version:     1.3.0
 * Author:      Leonardo
 */

require "plugin-update-checker/plugin-update-checker.php";
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker("https://github.com/leoviajar/wc-form-popup", __FILE__, "form-popup.php" );

// Impedir acesso direto ao arquivo
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Constantes do Plugin
define( 'PPP_PLUGIN_FILE', __FILE__ );
define( 'PPP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Incluir arquivos necessários
require_once PPP_PLUGIN_PATH . 'includes/settings.php';
require_once PPP_PLUGIN_PATH . 'includes/frontend.php';
require_once PPP_PLUGIN_PATH . 'includes/ajax.php';
require_once PPP_PLUGIN_PATH . 'includes/database.php';
require_once PPP_PLUGIN_PATH . 'includes/admin.php';

// Inicializar classes
$ppp_database = new PPP_Database();
$ppp_admin = new PPP_Admin($ppp_database);

// Ações de ativação/desativação (se necessário no futuro)
// register_activation_hook( __FILE__, 'ppp_activate_plugin' );
// register_deactivation_hook( __FILE__, 'ppp_deactivate_plugin' );

// function ppp_activate_plugin() {
//     // Código a ser executado na ativação
// }

// function ppp_deactivate_plugin() {
//     // Código a ser executado na desativação
// }


