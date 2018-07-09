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
    protected $preparegRegexp;

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
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function createUrlByRule(array $params)
    {
        list($regexp, $regexpParams, $url) = $this->getPreparedRegexp();

        $urlParts = [];
        foreach ($url as $urlPart) {
            if (is_array($urlPart)) {
                list($before, $param, $after) = $urlPart;
                if (!isset($params[$param])) {
                    throw new Exception(
                        "Param {$param} is required for url creating"
                    );
                }
                $urlParts[] = $before . $params[$param] . $after;
            } else {
                $urlParts[] = $urlPart;
            }
        }

        return '/' . implode('/', $urlParts);
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
        if ($this->preparegRegexp === null) {
            $this->preparegRegexp = [];
            $arRegexp = explode('/', trim($this->regexp, " \t\n\r\0\x0B/"));
            foreach ($arRegexp as $key => $value) {
                if (preg_match('/^([a-z_0-9\-]*)<([a-z_]{1}[a-z_0-9]*):?\s*([^><:]*)\s*>([a-z_0-9\-]*)$/i', $value, $matches)) {
                    $this->preparegRegexp[1][] = $matches[2];
                    $this->preparegRegexp[2][] = [$matches[1], $matches[2], $matches[4]];
                    $arRegexp[$key] = '';
                    $arRegexp[$key] .= preg_quote($matches[1]);
                    $arRegexp[$key] .= empty($matches[3]) ? '([a-z_0-9\-]+)' : "({$matches[3]})";
                    $arRegexp[$key] .= preg_quote($matches[4]);
                } elseif (preg_match('/^[a-z_0-9\-]*$/i', $value)) {
                    $arRegexp[$key] = preg_quote($value);
                    $this->preparegRegexp[2][] = $value;
                } else {
                    throw new Exception("Wrong regexp part: {$value}");
                }
            }
            $this->preparegRegexp[0] = '/^' . implode('\/', $arRegexp) . '$/i';
        }

        return $this->preparegRegexp;
    }
}
