<?php

namespace marvin255\bxfoundation\routing\filter;

/**
 * Фильтр, который пропускает только ajax запросы.
 */
class AjaxOnly extends Header
{
    public function __construct()
    {
        parent::__construct([
            'x-requested-with' => 'XMLHttpRequest',
        ]);
    }
}
