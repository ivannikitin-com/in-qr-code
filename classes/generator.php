<?php 
/**
 * Генератор QR кодов в админке
 * Реализует страницу в админке для генерации произвольных QR кодов
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */

 namespace IN_QR_CODE;

class Generator {
   /**
    * Конструктор класса
    */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
    }
    
    /**
     * Добавляет страницу админки
     */
    public function add_admin_page() {
        add_submenu_page(
            'tools.php',
            __( 'Генератор QR кодов', IN_QR_CODE ),
            __( 'QR коды', IN_QR_CODE ),
            'manage_options',
            IN_QR_CODE,
            array( $this, 'show_admin_page' )
        );
    }

    /**
     * Показывает страницу админки
     * https://nimblewebdeveloper.com/blog/add-tabs-to-wordpress-plugin-admin-page
     */
    public function show_admin_page() {

        //Get the active tab from the $_GET param
        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

        ?>
        <!-- Our admin page content should all be inside .wrap -->
        <div class="wrap">
          <!-- Print the page title -->
          <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
          <!-- Here are our tabs -->
          <nav class="nav-tab-wrapper"> 
            <?php foreach ( Plugin::get_instance()->get_shortcodes() as $tab_id => $title ): ?>
                <a href="?page=<?php echo IN_QR_CODE ?>&tab=<?php echo $tab_id ?>" class="nav-tab 
                    <?php if($tab === null || $tab == $tab_id):?>nav-tab-active<?php endif; ?>">
                        <?php echo $title ?>
                </a>
            <?php endforeach ?>    
          </nav>
      
          <div class="tab-content">
          <?php switch($tab) :
            case 'in_qr_url':
              echo 'in_qr_url'; //Put your HTML here
              break;
            case 'in_qr_email':
              echo 'in_qr_email'; //Put your HTML here
              break;
            case 'in_qr_vcard':
              echo 'in_qr_vcard'; //Put your HTML here
              break;
            case 'in_qr_payment':
              echo 'in_qr_payment'; //Put your HTML here
              break;

            default:
              echo 'in_qr_text';
              break;
          endswitch; ?>
          </div>
        </div>
        <?php      
    }

}