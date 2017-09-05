<?php

namespace creative\foundation\routing\action;

use creative\foundation\request\RequestInterface;
use creative\foundation\routing\rule\RuleResultInterface;
use creative\foundation\routing\Exception;

/**
 * Действие, которое вызывает компонент битрикса с указанными настройками.
 */
class Component implements ActionInterface
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
     * @throws \creative\foundation\routing\Exception
     */
    public function __construct($component, array $params = array(), $template = '')
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
    public function run(RuleResultInterface $ruleResult, RequestInterface $request)
    {
        $params = $this->setRuleResultReplaces($this->params, $ruleResult);

        ob_start();
        ob_implicit_flush(false);
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            $this->component,
            $this->template,
            $params,
            false
        );

        return ob_get_clean();
    }

    /**
     * Заменяет ссылки на значение из результат обработки url на сами параметры.
     *
     * @param array                                                 $params     Список параметров из конструктора
     * @param \creative\foundation\routing\rule\RuleResultInterface $ruleResult Значение полученные из обработки url
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
