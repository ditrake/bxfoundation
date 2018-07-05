<?php

namespace marvin255\bxfoundation\application;

use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\response\Bitrix as Response;
use marvin255\bxfoundation\routing\router\Router;
use marvin255\bxfoundation\services\cache\Bitrix as LibCache;
use marvin255\bxfoundation\services\iblock\Locator as IblockLocator;
use marvin255\bxfoundation\services\config\BitrixOptions;
use marvin255\bxfoundation\services\user\Bitrix as BitrixUser;

/**
 * Класс-фасад для битриксового Bitrix\Main\Application.
 *
 * Использует в своей основе паттерн Singleton. А, кроме того, использует
 * service locator. При запросе неизвестного метода обращается
 * к объекту Bitrix\Main\Application.
 */
class Application
{
    /**
     * Объект для реализации singleton.
     *
     * @var \marvin255\bxfoundation\application\Application
     */
    private static $instance;
    /**
     * Объект приложения битрикса.
     *
     * @var \Bitrix\Main\Application
     */
    protected $bxApp;
    /**
     * Объект service locator.
     *
     * @var \marvin255\bxfoundation\application\ServiceLocatorInterface
     */
    protected $locator;

    /**
     * Конструктор
     */
    private function __construct()
    {
        $this->bxApp = \Bitrix\Main\Application::getInstance();
        $this->locator = new ServiceLocator;
        $this->setDefaultServices();
    }

    /**
     * Магия. Передаем неизвестные данному объекту функции в Application или service locator.
     *
     * @param string $name
     * @param array  $params
     *
     * @return mixed
     *
     * @throws \marvin255\bxfoundation\application\Exception
     */
    public function __call($name, array $params)
    {
        if (!method_exists($this->bxApp, $name)) {
            throw new Exception("Method {$name} doesn't exist");
        }

        return call_user_func_array([$this->bxApp, $name], $params);
    }

    /**
     * Магия. Возвращает объект service locator и не дает
     * возможности его заменить.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \marvin255\bxfoundation\application\Exception
     */
    public function __get($name)
    {
        if ($name === 'locator') {
            $return = $this->locator;
        } elseif ($this->locator->has($name)) {
            $return = $this->locator->get($name);
        } else {
            throw new Exception("Property {$name} doesn't exist");
        }

        return $return;
    }

    /**
     * Задает сервисы по умолчанию.
     */
    protected function setDefaultServices()
    {
        $this->locator->set(
            'request',
            new Request($this->bxApp->getContext()->getRequest())
        );
        $this->locator->set(
            'response',
            new Response($this->bxApp->getContext()->getResponse())
        );
        $this->locator->set('router', new Router);
        $this->locator->set('db', $this->bxApp->getConnection());
        $this->locator->set(
            'cache',
            new LibCache($this->bxApp->getCache(), $this->bxApp->getTaggedCache())
        );
        $this->locator->set(
            'iblockLocator',
            new IblockLocator(null, null, $this->locator->get('cache'))
        );
        $this->locator->set('options', new BitrixOptions);
        $this->locator->set('user', new BitrixUser);
    }

    /**
     * Возвращает объект singleton, если он уже создан, либо создает новый
     * и возвращает новый.
     *
     * @return \marvin255\bxfoundation\application\Application
     */
    public static function getInstance()
    {
        return self::$instance === null
            ? self::$instance = new self
            : self::$instance;
    }

    /**
     * Реализация singleton. Запрещает клонирование объектов.
     */
    private function __clone()
    {
    }

    /**
     * Реализация singleton. Запрещает извлечение сериализованных объектов.
     */
    private function __wakeup()
    {
    }
}
