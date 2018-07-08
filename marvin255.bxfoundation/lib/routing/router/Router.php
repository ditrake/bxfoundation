<?php

namespace marvin255\bxfoundation\routing\router;

use marvin255\bxfoundation\routing\rule\RuleInterface;
use marvin255\bxfoundation\routing\action\ActionInterface;
use marvin255\bxfoundation\routing\rule\RuleResult;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\response\exception\Response as ResponseException;
use marvin255\bxfoundation\response\exception\NotFound;
use marvin255\bxfoundation\response\exception\ServerError;
use InvalidArgumentException;

/**
 * Объект, который ищет подходящее правило для url
 * и отображает связанное с ним действие.
 */
class Router implements RouterInterface
{
    /**
     * Массив с правилами и действиями для данных правил.
     *
     * @var array
     */
    protected $routes = [];
    /**
     * Массив с правилами и действиями для исключительных ситуаций.
     *
     * @var array
     */
    protected $routesExceptions = [];

    /**
     * @inheritdoc
     */
    public function registerRoute(RuleInterface $rule, ActionInterface $action, $routeName = null)
    {
        $this->routes[] = [$rule, $action, $routeName];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function registerExceptionAction($code, ActionInterface $action)
    {
        $this->routesExceptions[$code] = $action;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function route(RequestInterface $request, ResponseInterface $response)
    {
        $return = null;

        try {
            $return = $this->routeInternal($request, $response);
            if ($return === null) {
                throw new NotFound;
            }
        } catch (ResponseException $e) {
            $return = $this->routeException($e, $request, $response);
        } catch (\Exception $e) {
            $internalException = new ServerError($e->getMessage(), $e->getCode(), $e);
            $return = $this->routeException($internalException, $request, $response);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function url($routeName, array $params = [])
    {
        $route = $this->findRouteByName($routeName);
        if ($route === null) {
            throw new InvalidArgumentException(
                "Can't find route with name {$routeName}"
            );
        }

        list($rule, $action, $name) = $route;

        return $rule->createUrl($params);
    }

    /**
     * Обрабатывает ссылку.
     *
     * @param \marvin255\bxfoundation\request\RequestInterface   $request  Ссылка на текущий объект запроса
     * @param \marvin255\bxfoundation\response\ResponseInterface $response Ссылка на текущий объект ответа
     *
     * @return string
     */
    protected function routeInternal(RequestInterface $request, ResponseInterface $response)
    {
        $return = null;
        foreach ($this->routes as $route) {
            list($rule, $action) = $route;
            if ($ruleResult = $rule->parse($request)) {
                $return = $action->run($ruleResult, $request, $response);
                break;
            }
        }

        return $return;
    }

    /**
     * Ищет роут по указанному имени.
     *
     * @param string $routeName
     *
     * @return array|null
     */
    protected function findRouteByName($routeName)
    {
        $return = null;
        foreach ($this->routes as $route) {
            list($rule, $action, $currentRouteName) = $route;
            if ($currentRouteName === $routeName) {
                $return = $route;
                break;
            }
        }

        return $return;
    }

    /**
     * Обработка исключения, связанного с ответами http.
     *
     * @param \marvin255\bxfoundation\response\exception\Response $exception Ссылка на объект пойманного исключения
     * @param \marvin255\bxfoundation\request\RequestInterface    $request   Ссылка на текущий объект запроса
     * @param \marvin255\bxfoundation\response\ResponseInterface  $response  Ссылка на текущий объект ответа
     *
     * @return string
     *
     * @throws \marvin255\bxfoundation\response\exception\Response
     */
    protected function routeException(ResponseException $exception, RequestInterface $request, ResponseInterface $response)
    {
        $return = null;

        $response->setStatus($exception->getHttpStatus());
        if (isset($this->routesExceptions[$exception->getHttpStatus()])) {
            $ruleResult = new RuleResult(['exception' => $exception]);
            $action = $this->routesExceptions[$exception->getHttpStatus()];
            $return = $action->run($ruleResult, $request, $response);
        } else {
            throw $exception;
        }

        return $return;
    }
}
