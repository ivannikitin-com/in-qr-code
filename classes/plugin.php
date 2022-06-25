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
    * Шорт-коды плагина
    * @var mixed
    */
   private $shortcodes;

   /**
    * Конструктор класса
    */
   private function __construct() {
      // Шорткоды плагина
      $this->shortcodes = array(
         'in_qr_text'      => __( 'QR с текстом', IN_QR_CODE ),
         'in_qr_url'       => __( 'QR с URL', IN_QR_CODE ),
         'in_qr_email'     => __( 'QR с E-mail', IN_QR_CODE ),
         'in_qr_vcard'     => __( 'QR с визиткой', IN_QR_CODE ),
         'in_qr_payment'   => __( 'QR платежными реквизитами', IN_QR_CODE )
      );

      // Инициализация шорткодов и фильтров
      foreach ( $this->get_shortcodes() as $shortcode => $description ) {
         add_shortcode( $shortcode, array( $this, 'do_shortcode' ) );
         add_filter( $shortcode, array( $this, 'do_filter' ), 10, 2 );
      }

      // Инициализация генератора
      if ( is_admin() ) new Generator();
   }

   /**
    * Возвращает список шорткодов
    * @return mixed
    */
   public function get_shortcodes() {
      return $this->shortcodes;
   }

   /**
    * Обработчик шорткодов
    * @param mixed  $atts    Ассоциативный массив атрибутов указанных в шорткоде
    * @param string $content Текст QR для генерации
    * @param string $tag     Имя шорткода
    * @return string         Возвращает HTML код для вставки на страницу 
    */
   public function do_shortcode( $atts, $content, $tag ) {
      $qr = $this->get_qr( $atts, $content, $tag );
      return $qr->get_html();
   }

   /**
    * Обработчик фильтра
    * @param string $content Текст QR для генерации
    * @param mixed  $atts    Ассоциативный массив атрибутов указанных в шорткоде
    * @return string         Возвращает URL к файлу c QR кодом
    */
    public function do_filter( $content, $atts ) {
      $qr = $this->get_qr( $atts, $content, $tag );
      return $qr->get_url();
   }   

   /**
    * Формирование QR кода
    * @param mixed  $atts    Ассоциативный массив атрибутов указанных в шорткоде
    * @param string $content Текст QR для генерации
    * @param string $tag     Имя шорткода
    * @return QR
    */
   private function get_qr( $atts, $content, $tag ) {
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
      return new QR( $type );      
   }
}