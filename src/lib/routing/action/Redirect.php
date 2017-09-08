<?php

namespace creative\foundation\routing\action;

use creative\foundation\request\RequestInterface;
use creative\foundation\response\ResponseInterface;
use creative\foundation\routing\rule\RuleResultInterface;
use creative\foundation\routing\Exception;

/**
 * Переадресация на указанную ссылку.
 */
class Redirect extends Base
{
    /**
     * Ссылка, на которую нужно будете переадресовать пользователя.
     *
     * @var string
     */
    protected $url = null;

    /**
     * Конструктор.
     *
     * @param string $url Ссылка, на которую нужно будете переадресовать пользователя
     *
     * @throws \creative\foundation\routing\Exception
     */
    public function __construct($url)
    {
        $url = trim($url);
        if ($url === '') {
            throw new Exception('Url parameter can\'t be empty');
        }
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    protected function runInternal(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        LocalRedirect($this->url);
    }
}
