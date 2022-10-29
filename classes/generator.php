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
        add_action( 'admin_init', array( $this, 'admin_init' ) );
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
     * Инициализация админки
     */
    public function admin_init() {
      register_setting( IN_QR_CODE, IN_QR_CODE, 'sanitize_callback' );
      
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
          <form action="options.php" method="POST">
            <div class="tab-content">
            <?php 
            switch( $tab ) :
              case 'in_qr_vcard':
                $this->register_fields( $tab, array_merge( VCard::get_params(), Base_Text::get_params() ) );
                break;

              case 'in_qr_payment':
                $this->register_fields( $tab, array_merge( Payment::get_params(), Base_Text::get_params() ) );
                break;

              case 'in_qr_url':
              case 'in_qr_email':
              default:
                $this->register_fields( $tab, Base_Text::get_params() );

            endswitch; 

            settings_fields( IN_QR_CODE );     // скрытые защитные поля
            do_settings_sections( IN_QR_CODE );
            submit_button( __( 'Генерация QR', IN_QR_CODE ) ); // Кнопка сохранения
            ?>
            </div>
          </form>
        </div>
        <?php      
    }
  
  /**
   * Метод регистрирует поля по массиву со списком
   * @param string  $section_id   Идентификатор секции
   * @param mixed   $fields       Массив полей, для регистрации используются только ключи этого массива
   */
  private function register_fields( $section_id, $fields ) {
    // Создает новый блок (секцию), в котором выводятся поля настроек.
    add_settings_section( $section_id, '', '', IN_QR_CODE );
    // Определения полей
    $filed_def = $this->get_fields_definitions();
    
    // Удаляем ALT, он не нужен здесь
    unset( $fields[ 'alt' ] );

    // Добавление полей
    foreach ( array_keys( array_merge( array( 'content' => '' ), $fields ) ) as $field_id ) {
      add_settings_field( 
        $field_id,                          // Ярлык (slug) опции, используется как идентификатор поля.
        $filed_def[ $field_id ][ 'desc' ],  // Название поля.
        array( $this, 'show_field' ),       // Функция обратного вызова
        IN_QR_CODE,                         // Страница меню в которую будет добавлено поле
        $section_id,                        // Название секции настроек, в которую будет добавлено поле.
        array(                              // Дополнительные параметры, которые нужно передать callback функции
          'id'      => $field_id,
          'default' => ( isset( $fields[ $field_id ] ) ) ? $fields[ $field_id ] : ''
        )
       );
    }
  }

  /**
   * Метод отрисовывает поле ввода
   * @param mixed  $args   Аргументы вызова
   */
  public function show_field ( $args ) {
    // Определения полей
    $filed_def = $this->get_fields_definitions();    
    switch ( $filed_def[ $args[ 'id' ] ] ){

      default: ?> 
        <input type="text" 
          name="" 
          id="" 
          value=""
          placeholder="<?php echo $args[ 'default' ] ?>"
	      />
      <?php
    }
  }



  /**
   * Метод возвращает определение полей для страницы
   * @return mixed
   */
  public function get_fields_definitions() {
    return array(
      'content'     => array( 'type' => 'textarea', 'desc' => __( 'Содержимое QR кода', IN_QR_CODE ) ),
      'width'       => array( 'type' => 'int', 'desc' => __( 'Ширина QR кода', IN_QR_CODE ) ),
      'height'      => array( 'type' => 'int', 'desc' => __( 'Высота QR кода', IN_QR_CODE ) ),
      'size'        => array( 'type' => 'int', 'desc' => __( 'Размер «пикселя»', IN_QR_CODE ) ),
      'margin'      => array( 'type' => 'int', 'desc' => __( 'Отступ от краев, задаётся в «пикселях»', IN_QR_CODE ) ),
      'level'       => array( 'type' => 'select', 'desc' => __( 'Уровень коррекции ошибок', IN_QR_CODE ), 'options' => array(
        'L' => __( 'Избыточность 7%', IN_QR_CODE ),  
        'M' => __( 'Избыточность 15%', IN_QR_CODE ),  
        'Q' => __( 'Избыточность 25%', IN_QR_CODE ),  
        'H' => __( 'Избыточность 30%', IN_QR_CODE )
      ) ),
      'bgcolor'     => array( 'type' => 'color', 'desc' => __( 'Цвет фона', IN_QR_CODE ) ),
      'fgcolor'     => array( 'type' => 'color', 'desc' => __( 'Цвет пикселей', IN_QR_CODE ) ),
      'logo'        => array( 'type' => 'img', 'desc' => __( 'Логотип в изображении', IN_QR_CODE ) ),
      'alt'         => array( 'type' => 'text', 'desc' => __( 'Атрибут ALT', IN_QR_CODE ) ),
      'name'        => array( 'type' => 'text', 'desc' => __( 'Имя в визитке', IN_QR_CODE ) ),
      'title'       => array( 'type' => 'text', 'desc' => __( 'Должность', IN_QR_CODE ) ),
      'org'         => array( 'type' => 'text', 'desc' => __( 'Название компании', IN_QR_CODE ) ),            
      'birthday'    => array( 'type' => 'date', 'desc' => __( 'День рождения', IN_QR_CODE ) ),
      'address'     => array( 'type' => 'textarea', 'desc' => __( 'Адрес, поля разделяются точкой с запятой: дом и улица; населённый пункт; регион (штат, область); почтовый индекс; страна', IN_QR_CODE ) ),
      'tel'         => array( 'type' => 'tel', 'desc' => __( 'Телефон, предполагается рабочий', IN_QR_CODE ) ),
      'email'       => array( 'type' => 'email', 'desc' => __( 'E-mail', IN_QR_CODE ) ),
      'url'         => array( 'type' => 'text', 'desc' => __( 'Адрес сайта', IN_QR_CODE ) ),
      'note'        => array( 'type' => 'text', 'desc' => __( 'Заметки', IN_QR_CODE ) ),
      'payee'       => array( 'type' => 'text', 'desc' => __( 'Получатель платежа', IN_QR_CODE ) ),
      'acc'         => array( 'type' => 'text', 'desc' => __( 'Номер банковского счета получателя', IN_QR_CODE ) ),
      'bank'        => array( 'type' => 'text', 'desc' => __( 'Название банка', IN_QR_CODE ) ),           
      'bic'         => array( 'type' => 'text', 'desc' => __( 'БИК', IN_QR_CODE ) ),      
      'corr'        => array( 'type' => 'text', 'desc' => __( 'Корр.счет', IN_QR_CODE ) ),
      'inn'         => array( 'type' => 'text', 'desc' => __( 'ИНН получателя', IN_QR_CODE ) ),
      'kpp'         => array( 'type' => 'text', 'desc' => __( 'КПП получателя', IN_QR_CODE ) ),
      'last_name'   => array( 'type' => 'text', 'desc' => __( 'Фамилия плательщика', IN_QR_CODE ) ),
      'first_name'  => array( 'type' => 'text', 'desc' => __( 'Имя плательщика', IN_QR_CODE ) ),
      'middle_name' => array( 'type' => 'text', 'desc' => __( 'Отчество плательщика', IN_QR_CODE ) ),
      'payer_addr'  => array( 'type' => 'textarea', 'desc' => __( 'Адрес плательщика', IN_QR_CODE ) ),
      'purpose'     => array( 'type' => 'textarea', 'desc' => __( 'Назначение платежа', IN_QR_CODE ) ),
      'sum'         => array( 'type' => 'float', 'desc' => __( 'Сумма оплаты', IN_QR_CODE ) )
    ); 
  }
}