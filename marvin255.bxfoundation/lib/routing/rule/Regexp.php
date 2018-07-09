<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\request\RequestInterface;

/**
 * Правило, которое использует регулярное выражение для проверки url.
 */
class Regexp extends Base
{
    /**
     * @var string
     */
    protected $regexp = null;
    /**
     * @var array
     */
    protected $preparedRegexp;

    /**
     * @param string $regexp
     *
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    public function __construct($regexp)
    {
        if (trim($regexp) === '') {
            throw new Exception('regexp param must be set');
        }
        $this->regexp = $regexp;
    }

    /**
     * @inheritdoc
     */
    protected function parseByRule(RequestInterface $request)
    {
        $return = null;
        $prepared = $this->getPreparedRegexp();
        $path = trim($request->getPath(), " \t\n\r\0\x0B/");

        if (preg_match($prepared['parse'], $path, $matches)) {
            $return = [];
            foreach ($matches as $key => $value) {
                if ($key === 0) {
                    continue;
                }
                $return[$prepared['params'][$key - 1]['code']] = $value;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function createUrlByRule(array $params)
    {
        $prepared = $this->getPreparedRegexp();

        $toSearch = [];
        $toReplace = [];
        foreach ($prepared['params'] as $arParam) {
            if (!isset($params[$arParam['code']])) {
                throw new Exception(
                    "Param `{$arParam['code']}` is required for url creation"
                );
            } elseif (!preg_match("/^{$arParam['regexp']}$/i", $params[$arParam['code']])) {
                throw new Exception(
                    "Param `{$arParam['code']}` has wrong type for regexp {$arParam['regexp']}"
                );
            } else {
                $toSearch[] = "##{$arParam['code']}##";
                $toReplace[] = $params[$arParam['code']];
            }
        }

        return str_replace($toSearch, $toReplace, $prepared['create']);
    }

    /**
     * Возвращает подготовленное к проверке регулярное выражение.
     *
     * @return string
     *
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    protected function getPreparedRegexp()
    {
        if ($this->preparedRegexp === null) {
            $this->preparedRegexp = [
                'parse' => '',
                'create' => '',
                'params' => [],
            ];

            $trimmedRegexp = trim($this->regexp, " \t\n\r\0\x0B/");
            list($indignificantBegin, $indignificantEnd) = explode($trimmedRegexp, $this->regexp);
            $arRegexp = explode('/', $trimmedRegexp);

            foreach ($arRegexp as $key => $value) {
                $this->preparedRegexp['parse'] .= $this->preparedRegexp['parse'] ? '\/' : '';
                $this->preparedRegexp['create'] .= $this->preparedRegexp['create'] ? '/' : '';
                if (preg_match('/^([a-z_0-9\-]*)<([a-z_]{1}[a-z_0-9]*):?\s*([^><:]*)\s*>([a-z_0-9\-]*)$/i', $value, $matches)) {
                    $paramRegexp = empty($matches[3]) ? '[a-z_0-9\-]+' : $matches[3];
                    $this->preparedRegexp['parse'] .= preg_quote($matches[1], '/');
                    $this->preparedRegexp['parse'] .= "({$paramRegexp})";
                    $this->preparedRegexp['parse'] .= preg_quote($matches[4], '/');
                    $this->preparedRegexp['create'] .= "{$matches[1]}##{$matches[2]}##{$matches[4]}";
                    $this->preparedRegexp['params'][] = [
                        'regexp' => $paramRegexp,
                        'code' => $matches[2],
                    ];
                } elseif (preg_match('/^[a-z_0-9\-]*$/i', $value)) {
                    $this->preparedRegexp['parse'] .= preg_quote($value, '/');
                    $this->preparedRegexp['create'] .= $value;
                } else {
                    throw new Exception("Wrong regexp part: {$value}");
                }
            }

            $this->preparedRegexp['create'] = $indignificantBegin
                . $this->preparedRegexp['create']
                . $indignificantEnd;
            $this->preparedRegexp['parse'] = '/^'
                . $this->preparedRegexp['parse']
                . '$/i';
        }

        return $this->preparedRegexp;
    }
}
