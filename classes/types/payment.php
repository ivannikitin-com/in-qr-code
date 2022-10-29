<?php /**
 * Платежные реквизиты в QR код
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */
namespace IN_QR_CODE;

class Payment extends Base_Text { 
    /**
     * Возвращает массив настроек для этого типа
     * @static
     * @return mixed
     */
    public static function get_params() {
        return array(
            // Параметры для QR оплаты
            'payee'  => '',             // Получатель платежа
            'acc'  => '',               // Номер банковского счета получателя
            'bank'  => '',              // Название банка            
            'bic'  => '',               // БИК      
            'corr'  => '',              // Корр.счет
            'inn'  => '',               // ИНН получателя            
            'kpp'  => '',               // КПП получателя            
            'last_name'  => '',         // Фамилия плательщика
            'first_name'  => '',        // Имя плательщика
            'middle_name'  => '',       // Отчество плательщика
            'payer_addr'  => '',        // Адрес плательщика
            'purpose'  => '',           // Назначение платежа
            'sum'  => 0                 // Сумма оплаты
        );
    }


    /**
     * Получает и сохраняет контент
     * 
     * @param string    $payment    Назначение платежа
     * @param mixed     $params     Массив параметров, необходимый для генерации кода
     */
    public function __construct( $payment, $params = array() ) {
        // Если $params не массив, заменяем его пустым массивом
        if ( ! is_array( $params ) ) $params = array();        
        parent::__construct( $payment, array_merge( self::get_params(),  array(
            'purpose'  => $payment,     // Назначение платежа
        ), $params ) );
    }

    /**
     * Возвращает содержимое для генерации QR
     * @return string
     */
    public function get_content() {
        $content = 'ST00012';
        foreach ( $this->params as $param => $value ) {
            if ( empty( $value ) ) continue;
            switch ( $param ) {
                case 'payee':
                    $content .= '|Name=' . $value;
                    break;

                case 'acc':
                    $content .= '|PersonalAcc=' . $value;
                    break;

                case 'bank':
                    $content .= '|BankName=' . $value;
                    break;

                case 'bic':
                    $content .= '|BIC=' . $value;
                    break;

                case 'corr':
                    $content .= '|CorrespAcc=' . $value;
                    break;

                case 'inn':
                        $content .= '|PayeeINN=' . $value;
                        break;                    

                case 'kpp':
                        $content .= '|KPP=' . $value;
                        break;                    

                case 'last_name':
                    $content .= '|LastName=' . $value;
                    break;

                case 'first_name':
                    $content .= '|FirstName=' . $value;
                    break;

                case 'middle_name':
                    $content .= '|MiddleName=' . $value;
                    break;

                case 'payer_addr':
                    $content .= '|PayerAddr' . $value;
                    break;

                case 'purpose':
                    $content .= '|Purpose=' . $value;
                    break;

                case 'sum':
                    $content .= '|Sum=' . $value . PHP_EOL;
                    break;
            }
        }
        return $content;
    }    
}