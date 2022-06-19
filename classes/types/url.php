<?php /**
 * URL в QR код
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */
namespace IN_QR_CODE;

class URL extends Base_Text { 
    /**
     * Возвращает строку до элемента QR кода
     */
    public function get_prefix() {
        return '<a href="' . $this->get_content() . '">';
    }
    
    /**
     * Возвращает строку после элемента QR кода
     */
    public function get_suffix() {
        return '</a>';
    }
}