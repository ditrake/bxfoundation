# Bxfoundation

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxfoundation/v/stable.png)](https://packagist.org/packages/marvin255/bxfoundation)
[![Total Downloads](https://poser.pugx.org/marvin255/bxfoundation/downloads.png)](https://packagist.org/packages/marvin255/bxfoundation)
[![License](https://poser.pugx.org/marvin255/bxfoundation/license.svg)](https://packagist.org/packages/marvin255/bxfoundation)
[![Build Status](https://travis-ci.org/marvin255/bxfoundation.svg?branch=master)](https://travis-ci.org/marvin255/bxfoundation)

Набор дополнительных инструметов для 1С-Битрикс "Управление сайтом".



## Оглавление

* [Установка](#Установка).
* [Service locator](#Service-locator).
* [Роутинг](#Роутинг).
* [Сервисы по умолчанию](#Сервисы-по-умолчанию).



## Установка

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md)**

1. Добавьте в ваш composer.json в раздел `require`:

    ```javascript
    "require": {
        "marvin255/bxfoundation": "*"
    }
    ```

2. Если требуется автоматическое обновление библиотеки через composer, то добавьте в раздел `scripts`:

    ```javascript
    "scripts": [
        {
            "post-install-cmd": "\\marvin255\\bxfoundation\\installer\\Composer::injectModule",
            "post-update-cmd": "\\marvin255\\bxfoundation\\installer\\Composer::injectModule",
        }
    ]
    ```

3. Выполните в консоли внутри вашего проекта:

    ```
    composer update
    ```

4. Если пункт 2 не выполнен, то скопируйте папку `vendors/marvin255/bxfoundation/marvin255.bxfoundation` в папку `local/modules` вашего проекта.

5. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".

**Обычная**

1. Скачайте архив с репозиторием.
2. Скопируйте папку `marvin255.bxfoundation` из архива репозитория в папку `local/modules` вашего проекта.
3. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".



## Service locator

В D7 появился общий объект для приложения, который доступен из каждого файла скрипта сайта. Тем не менее, он не позволяет эффективно и легально добавлять к себе определения сервисов, которые используются на сайте.

В дополнение к объекту `\Bitrix\Main\Application` предлагается использовать объект `\marvin255\bxfoundation\application\Application`, который не только дает доступ ко всем стандартным методам `\Bitrix\Main\Application` (по факту, он просто содержит в себе ссылку на `\Bitrix\Main\Application` и переадресует на него вызовы всех методов, которые не определены в самом `\marvin255\bxfoundation\application\Application`), но и в дополнение реализует паттерн service locator.

Для использования достаточно объявить в блоке use `\marvin255\bxfoundation\application\Application` вместо `\Bitrix\Main\Application`.

```php
//init.php

use Bitrix\Main\Loader;
use marvin255\bxfoundation\application\Application;

Loader::includeModule('marvin255.bxfoundation');
$app = Application::getInstance();

//регистрируем новый сервис
$app->locator->set('my_new_service', new MyNewService);
```

```php
//сервис будет доступен в любом другом файле скрипта сайта с помошью

use marvin255\bxfoundation\application\Application;

$my_new_service = Application::getInstance()->locator->get('my_new_service');
//либо более коротко
$my_new_service = Application::getInstance()->my_new_service;
```
