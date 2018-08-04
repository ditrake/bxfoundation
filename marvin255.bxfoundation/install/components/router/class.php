<?php

namespace Marvin255Bxfoundation;

use CBitrixComponent;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use marvin255\bxfoundation\application\Application;
use marvin255\bxfoundation\routing\rule\Regexp;
use marvin255\bxfoundation\routing\rule\RuleInterface;
use ReflectionClass;
use InvalidArgumentException;

/**
 * Класс для компонента роутинга.
 */
class Router extends CBitrixComponent
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function onPrepareComponentParams($p)
    {
        //список модулей, которые нужно загрузить до запуска логики
        if (!empty($p['MODULES']) && is_array($p['MODULES'])) {
            $p['MODULES'] = array_map('trim', $p['MODULES']);
        } else {
            $p['MODULES'] = [];
        }
        $p['MODULES'][] = 'marvin255.bxfoundation';
        $p['MODULES'] = array_unique($p['MODULES']);

        //список роутов
        if (empty($p['ROUTES']) || !is_array($p['ROUTES'])) {
            throw new InvalidArgumentException(
                'ROUTES parameter must be an non empty array'
            );
        }

        //список роутов для исключений
        if (empty($p['EXCEPTIONS_ROUTES']) || !is_array($p['EXCEPTIONS_ROUTES'])) {
            $p['EXCEPTIONS_ROUTES'] = [];
        }

        //список глобальных фильтров
        if (empty($p['FILTERS']) || !is_array($p['FILTERS'])) {
            $p['FILTERS'] = [];
        }

        return parent::onPrepareComponentParams($p);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Bitrix\Main\LoaderException
     * @throws \InvalidArgumentException
     */
    public function executeComponent()
    {
        $this->loadModules();
        $this->loadFilters();
        $this->loadRoutes();
        $this->loadExceptionRoutes();

        $app = Application::getInstance();

        return $app->router->route($app->request, $app->response);
    }

    /**
     * Загружает указанные модули перед запуском компонента.
     *
     * @throws \Bitrix\Main\LoaderException
     */
    protected function loadModules()
    {
        foreach ($this->arParams['MODULES'] as $moduleName) {
            if (Loader::includeModule($moduleName)) {
                continue;
            }
            throw new LoaderException(
                "Can't load {$moduleName} module"
            );
        }
    }

    /**
     * Загружает глобальные фильтры для правил.
     */
    protected function loadFilters()
    {
        $this->filters = [];
        foreach ($this->arParams['FILTERS'] as $filter) {
            if (is_array($filter)) {
                $filter = $this->instantiateObject($filter);
            } else {
                $filter = $this->instantiateObject([$filter]);
            }
            $this->filters[] = $filter;
        }
    }

    /**
     * Загружает роуты из параметров в роутер.
     *
     * @throws \InvalidArgumentException
     */
    protected function loadRoutes()
    {
        $router = Application::getInstance()->router;

        foreach ($this->arParams['ROUTES'] as $key => $route) {
            $rule = null;
            $action = null;
            //пробуем инстанцировать правило
            if (!empty($route['rule']) && $route['rule'] instanceof RuleInterface) {
                $rule = $route['rule'];
            } elseif (!empty($route['rule']) && is_string($route['rule'])) {
                $rule = $this->instantiateObject([Regexp::class, $route['rule']]);
            } elseif (!empty($route['rule']) && is_array($route['rule'])) {
                $rule = $this->instantiateObject($route['rule']);
            }
            //пробуем инстанцировать действие
            if (!empty($route['action']) && $route['action'] instanceof RuleInterface) {
                $action = $route['action'];
            } elseif (!empty($route['action']) && is_string($route['action'])) {
                $action = $this->instantiateObject([$route['action']]);
            } elseif (!empty($route['action']) && is_array($route['action'])) {
                $action = $this->instantiateObject($route['action']);
            }
            //если не смогли что-то инстанцировать, то не можем продолжать
            if (!$rule || !$action) {
                throw new InvalidArgumentException(
                    "Can't parse {$key} route"
                );
            }
            //добавляем фильтры для правил
            $this->addFiletrs($rule, !empty($route['filters']) ? $route['filters'] : []);
            //регистрируем роут
            $router->registerRoute($rule, $action, $key);
        }
    }

    /**
     * Загружает роуты исключений из параметров в роутер.
     *
     * @throws \InvalidArgumentException
     */
    protected function loadExceptionRoutes()
    {
        $router = Application::getInstance()->router;

        foreach ($this->arParams['EXCEPTIONS_ROUTES'] as $exceptionCode => $action) {
            //пробуем инстанцировать действие
            if ($action instanceof RuleInterface) {
                $action = $action;
            } elseif (is_string($action)) {
                $action = $this->instantiateObject([$action]);
            } elseif (is_array($action)) {
                $action = $this->instantiateObject($action);
            } else {
                throw new InvalidArgumentException(
                    "Can't parse {$exceptionCode} exception action"
                );
            }
            //регистрируем роут
            $router->registerExceptionAction($exceptionCode, $action);
        }
    }

    /**
     * Добавляем фильтры к правилу.
     *
     * @param \marvin255\bxfoundation\routing\rule\RuleInterface $rule
     * @param array                                              $filters
     */
    protected function addFiletrs(RuleInterface $rule, array $filters)
    {
        foreach ($filters as $filter) {
            if (is_array($filter)) {
                $filter = $this->instantiateObject($filter);
            } else {
                $filter = $this->instantiateObject([$filter]);
            }
            $rule->filter($filter);
        }

        foreach ($this->filters as $filter) {
            $rule->filter($filter);
        }
    }

    /**
     * Инстанцирует объект из массива, первым элементом должна быть строка с классом.
     *
     * @param array $options
     *
     * @return mixed
     */
    protected function instantiateObject(array $options)
    {
        $class = array_shift($options);
        $reflect = new ReflectionClass($class);

        return $reflect->newInstanceArgs($options);
    }
}
