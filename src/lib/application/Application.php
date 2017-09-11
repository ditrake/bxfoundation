<?php

namespace creative\foundation\application;

use creative\foundation\request\Bitrix as Request;
use creative\foundation\response\Bitrix as Response;
use creative\foundation\routing\router\Router;

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
     * Объект приложения битрикса.
     *
     * @var \Bitrix\Main\Application
     */
    protected $bitrixApplication = null;
    /**
     * Объект service locator.
     *
     * @var \creative\foundation\application\ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Магия. Передаем неизвестные данному объекту функции в Application или service locator.
     *
     * @param string $name
     * @param array $params
     *
     * @return mixed
     *
     * @throws \creative\foundation\application\Exception
     */
    public function __call($name, array $params)
    {
        if (method_exists($this->bitrixApplication, $name)) {
            return call_user_func_array(
                [$this->bitrixApplication, $name],
                $params
            );
        } else {
            throw new Exception("Method {$name} doesn't exist");
        }
    }

    /**
     * Магия. Возвращает объект service locator и не дает
     * возможности его заменить.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \creative\foundation\application\Exception
     */
    public function __get($name)
    {
        if ($name === 'locator') {
            return $this->serviceLocator;
        } else {
            throw new Exception("Property {$name} doesn't exist");
        }
    }

    /**
     * Конструктор.
     *
     * Реализация singleton. Видимость private апрещает прямое создание новых объектов.
     *
     * @param \Bitrix\Main\Application $bitrixApplication
     * @param \creative\foundation\application\ServiceLocatorInterface $serviceLocator
     */
    private function __construct(\Bitrix\Main\Application $bitrixApplication = null, ServiceLocatorInterface $serviceLocator = null)
    {
        if (empty($bitrixApplication)) {
            $bitrixApplication = \Bitrix\Main\Application::getInstance();
        }
        $this->bitrixApplication = $bitrixApplication;

        if (empty($serviceLocator)) {
            $serviceLocator = new ServiceLocator;
        }
        $this->serviceLocator = $serviceLocator;
        $this->setDefaultServices($bitrixApplication, $serviceLocator);
    }

    /**
     * Задает сервисы по умолчанию.
     *
     * @param \Bitrix\Main\Application $bitrixApplication
     * @param \creative\foundation\application\ServiceLocatorInterface $serviceLocator
     */
    protected function setDefaultServices(\Bitrix\Main\Application $bitrixApplication, ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('request')) {
            $serviceLocator->set(
                'request',
                new Request($bitrixApplication->getContext()->getRequest())
            );
        }
        if (!$serviceLocator->has('response')) {
            $serviceLocator->set(
                'response',
                new Response($bitrixApplication->getContext()->getResponse())
            );
        }
        if (!$serviceLocator->has('router')) {
            $serviceLocator->set('router', new Router);
        }
        if (!$serviceLocator->has('db')) {
            $serviceLocator->set('db', $bitrixApplication->getConnection());
        }
        if (!$serviceLocator->has('cache')) {
            $serviceLocator->set('cache', $bitrixApplication->getCache());
        }
        if (!$serviceLocator->has('managed_cache')) {
            $serviceLocator->set(
                'managed_cache',
                $bitrixApplication->getManagedCache()
            );
        }
        if (!$serviceLocator->has('tagged_cache')) {
            $serviceLocator->set(
                'tagged_cache',
                $bitrixApplication->getTaggedCache()
            );
        }
    }


    /**
     * Объект для реализации singleton.
     *
     * @var \creative\foundation\application\Application
     */
    static private $instance = null;

    /**
     * Возвращает объект singleton, если он уже создан, либо создает новый
     * и возвращает новый.
     *
     * @return \creative\foundation\application\Application
     */
    static public function getInstance()
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
