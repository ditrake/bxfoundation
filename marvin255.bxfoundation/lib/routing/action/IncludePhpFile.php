<?php

namespace marvin255\bxfoundation\routing\action;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\response\ResponseInterface;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;
use marvin255\bxfoundation\Exception;

/**
 * Действие, которое отображает указанный php файл.
 */
class IncludePhpFile extends Base
{
    /**
     * Путь к файлу, который следует отобразить.
     *
     * @var string
     */
    protected $filePath;
    /**
     * Список параметров, которые будут переданы в отображаемый файл.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Конструктор.
     *
     * @param string $filePath Путь к файлу, который следует отобразить
     * @param array  $params   Список параметров, которые будут переданы в отображаемый файл
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    public function __construct($filePath, array $params = [])
    {
        if (!file_exists($filePath)) {
            throw new Exception("File is not exists: {$filePath}");
        }
        foreach ($params as $key => $value) {
            if (!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]*$/', $key)) {
                throw new Exception("Wrong parameter name: {$key}");
            }
        }
        $this->filePath = $filePath;
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    protected function runInternal(RuleResultInterface $ruleResult, RequestInterface $request, ResponseInterface $response)
    {
        return $this->includeFile($this->filePath, $this->params);
    }

    /**
     * Подключает файл для отображения.
     *
     * @param string $___file___   Путь к файлу
     * @param string $___params___ Параметры, которые будут переданы в файл
     *
     * @return string
     */
    protected function includeFile($___file___, array $___params___ = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($___params___);
        include $___file___;

        return ob_get_clean();
    }
}
