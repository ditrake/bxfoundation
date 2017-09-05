<?php

namespace creative\foundation\routing\rule;

use creative\foundation\routing\Exception;
use creative\foundation\request\RequestInterface;

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
     * @param string $regexp
     * @param array  $filters
     *
     * @throws \creative\foundation\routing\Exception
     */
    public function __construct($regexp, array $filters = null)
    {
        $regexp = trim($regexp);
        if (!$regexp) {
            throw Exception('regexp param must be set');
        }
        $this->regexp = $regexp;
        if ($filters) {
            $this->attachFilters($filters);
        }
    }

    /**
     * @inheritdoc
     */
    protected function parseByRule(RequestInterface $request)
    {
        $return = null;
        list($regexp, $params) = $this->getPreparedRegexp();
        $path = trim($request->getPath(), " \t\n\r\0\x0B/");
        if (preg_match($regexp, $path, $matches)) {
            $return = [];
            foreach ($matches as $key => $value) {
                if ($key === 0 || !isset($params[$key - 1])) {
                    continue;
                }
                $return[$params[$key - 1]] = $value;
            }
        }

        return $return;
    }

    /**
     * Возвращает подготовленное к проверке регулярное выражение.
     *
     * @return string
     *
     * @throws \creative\foundation\routing\Exception
     */
    protected function getPreparedRegexp()
    {
        $return = [
            'regexp' => null,
            'params' => [],
        ];
        $arRegexp = explode('/', $this->regexp);
        foreach ($arRegexp as $key => $value) {
            if (preg_match('/^([a-z_0-9\-]*)<([a-z_]{1}[a-z_0-9]*):?\s*([^><:]*)\s*>([a-z_0-9\-]*)$/i', $value, $matches)) {
                $return['params'][] = $matches[2];
                $arRegexp[$key] = '';
                $arRegexp[$key] .= preg_quote($matches[1]);
                $arRegexp[$key] .= empty($matches[3]) ? '([a-z_0-9\-]+)' : "({$matches[3]})";
                $arRegexp[$key] .= preg_quote($matches[4]);
            } elseif (preg_match('/^[a-z_0-9\-]*$/i', $value)) {
                $arRegexp[$key] = preg_quote($value);
            } else {
                throw new Exception("Wrong regexp part: {$value}");
            }
        }
        $return['regexp'] = '/^' . implode('\/', $arRegexp) . '$/i';

        return array_values($return);
    }
}
