IN QR code
==========

Плагин гибкой генерации QR кодов для WordPress.

Существующие плагины генерации QR кодов генерируют коды преимущественно для URL
и, как правило, динамически. Основная идея этого плагина заключается в следующем:
1. Генерация любого типа QR-кодов (URL, vCard, платежные коды).
2. Полная настраиваемость кода (размеры, цвета, логотип и т.п.).
3. Вызов как через шорт-код, так и через хук-фильтр для интеграции с другими плагинами.
4. Как динамическая генерация, так и сохранение сгенерированных кодов в wp-content,
   чтобы можно было их использовать, например, для вставки в генерируемые PDF (счета и т.п.)
5. Генератор QR кодов в админке сайта.

Генерация QR-кодов осуществляется библиотекой [PHP QR Code](http://phpqrcode.sourceforge.net/).

## Шорт-коды
Плагин реализует следующие шорт-коды:
* `[in_qr_text]`  -- Создание QR с текстом
* `[in_qr_url]`  -- Создание QR с произвольным URL
* `[in_qr_email]` -- Создание QR с E-mail ссылкой
* `[in_qr_vcard]` -- Создание QR с визитной карточкой
* `[in_qr_payment]` -- Создание QR с платежными реквизитами

Все шорткоды поддерживают большой список параметров.

### Общие параметры всех шорткодов
* width -- Может задавать ширину генерируемого кода, по умолчанию вычисляется после генерации QR
* height -- Может задавать высоту генерируемого кода, по умолчанию вычисляется после генерации QR
* size -- Задает размер пикселя QR. По умолчанию 3
* margin -- Отступ от краев, задаётся в единицах, указанных в size
* level -- Уровень коррекции ошибок (L - 7%, M - 15%, Q - 25%, H - 30%). Задает избыточность кода QR
* bgcolor -- Цвет фона, может быть transparent, по умолчанию #FFFFFF
* fgcolor -- Цвет пикселя, может быть transparent, по умолчанию #000000
* logo -- Вставка логотипа в QR. Задается как относительный путь от wp-content.  
          Для правильной генерации обязательно должен быть установлен level как "H"
* alt -- Атрибут ALT изображения QR. По умолчанию -- переданная строка контента

**Важно**. Если указаны параметры `width` и `height` то производится ресемплирование и изменение изображения QR после всех манипуляций с ним, в том числе после вставки логотипа, что может привести к "замыливанию" вставленного логотипа. Рекомендуем не указывать эти параметры, а изменять размеры QR кода параметром размера пикселя `size`. 

## Шорт-код in_qr_text
Кодирует произвольный текст. 

Примеры использования:

### Просто текст
```
[in_qr_text]Привет, мир![/in_qr_text]
```
![Привет, мир!](asserts/demo/text-1.png)


### QR синего цвета с прозрачным фоном
```
[in_qr_text fgcolor="#0000CC" bgcolor="transparent"]Привет Мир![/in_qr_text]
```
![Привет, мир!](asserts/demo/text-2.png)

### QR с текстом и произвольным логотипом
```
[in_qr_text level="H" logo="/uploads/2022/06/wordpress-plugin.jpg"]Привет Мир![/in_qr_text]
```
![Привет, мир!](asserts/demo/text-3.png)

## Шорткод in_qr_url
Кодирует URL и выводит изображение ссылкой на этот URL.

Примеры использования:

### Ссылка на сайт
```
[in_qr_url]https://github.com/ivannikitin-com/in-qr-code[/in_qr_url]
```
![Пример URL](asserts/demo/url-1.png)

## Ссылка на сайт размером 300x300
```
[in_qr_url width="300" height="300"]https://github.com/ivannikitin-com/in-qr-code[/in_qr_url]
```
![Пример URL](asserts/demo/url-2.png)


## Шорткод in_qr_email
Кодирует E-mail и выводит изображение ссылкой на этот URL.

Примеры использования:

### QR с E-mail 
```
[in_qr_email]test@example.com[/in_qr_email]
```
![test@example.com](asserts/demo/email.png)


## Шорткод in_qr_vcard
Кодирует визитную карточку в формате [vCard](https://ru.wikipedia.org/wiki/VCard).
Используются следующие дополнительные атрибуты шорт-кода:
* `name` -- Имя пользователя, по умолчанию содержимое шорт-кода
* `title` -- Должность
* `org` -- Название компании
* `birthday` -- День рождения ГГГГ-ММ-ДД
* `address` -- Адрес, поля разделяются точкой с запятой: дом и улица; населённый пункт; регион (штат, область); почтовый индекс; страна
* `tel` -- Телефон, предполагается рабочий
* `email` -- E-mail
* `url` -- Адрес сайта
* `note` -- Заметки

### Визитка в QR 
```
[in_qr_vcard 
   title="Директор" 
   org="Рога и копыта"
   address="1;Набережная ул.;г.Черноморск;Одесская обл.;20830;СССР"
   tel="+48(2)999-99-99"
   email="o.bender@mail.ru"
]Остап Бендер[/in_qr_vcard]
```
![Остап Бендер](asserts/demo/vcard.png)


## Шорткод in_qr_payment
Кодирует платежные реквизиты в [ГОСТ Р 56042-2014](https://docs.cntd.ru/document/1200110981).
Используются следующие дополнительные атрибуты шорт-кода:
* `payee` -- Получатель платежа
* `acc` -- Номер банковского счета получателя
* `bank` -- Название банка
* `bic` -- БИК банка
* `corr` -- Корр.счет
* `inn` -- ИНН получателя
* `kpp` -- КПП плательщика
* `last_name` -- Фамилия плательщика или название организации плательщика
* `first_name` -- Имя плательщика
* `middle_name` -- Отчество плательщика
* `payer_addr` -- Адрес плательщика
* `purpose` -- Назначение платежа
* `sum` -- Сумма оплаты

### QR код для оплаты банковским приложением
```
[in_qr_payment 
   payee="ИП Никитин И.Г." 
   acc="40802810102680000003"
   bank="ОАО Альфа-Банк"
   bic="044525593"
   inn="501810901400"
   last_name="Пупкин"
   first_name="Василий"
   sum="100"
]Добровольное пожертвование на кофе разработчику плагина[/in_qr_payment]
```
![Пожертвование на кофе](asserts/demo/payment.png)

## Программный вызов генерации QR из произвольного места
Программный вызов генерации позволяет сгенерировать и получить URL на QR код из любого плагина,
любой темы, любого программного кода. Вызов производится обращением к фильтру c именем шорт-кода
и теми же параметрами, что и у данного шорт-кода.

При вызове фильтру передается два параметра:
1. Содержимое, которое используется в шорт-коде 
2. Массив с параметрами (те же параметры, что и у шорт-кодов)

Например, нужно получить QR к E-mail, синими пикселями и прозрачным фоном в коде темы.
Используйте следующий код:

```php
$params = array(
   'fgcolor' => '#0000CC', 
   'bgcolor' => 'transparent'
);
$qr = apply_filters( 'in_qr_email', 'test@example.com', $params );
echo '<img src="' . $qr . '" alt="Мой QR код">';
```
![test@example.com](asserts/demo/email-2.png)
