<?php /**
 * Базовый класс типа QR кода
 * ВЫполняет формирование содержания QR-кода
 * Этот класс просто получает строку текста и возвращает ее для генерации QR кода
 * Остальные классы наследуются от этого класса
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */
namespace IN_QR_CODE;

class Base_Text {
    /**
     * Сохраненная текстовая строка содержания QR
     * @var string
     */
    protected $content = '';

    /**
     * Сохраненная текстовая строка содержания QR
     * @var mixed
     */
    protected $params = array();


    /**
     * Получает и сохраняет контент
     * 
     * @param string    $content    Тестовая строка для генерации кода
     * @param mixed     $params     Массив параметров, необходимый для генерации кода
     */
    public function __construct( $content, $params = array() ) {
        $this->content = $content;
        
        // Если $params не массив, заменяем его пустым массивом
        if ( ! is_array( $params ) ) $params = array();

        // Список параметров, нужный для генерации кода
        $this->params = array_merge( array(
            'width'     => '',              // Ширина QR кода
            'height'    => '',              // Высота QR кода
            'size'      => 3,               // размер «пикселя»
            'margin'    => 2,               // Отступ от краев, задаётся в единицах, указанных в $size
            'level'     => 'L',             // Уровень коррекции ошибок (L, M, Q, H)
            'bgcolor'   => '#FFFFFF',       // Цвет фона, может быть transparent
            'fgcolor'   => '#000000',       // Цвет пикселей
            'logo'      => null,            // Логотип в изображении
            'alt'       => $content         // Атрибут ALT
        ), $params );
    }

    /**
     * Возвращает содержимое для генерации QR
     * @return string
     */
    public function get_content() {
        return $this->content;
    }

    /**
     * Возвращает значение выбранного параметра
     * @param string    $param      Название параметра
     * @param string    $default    Значение по умолчанию
     * @return string
     */
    public function get_param( $param, $default = '' ) {
        if ( ! isset( $this->params[ $param ] ) ) return $default;
        return $this->params[ $param ];
    }

    /**
     * Сохраняет новое значение выбранного параметра
     * @param string    $param      Название параметра
     * @param string    $value      Значение параметра
     */
    public function set_param( $param, $value ) {
        $this->params[ $param ] = $value;
    }    

    /**
     * Возвращает хэш, зависящий от контента и параметров для кэширования
     * @return string
     */
    public function get_hash() {
        return md5( mb_strtolower( trim( $data . serialize( $this->params ) ) ) );
    }

    /**
     * Возвращает строку до элемента QR кода
     */
    public function get_prefix() {
        return '';
    }
    
    /**
     * Возвращает строку после элемента QR кода
     */
    public function get_suffix() {
        return '';
    }
}