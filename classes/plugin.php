<?php /**
 * Основной класс плагина
 * Реализован как Singleton, чтобы получать доступ к нему из любого места
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */

 namespace IN_QR_CODE;

 class Plugin {
   /**
    * Экземпляр класса
    */
    protected static $_instance;

   /**
    * Возвращает экземпляр класса
    */
    public static function get_instance() {
        if (self::$_instance === null) {
            self::$_instance = new self;  
        }
 
        return self::$_instance;
    }

   /**
    * Конструктор класса
    */
   private function __construct() {
      // Шорткоды плагина
      $shortcodes = array(
         'in_qr_text',        // QR с текстом
         'in_qr_url',         // QR с URL
         'in_qr_email',       // QR с E-mail
         'in_qr_vcard',       // QR с визиткой
         'in_qr_payment',     // QR платежными реквизитами
      );
      foreach ( $shortcodes as $shortcode ) {
         add_shortcode( $shortcode, array( $this, 'do_shortcode' ) );
      }
   }

   /**
    * Обработчик шорткодов
    * @param mixed  $atts    Ассоциативный массив атрибутов указанных в шорткоде
    * @param string $atts    Текст шорткода, когда используется контентный шорткод
    * @param string $tag     Имя шорткода
    * @return string
    */
   public function do_shortcode( $atts, $content, $tag ) {
      // Создаем нужный тип контента
      switch ( $tag ) {
         case 'in_qr_url' :
            $type = new URL( $content, $atts );
            break;

         case 'in_qr_email' :
            $type = new Email( $content, $atts );
            break;
            
         case 'in_qr_vcard' :
            $type = new VCard( $content, $atts );
            break;

         case 'in_qr_payment' :
            $type = new Payment( $content, $atts );
            break;
            
         default: 
            $type = new Base_Text( $content, $atts );
      }

      // Создаем QR код
      $qr = new QR( $type );

      // Вернем результат
      return $qr->get_html();
   }
}