# Bxfoundation

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxfoundation/v/stable.png)](https://packagist.org/packages/marvin255/bxfoundation)
[![Total Downloads](https://poser.pugx.org/marvin255/bxfoundation/downloads.png)](https://packagist.org/packages/marvin255/bxfoundation)
[![License](https://poser.pugx.org/marvin255/bxfoundation/license.svg)](https://packagist.org/packages/marvin255/bxfoundation)
[![Build Status](https://travis-ci.org/marvin255/bxfoundation.svg?branch=master)](https://travis-ci.org/marvin255/bxfoundation)



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
            "post-install-cmd": "\\creative\\foundation\\installer\\Composer::injectModule",
            "post-update-cmd": "\\creative\\foundation\\installer\\Composer::injectModule",
        }
    ]
    ```

3. Выполните в консоли внутри вашего проекта:

    ```
    composer update
    ```

4. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".

**Обычная**

1. Скачайте архив с репозиторием.
2. Скопируте папку `marvin255.bxfoundation` из архива репозитория в папку `local/modules` вашего проекта.
3. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".
