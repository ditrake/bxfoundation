<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\routing\Exception;

/**
 * Действие, которое вызывает компонент битрикса с указанными настройками.
 */
class Component extends Base
{
    /**
     * Название компонента в пространстве имен битрикса.
     *
     * @var string
     */
    protected $component = '';
    /**
     * Список параметров для инициализации компонента.
     *
     * Значения параметров, которые подходят под выражение `\$ruleResult.[a-z\_]+`
     * будут получены из переменной $ruleResult.
     *
     * @var array
     */
    protected $params = [];
    /**
     * Название шаблона компонента битрикса.
     *
     * @var string
     */
    protected $template = '';

    /**
     * Конструктор.
     *
     * @param string $component Название компонента в пространстве имен битрикса
     * @param array  $params    Список параметров для инициализации компонента
     * @param string $template  Название шаблона компонента битрикса
     *
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    public function __construct($component, $template = '', array $params = array())
    {
        if (empty($component)) {
            throw new Exception('Component name can\'t be empty');
        }
        $this->component = $component;
        $this->params = $params;
        $this->template = $template;
    }

    /**
     * @inheritdoc
     */
    protected function runInternal(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        $params = $this->setRuleResultReplaces($this->params, $ruleResult);

        return $this->includeComponent($this->component, $this->template, $params);
    }

    /**
     * Подключает компонент 1С-Битрикс по указаным параметрам.
     *
     * @param string $componentName Название компонента
     * @param string $template      Шаблон компонента
     * @param array  $arParams      Массив настроек компонента
     *
     * @return string
     */
    protected function includeComponent($componentName, $template = '', array $arParams = array())
    {
        ob_start();
        ob_implicit_flush(false);
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            $componentName,
            $template,
            $arParams,
            false
        );

        return ob_get_clean();
    }

    /**
     * Заменяет ссылки на значение из результат обработки url на сами параметры.
     *
     * @param array                                                 $params     Список параметров из конструктора
     * @param \marvin255\bxfoundation\routing\rule\RuleResultInterface $ruleResult Значение полученные из обработки url
     *
     * @return array
     */
    protected function setRuleResultReplaces(array $params, RuleResultInterface $ruleResult)
    {
        $return = [];
        foreach ($params as $key => $value) {
            if (preg_match('/^\$ruleResult\.([a-z\_]+)$/i', $value, $matches)) {
                $return[$key] = $ruleResult->getParam($matches[1]);
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}
