<?php /**
 * Класс генератора QR кода
 * ВЫполняет генерацию одного QR-кода с нужными параметрами по требованию
 * 
 * @package           in-qr-code
 * @author            Иван Никитин
 * @copyright         2022 IvanNikitin.com
 * @license           GPL-2.0-or-later
 */
namespace IN_QR_CODE;
use \Exception as Exception;

// Подключение библиотеки "PHP QR Code"
require_once IN_QR_CODE_DIR . '/phpqrcode/qrlib.php';
use \QRcode as QRcode;

// Исключения
class NoFileException extends Exception {};
class FolderCreateException extends Exception {};


class QR {
   /**
    * Файл c QR кодом
    * @var string
    */
    private $filename = '';

    /**
     * Экземпляр с классом типа QR кода
     */
    public $type = null;

    /**
     * Папка с кэшем QR кодов
     */
    const CACHE_DIR = '/cache/qr/';

   /**
    * Конструктор класса
    * Формирует имя временного файла для дальнейшей работы
    * 
    * @param Base_Text  $type   Тип QR кода
    */
    public function __construct( $type ) {
        // Тип QR кода
        $this->type = $type;

        // Проверка папки для кэша и создание ее при необходимости
        $cache_folder =  WP_CONTENT_DIR . self::get_cache_dir();
        if( ! is_dir( $cache_folder ) ) {
            if ( ! mkdir( $cache_folder, 0775, true ) ) {
                throw new FolderCreateException( __( 'Ошибка создания папки' . ' ' . $cache_folder, IN_QR_CODE ) );
            }
        }
    }

    /**
     * Статическая функция возвращает путь к папке кэша
     */
    public static function get_cache_dir() {
        return apply_filters( 'in_qr_code_cache_dir', self::CACHE_DIR );
    }

    /**
     * Функция возвращает полный путь к файлу с QR кодом
     */
    private function get_file_path() {
        return WP_CONTENT_DIR . self::get_cache_dir() . $this->filename;
    }

    /**
     * Функция возвращает полный путь к файлу с QR кодом
     */
    private function get_file_url() {
        return WP_CONTENT_URL . self::get_cache_dir() . $this->filename;
    }    

    /**
     * Возвращает полный путь к файлу с QR кодом
     */
    public function get_path() {
        if ( empty( $this->filename ) ) {
            // Генерация еще не производилась, выполняем ее
            $this->generate();
        }

        if ( ! file_exists( $this->get_file_path() ) ) {
            // Ошибка! файл не найден!
            throw new NoFileException( __( 'Файл c QR кодом не найден!', IN_QR_CODE ) );
        }

        return $this->get_file_path();
    }

    /**
     * Возвращает URL файла с QR кодом
     */
    public function get_url() {
        // Проверим наличие файла и генерируем при необходимости
        if ( empty( $this->get_path() ) ) {
            throw new NoFileException( __( 'Файл c QR кодом не найден!', IN_QR_CODE ) );
        }

        return $this->get_file_url();
    }

    /**
     * Возвращает элемент IMG с QR кодом
     */
    public function get_html() {
        return 
            $this->type->get_prefix() . 
            '<img src="' . $this->get_url() . 
            '" alt="' . $this->type->get_param( 'alt' ) . 
            '" width="' . $this->type->get_param( 'width' ) . 
            '" height="' . $this->type->get_param( 'height' ) . 
            '">' . 
            $this->type->get_suffix();
    }

    /**
     * Генерация QR кода
     */
    public function generate() {
        // Имя файла для кэша
        $this->filename = $this->type->get_hash() . '.png';

        // Подготовка данных
        $level = $this->type->get_param( 'level', 'L' );
        $size = $this->type->get_param( 'size', 3 );
        $margin = $this->type->get_param( 'margin', 2 );

        // Генерация QR кода
        QRcode::png( 
            $this->type->get_content(), // текст, который будет закодирован
            $this->get_file_path(),     // Выходной файл
            $level,                     // Уровень коррекции ошибок (L - 7%, M - 15%, Q - 25%, H - 30%)
            $size,                      // размер «пикселя», по умолчанию 3px
            $margin,                    // Отступ от краев, задаётся в единицах, указанных в $size
            false                       // если true, то изображение одновременно сохранится в файле $outfile и выведется в браузер
        );

        // Флаг модификации полученного изображения
        $image_modified = false;

        // Пост-обработка
        $qr = imagecreatefrompng( $this->get_file_path() );
        $qr_width = imagesx( $qr );
        $qr_height = imagesy( $qr );


        // Цвет и фон
        $bgcolor = $this->type->get_param( 'bgcolor', '#FFFFFF' );
        $fgcolor = $this->type->get_param( 'fgcolor', '#000000' );

        if ( $bgcolor != '#FFFFFF' || $fgcolor != '#000000' ) {
            // Цвет фона
            $is_transparent = 'transparent' == $bgcolor;
            $bgcolor = ( $is_transparent ) ? 'FFFFCC' : str_replace( '#', '', $bgcolor );
            $r = hexdec( substr( $bgcolor, 0, 2 ) );
            $g = hexdec( substr( $bgcolor, 2, 2 ) );
            $b = hexdec( substr( $bgcolor, 4, 2 ) );                
            $qr_bgcolor = imageColorAllocate($qr, $r, $g, $b );
            if ( $is_transparent ) imagecolortransparent( $qr, $qr_bgcolor );
   
            // Цвет пикселей
            $fgcolor = str_replace( '#', '', $fgcolor );
            $r = hexdec( substr( $fgcolor, 0, 2 ) );
            $g = hexdec( substr( $fgcolor, 2, 2 ) );
            $b = hexdec( substr( $fgcolor, 4, 2 ) );                
            $qr_fgcolor = imageColorAllocate($qr, $r, $g, $b);            

            // Проход по всем пикселям
            for ( $x = 0; $x < $qr_width; $x++ ) {
                for ( $y = 0; $y < $qr_height; $y++ ) {
                    $color = imagecolorat( $qr, $x, $y );
                    switch ( $color ) {
                        case 0:
                            imageSetPixel( $qr, $x, $y, $qr_bgcolor );
                            break;

                        case 1:
                            imageSetPixel( $qr, $x, $y, $qr_fgcolor );
                            break;
                    }
                }
            }
            // Изображение модифицировано
            $image_modified = true;
        }

        // Логотип в QR
        $logo = $this->type->get_param( 'logo' );
        $logo_file = WP_CONTENT_DIR . $logo;
        if ( ! empty( $logo ) && file_exists( $logo_file ) ) {
            $dst = imagecreatetruecolor( $qr_width, $qr_height );
            imagecopy( $dst, $qr, 0, 0, 0, 0, $qr_width, $qr_height );
            imagedestroy( $qr );

            /* Наложение логотипа */
            $logo_im = ( str_ends_with( $logo_file, '.png' ) ) ? imagecreatefrompng( $logo_file ) : imagecreatefromjpeg( $logo_file );
            $logo_width = imagesx( $logo_im );
            $logo_height = imagesy( $logo_im );

            $new_width = $qr_width / 3;
            $new_height = $logo_height / ( $logo_width / $new_width );

            $x = ceil( ($qr_width - $new_width ) / 2 );
            $y = ceil( ($qr_height - $new_height ) / 2 );

            imagecopyresampled( $dst, $logo_im, $x, $y, 0, 0, $new_width, $new_height, $logo_width, $logo_height );
            $qr = $dst;

            // Изображение модифицировано
            $image_modified = true;            
        }

        // При необходимости изменения размеров
        $width = $this->type->get_param( 'width' );
        $height = $this->type->get_param( 'height' );
        if ( empty( $width ) && empty( $height ) ) {
            // Размеры не указаны, вписываем полученные размеры
            $width = $this->type->set_param( 'width', $qr_width );
            $height = $this->type->set_param( 'height', $qr_height );
        }
        else {
            // Изменение размеров изображения
            $dst = imagecreatetruecolor( $width, $height );
            imagecopyresampled( $dst, $qr, 0, 0, 0, 0, $width, $height, $qr_width, $qr_height );
            imagedestroy( $qr );
            $qr = $dst;            
            $image_modified = true;
        }


        // Сохранение изображения
        if ( $image_modified ) imagepng( $qr, $this->get_file_path() );

    }
}