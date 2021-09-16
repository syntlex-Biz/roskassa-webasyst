<?php
return array(
    'url' => array(
        'value' => 'https://pay.roskassa.net/form/',
        'title' => 'URL мерчанта',
        'description' => 'url для оплаты в системе Ros-Kassa',
        'control_type' => waHtmlControl::INPUT,
    ),
    'shop_id' => array(
        'value' => '',
        'title' => 'Идентификатор магазина',
        'description' => 'Идентификатор магазина, зарегистрированного в системе Roskassa',
        'control_type' => waHtmlControl::INPUT,
    ),
    'key1' => array(
        'value' => '',
        'title' => 'Первый секретный ключ',
        'description' => 'Должен совпадать с секретным ключем , указанным в <a href="https://my.roskassa.net/shop-settings/">аккаунте Ros-Kassa</a>',
        'control_type' => waHtmlControl::INPUT,
    ),

    'email_error' => array(
        'value' => '',
        'title' => 'Email для ошибок',
        'description' => 'Email для отправки ошибок оплаты',
        'control_type' => waHtmlControl::INPUT,
    ),
    'test_mode' => array(
        'value' => '1',
        'title' => 'Тестовый режим',
        'description' => 'Тестовый режим',
        'control_type' => waHtmlControl::CHECKBOX,
    ),

);

