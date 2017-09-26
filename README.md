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

В дополнение к объекту `\Bitrix\Main\Application` предлагается использовать объект `\marvin255\bxfoundation\application\Application`, который не только дает доступ ко всем стандартным методам `\Bitrix\Main\Application` (по факту, он просто содержит в себе ссылку на `\Bitrix\Main\Application` и переадресует на него вызовы всех методов, которые не определены в самом `\marvin255\bxfoundation\application\Application` с помощью `__call`), но и в дополнение реализует паттерн service locator.

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
//сервис будет доступен в любом другом файле скрипта сайта с помощью

use marvin255\bxfoundation\application\Application;

$my_new_service = Application::getInstance()->locator->get('my_new_service');
//либо более коротко
$my_new_service = Application::getInstance()->my_new_service;
```



## Роутинг

В качестве роутинга в битриксе предлагается использовать комбинацию из комплексных компонентов и urlrewrite.php, но такой подход не всегда позволяет очевидно определить откуда именно будет отображена страница. Кроме того, возникает необходимость в постоянном дублировании шаблонов комплексных компонентов.

В замен такому подходу предлагается использовать объект для роутинга запросов.

Использование данного объекта требует, чтобы все входящие запросы были бы переадресованы на главную страницу (index.php), пример настройки urlrewrite для такого случая:

```php
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/#",
		"RULE" => "",
		"ID" => "page",
		"PATH" => "/index.php",
	),
);
```

Пример главной страницы:

```php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use marvin255\bxfoundation\application\Application;
use marvin255\bxfoundation\routing\rule\Regexp;
use marvin255\bxfoundation\routing\action\Component;
use marvin255\bxfoundation\routing\action\Chain;

$app = Application::getInstance();

//список новостей
$app->router->registerRoute(
    new Regexp('/news'),
    new Chain([
        new Component('bitrix:catalog.filter', '', [
            'IBLOCK_TYPE' => 'content',
            'IBLOCK_ID' => '1',
            'FILTER_NAME' => 'news_filter',
            'FIELD_CODE' => ['NAME', 'ACTIVE_FROM'],
        ]),
        new Component('bitrix:news.list', '', [
            'IBLOCK_TYPE' => 'content',
            'IBLOCK_ID' => '1',
            'FILTER_NAME' => 'news_filter',
        ])
    ])
);

//новость детально
$app->router->registerRoute(
    new Regexp('/news/<ID:\d+>'),
    new Component('bitrix:news.detail', '', [
        'IBLOCK_TYPE' => 'content',
        'IBLOCK_ID' => '1',
        'ELEMENT_ID' => '$ruleResult.ID',
    ])
);

//страница 404
$app->router->registerRouteException(404, new Component('bitrix:search.page'));

//роутим текущую ссылку и печатаем результат
echo $app->router->route($app->request, $app->response);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
```

Каждый роут состоит из пары объектов: правила, которое реализует `\marvin255\bxfoundation\routing\rule\RuleInterface`, и действия, которое реализует `\marvin255\bxfoundation\routing\action\ActionInterface`. Соответственно, объект роутер позволяет зарегистрировать любое множество таких пар правило-действие с помощью метода `registerRoute`. При запросе результата роутер будет последовательно вызывать каждое правило до тех пор, пока одно из них не ответит, что url подходит, после этого будет вызвано соответствующее правилу действие, которое вернет строку для отображения. Если же все правила будут опробованы и url не будет соответствовать ни одному из них, то роутер выбросит исключение со статусом 404.

Правило может получить некоторые данные из url, как, например, идентификатор новости из примера выше. Эти данные будут так же переданы в действие и доступны с помощью макроса `'$ruleResult.ID'`, где вместо ID нужно будет указать имя параметра, которое получило правило.

В качестве правила и действия можно передавать любые объекты, которые реализуют `\marvin255\bxfoundation\routing\rule\RuleInterface` и `\marvin255\bxfoundation\routing\action\ActionInterface` соответственно.

Кроме того, можно зарегистрировать действие на тот случай, если действие или правило вернет исключение. Для этого служит метод `registerRouteException` роутера. Первым параметром нужно передать код статуса состояния (как в примере выше: 404, страница не найдена) и соответствующий этому статусу объект, который реализует `\marvin255\bxfoundation\routing\action\ActionInterface`.

**Классы правил, которые поставляются с библиотекой:**

`\marvin255\bxfoundation\routing\rule\Regexp`

В качестве единственного элемента конструктора принимает регулярное выражение, котором должен соответствовать переданный url. С помощью синтаксиса вида `<ИмяПараметра: регулярное выражение для соответствия>` можно задать часть url, которая может изменяться согласно указанному регулярному выражению.

`\marvin255\bxfoundation\routing\rule\Iblock`

Правило, которое сравнивает ссылку с правилами указанными в настройках соответствующего инфоблока (DETAIL_PAGE_URL, SECTION_PAGE_URL или LIST_PAGE_URL). В качестве первого параметра принимает объект класса `\marvin255\bxfoundation\services\iblock\Locator`, который служит для получения данных об инфоблоках, вторым параметром - код инфоблока, третьим - массив сущностей указанного инфоблока, для которых сработает данное правило (iblock, section или element).

**Классы действий, которые поставляются с библиотекой:**

`\marvin255\bxfoundation\routing\action\Component`

Действие, которое просто вызывает указанный в конструкторе компонент Битрикса. В качестве первого параметра принимает имя компонента, как для вызова `$APPLICATION->includeComponent()`, второго - название шаблона компонента, третьего - массив параметров (arParams), которые будут переданы в компонент при вызове.

`\marvin255\bxfoundation\routing\action\Chain`

Действие, которое позволяет объединить вызов нескольких действий в цепочку. В качестве единственного аргумента в конструктор принимает массив объектов, реализующих `\marvin255\bxfoundation\routing\action\ActionInterface`.
