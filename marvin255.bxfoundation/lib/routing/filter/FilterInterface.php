<?php

namespace marvin255\bxfoundation\routing\filter;

use marvin255\bxfoundation\events\EventableInterface;
use marvin255\bxfoundation\events\ResultInterface;

/**
 * Объект, с дополнительной фильтрацией запроса.
 *
 * Нужен, чтобы одинаковые фильтры можно было применить к разным роутерам, и при
 * этом не переписывать одинаковый функциолнал внутри каждого роутера.
 */
interface FilterInterface
{
    /**
     * Добавялет фильтр к указанному роуту.
     *
     * @param \marvin255\bxfoundation\routing\route\RouteInterface $route
     *
     * @return \marvin255\bxfoundation\routing\filter\FilterInterface
     */
    public function attachTo(EventableInterface $route);

    /**
     * Проверяет, чтобы параметры события соответствовали бы фильтру.
     *
     * @param \marvin255\bxfoundation\events\ResultInterface $eventResult
     */
    public function filter(ResultInterface $eventResult);
}
