<?php

namespace marvin255\bxfoundation\application;

use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\response\Bitrix as Response;
use marvin255\bxfoundation\routing\router\Router;
use marvin255\bxfoundation\services\cache\Bitrix as LibCache;
use marvin255\bxfoundation\services\iblock\Locator as IblockLocator;
use marvin255\bxfoundation\services\config\BitrixOptions;
use marvin255\bxfoundation\services\user\Bitrix as BitrixUser;
use marvin255\bxfoundation\view\PhpView;

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
    private static $instance = null;
    /**
     * Объект приложения битрикса.
     *
     * @var \Bitrix\Main\Application
     */
    protected $bitrixApplication = null;
    /**
     * Объект service locator.
     *
     * @var \marvin255\bxfoundation\application\ServiceLocatorInterface
     */
    protected $serviceLocator = null;

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
     * @throws \marvin255\bxfoundation\application\Exception
     */
    public function __get($name)
    {
        if ($name === 'locator') {
            return $this->serviceLocator;
        } elseif ($this->serviceLocator->has($name)) {
            return $this->serviceLocator->get($name);
        } else {
            throw new Exception("Property {$name} doesn't exist");
        }
    }

    /**
     * Конструктор.
     *
     * Реализация singleton. Видимость private апрещает прямое создание новых объектов.
     *
     * @param \Bitrix\Main\Application                                    $bitrixApplication
     * @param \marvin255\bxfoundation\application\ServiceLocatorInterface $serviceLocator
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
     * @param \Bitrix\Main\Application                                    $bitrixApplication
     * @param \marvin255\bxfoundation\application\ServiceLocatorInterface $serviceLocator
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
            $serviceLocator->set(
                'cache',
                new LibCache($bitrixApplication->getCache(), $bitrixApplication->getTaggedCache())
            );
        }
        if (!$serviceLocator->has('iblockLocator')) {
            $serviceLocator->set(
                'iblockLocator',
                new IblockLocator(
                    null,
                    null,
                    $serviceLocator->has('cache') ? $serviceLocator->get('cache') : null
                )
            );
        }
        if (!$serviceLocator->has('options')) {
            $serviceLocator->set('options', new BitrixOptions);
        }
        if (!$serviceLocator->has('user')) {
            $serviceLocator->set('user', new BitrixUser);
        }
        if (!$serviceLocator->has('view')) {
            $documentRoot = $bitrixApplication->getContext()
                ->getServer()
                ->getDocumentRoot();
            $serviceLocator->set('view', new PhpView([$documentRoot]));
        }
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
