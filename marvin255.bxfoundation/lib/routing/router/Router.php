<?php

namespace marvin255\bxfoundation\routing\router;

use marvin255\bxfoundation\routing\rule\RuleInterface;
use marvin255\bxfoundation\routing\action\ActionInterface;
use marvin255\bxfoundation\routing\rule\RuleResult;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\HttpException;
use marvin255\bxfoundation\routing\NotFoundException;

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
    public function registerRoute(RuleInterface $rule, ActionInterface $action)
    {
        $this->routes[] = [$rule, $action];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function registerRouteException($code, ActionInterface $action)
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
                throw new NotFoundException();
            }
        } catch (HttpException $e) {
            $return = $this->routeException($e, $request, $response);
        }

        return $return;
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
     * Обработка исключения, связанного с ответами http.
     *
     * @param \marvin255\bxfoundation\routing\HttpException      $exception Ссылка на объект пойманного исключения
     * @param \marvin255\bxfoundation\request\RequestInterface   $request   Ссылка на текущий объект запроса
     * @param \marvin255\bxfoundation\response\ResponseInterface $response  Ссылка на текущий объект ответа
     *
     * @return string
     *
     * @throws \marvin255\bxfoundation\routing\HttpException
     */
    protected function routeException(HttpException $exception, RequestInterface $request, ResponseInterface $response)
    {
        $return = null;

        $response->setStatus($exception->getHttpStatus());
        if (isset($this->routesExceptions[$exception->getHttpCode()])) {
            $ruleResult = new RuleResult(['exception' => $exception]);
            $action = $this->routesExceptions[$exception->getHttpCode()];
            $return = $action->run($ruleResult, $request, $response);
        } else {
            throw $exception;
        }

        return $return;
    }
}
