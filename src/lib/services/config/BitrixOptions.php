<?php

namespace creative\foundation\services\config;

use Bitrix\Main\Config\Option;
use creative\foundation\services\Exception;

/**
 * Класс, который инкапсулирует в себе обращение к \Bitrix\Main\Config\Option.
 *
 * Вызовы методов Option должны быть статичными, поэтому они не поддаются
 * перехвату, что вызывает необходимость писать код с высоким зацеплением.
 */
class BitrixOptions
{
    /**
     * Возвращает значение указанной опции.
     *
     * @param string      $moduleId Идентификатор модуля, внутри которого нужно искать опцию
     * @param string      $name     Название опции
     * @param string      $default  Значение по умолчанию, которое будет возвращаено, если опция не будет найлена
     * @param bool|string $siteId   Идентификатор сайта, для которого нужно искать опцию
     *
     * @return string
     *
     * @throws \creative\foundation\services\Exception
     */
    public function get($moduleId, $name, $default = null, $siteId = false)
    {
        try {
            $return = Option::get($moduleId, $name, $default, $siteId);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $return;
    }

    /**
     * Задает значение указанной опции.
     *
     * @param string $moduleId Идентификатор модуля, внутри которого нужно искать опцию
     * @param string $name     Название опции
     * @param string $value    Новое значение опции
     * @param string $siteId   Идентификатор сайта, для которого нужно искать опцию
     *
     * @return \Bitrix\Main\Config\Option\BitrixOptions
     *
     * @throws \creative\foundation\services\Exception
     */
    public function set($moduleId, $name, $value, $siteId = '')
    {
        try {
            Option::set($moduleId, $name, $value, $siteId);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }
}
