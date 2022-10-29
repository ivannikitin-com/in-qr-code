<?php /**
 * vCard в QR код
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */
namespace IN_QR_CODE;

class VCard extends Base_Text { 
    /**
     * Возвращает массив настроек для этого типа
     * @static
     * @return mixed
     */
    public static function get_params() {
        return array(
            // Параметры vCard
            // https://ru.wikipedia.org/wiki/VCard
            'name'  => '',              // Имя пользователя
            'title'  => '',             // Должность
            'org'  => '',               // Название компании            
            'birthday'  => '',          // День рождения ГГГГ-ММ-ДД
            'address'  => '',           // Адрес, поля разделяются точкой с запятой: дом и улица; населённый пункт; регион (штат, область); почтовый индекс; страна
            'tel'  => '',               // Телефон, предполагается рабочий
            'email'  => '',             // E-mail
            'url'  => '',               // Адрес сайта
            'note'  => ''               // Заметки
        );
    }

    /**
     * Получает и сохраняет контент
     * 
     * @param string    $name       Имя пользователя, используется, если не задан параметр name
     * @param mixed     $params     Массив параметров, необходимый для генерации кода
     */
    public function __construct( $name, $params = array() ) {
        // Если $params не массив, заменяем его пустым массивом
        if ( ! is_array( $params ) ) $params = array();        
        parent::__construct( $name, array_merge( self::get_params(), array(
            'name'  => $name,           // Имя пользователя
        ), $params ) );
    }

    /**
     * Возвращает содержимое для генерации QR
     * @return string
     */
    public function get_content() {
        $vcard = 'BEGIN:VCARD' . PHP_EOL . 'VERSION:3.0' . PHP_EOL;
        foreach ( $this->params as $param => $value ) {
            if ( empty( $value ) ) continue;
            switch ( $param ) {
                case 'name':
                    $vcard .= 'FN:' . $value . PHP_EOL;
                    break;

                case 'title':
                    $vcard .= 'TITLE:' . $value . PHP_EOL;
                    break;

                case 'org':
                    $vcard .= 'ORG:' . $value . PHP_EOL;
                    break;

                case 'birthday':
                    $vcard .= 'BDAY:' . $value . PHP_EOL;
                    break;

                case 'address':
                    $vcard .= 'ADR;TYPE=postal,parcel:' . $value . PHP_EOL;
                    break;

                case 'tel':
                    $vcard .= 'TEL;TYPE=work,voice,pref:' . $value . PHP_EOL;
                    break;

                case 'email':
                    $vcard .= 'EMAIL;TYPE=INTERNET:' . $value . PHP_EOL;
                    break;

                case 'url':
                    $vcard .= 'URL:' . $value . PHP_EOL;
                    break;

                case 'note':
                    $vcard .= 'NOTE:' . $value . PHP_EOL;
                    break;
            }
        }
        $vcard .= 'END:VCARD';
        return $vcard;
    }    
}