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
     * Получает и сохраняет контент
     * 
     * @param string    $payment    Назначение платежа
     * @param mixed     $params     Массив параметров, необходимый для генерации кода
     */
    public function __construct( $payment, $params = array() ) {
        // Если $params не массив, заменяем его пустым массивом
        if ( ! is_array( $params ) ) $params = array();        
        parent::__construct( $payment, array_merge( array(
            // Параметры для QR оплаты
            'name'  => '',              // Получатель платежа
            'acc'  => '',               // Номер банковского счета получателя
            'bank'  => '',              // Название банка            
            'bic'  => '',               // БИК      
            'corr'  => '',              // Корр.счет
            'inn'  => '',               // ИНН получателя            
            'kpp'  => '',               // КПП получателя            
            'last_name'  => '',         // Фамилия плательщика
            'first_name'  => '',        // Имя плательщика
            'middle_name'  => '',       // Отчество плательщика
            'address'  => '',           // Адрес плательщика
            'purpose'  => $payment,     // Назначение платежа
            'sum'  => 0                 // Сумма оплаты
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
                case 'name':
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

                case 'address':
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