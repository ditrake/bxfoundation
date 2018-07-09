<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\events\EventableTrait;
use marvin255\bxfoundation\events\Result;
use marvin255\bxfoundation\response\exception\Forbidden;
use marvin255\bxfoundation\request\RequestInterface;

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
     * Фкнкция, которая осуществляет непосредственное создание url из
     * параметров.
     *
     * @param array $params
     *
     * @return string|null
     */
    abstract protected function createUrlByRule(array $params);

    /**
     * {@inheritdoc}
     *
     * Если отработали все фильтры до парсинга, сам парсинг,
     * но фильтры после парсинга не сработали, то считаем, что у пользователя
     * нет прав на доступ к данному ресурсу.
     *
     * @throws \marvin255\bxfoundation\response\exception\Forbidden
     */
    public function parse(RequestInterface $request, RuleResultInterface $ruleResult = null)
    {
        $return = null;

        $onBeforeRouteParsing = new Result('onBeforeRouteParsing', $this, [
            'request' => $request,
        ]);
        $this->riseEvent($onBeforeRouteParsing);

        if ($onBeforeRouteParsing->isSuccess() && ($parseResult = $this->parseByRule($request)) !== null) {
            $onAfterRouteParsing = new Result('onAfterRouteParsing', $this, [
                'request' => $request,
                'parseResult' => $parseResult,
            ]);
            $this->riseEvent($onAfterRouteParsing);

            if (!$onAfterRouteParsing->isSuccess()) {
                throw new Forbidden;
            }
            $return = $ruleResult ?: new RuleResult;
            $return->setParams($onAfterRouteParsing->getParam('parseResult'));
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function createUrl(array $params = [])
    {
        $return = null;

        $onBeforeUrlCreating = new Result('onBeforeUrlCreating', $this, [
            'params' => $params,
        ]);
        $this->riseEvent($onBeforeUrlCreating);

        if ($onBeforeUrlCreating->isSuccess()) {
            $url = $this->createUrlByRule($onBeforeUrlCreating->getParam('params'));

            $onAfterUrlCreating = new Result('onAfterUrlCreating', $this, [
                'params' => $onBeforeUrlCreating->getParam('params'),
                'url' => $url,
            ]);
            $this->riseEvent($onAfterUrlCreating);

            if ($onAfterUrlCreating->isSuccess()) {
                $return = $onAfterUrlCreating->getParam('url');
            }
        }

        return $return;
    }
}
