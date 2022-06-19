<?php /**
 * E-mail в QR код
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */
namespace IN_QR_CODE;

class Email extends URL { 
    /**
     * Получает и сохраняет контент
     * 
     * @param string    $email    E-mail
     * @param mixed     $params     Массив параметров, необходимый для генерации кода
     */
    public function __construct( $email, $params = array() ) {
        parent::__construct( 'mailto:' . $email, $params );
    }
}