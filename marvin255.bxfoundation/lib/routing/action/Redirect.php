<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\Exception;

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
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    public function __construct($url)
    {
        if (trim($url) === '') {
            throw new Exception(
                "Url parameter can't be empty"
            );
        }
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    protected function runInternal(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        return $this->localRedirect($this->url);
    }

    /**
     * Переадресует пользователя с помощью битриксового метода.
     *
     * @param string $url
     *
     * @return mixed
     */
    protected function localRedirect($url)
    {
        return \LocalRedirect($url);
    }
}
