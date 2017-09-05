<?php

namespace creative\foundation\routing\filter;

use creative\foundation\events\EventableInterface;
use creative\foundation\events\ResultInterface;

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
     * @param \creative\foundation\routing\route\RouteInterface $route
     *
     * @return \creative\foundation\routing\filter\FilterInterface
     */
    public function attachTo(EventableInterface $route);

    /**
     * Проверяет, чтобы в текущем запросе был верныйй метод http.
     *
     * @param \creative\foundation\events\ResultInterface $eventResult
     */
    public function filter(ResultInterface $eventResult);
}
