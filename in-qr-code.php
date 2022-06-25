<?php
/**
 * in-qr-code
 *
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       in-qr-code
 * Plugin URI:        https://github.com/ivannikitin-com/in-qr-code
 * Description:       Плагин гибкой генерации QR кодов для WordPress.
 * Version:           1.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            Иван Никитин
 * Author URI:        https://ivannikitin.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/ivannikitin-com/in-qr-code
 * Text Domain:       in-qr-code
 * Domain Path:       /lang
 */

/* Напрямую не вызываем! */
defined( 'ABSPATH' ) or die( 'Bad Request' );

/* Глобальные константы плагина */
define( 'IN_QR_CODE', 'in-qr-code' );	              // Text Domain
define( 'IN_QR_CODE_DIR', dirname( __FILE__ ) );	  // Папка плагина

/* Классы */
require_once( IN_QR_CODE_DIR . '/classes/plugin.php' );
require_once( IN_QR_CODE_DIR . '/classes/qr.php' );
require_once( IN_QR_CODE_DIR . '/classes/generator.php' );
require_once( IN_QR_CODE_DIR . '/classes/types/base_text.php' );
require_once( IN_QR_CODE_DIR . '/classes/types/url.php' );
require_once( IN_QR_CODE_DIR . '/classes/types/email.php' );
require_once( IN_QR_CODE_DIR . '/classes/types/vcard.php' );
require_once( IN_QR_CODE_DIR . '/classes/types/payment.php' );

// Запуск
IN_QR_CODE\Plugin::get_instance();