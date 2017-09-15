<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\events\EventableTrait;
use marvin255\bxfoundation\events\Result;
use marvin255\bxfoundation\routing\Exception;
use marvin255\bxfoundation\routing\ForbiddenException;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\routing\filter\FilterInterface;

/**
 * Абстрактный класс для правила url.
 */
abstract class Base implements RuleInterface, EventableInterface
{
    use EventableTrait;

    /**
     * Фкнкция, которая осуществляет непосредственный разбор запроса.
     * В случае успеха возваращает параметры, которые правило полуило из запроса.
     * В случае неудачи false.
     *
     * @param \marvin255\bxfoundation\request\RequestInterface $request
     *
     * @return false|array
     */
    abstract protected function parseByRule(RequestInterface $request);

    /**
     * {@inheritdoc}
     *
     * Если отработали все фильтры до парсинга, сам парсинг,
     * но фильтры после парсинга не сработали, то считаем, что у пользователя
     * нет прав на доступ к данному ресурсу.
     *
     * @throws \marvin255\bxfoundation\routing\ForbiddenException
     */
    public function parse(RequestInterface $request, RuleResultInterface $ruleResult = null)
    {
        $return = null;

        $onBeforeRouteParsing = new Result(
            'onBeforeRouteParsing',
            $this,
            ['request' => $request]
        );
        $this->riseEvent($onBeforeRouteParsing);

        if ($onBeforeRouteParsing->isSuccess() && ($parseResult = $this->parseByRule($request)) !== null) {
            $onAfterRouteParsing = new Result(
                'onAfterRouteParsing',
                $this,
                [
                    'request' => $request,
                    'parseResult' => $parseResult,
                ]
            );
            $this->riseEvent($onAfterRouteParsing);
            if (!$onAfterRouteParsing->isSuccess()) {
                throw new ForbiddenException();
            }
            $return = $ruleResult ?: new RuleResult;
            $return->setParams($onAfterRouteParsing->getParam('parseResult'));
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    public function attachFilters(array $filters)
    {
        foreach ($filters as $key => $filter) {
            if (!($filter instanceof FilterInterface)) {
                throw new Exception("Filter object with key {$key} must be a FilterInterface instance");
            }
            $filter->attachTo($this);
        }

        return $this;
    }
}
