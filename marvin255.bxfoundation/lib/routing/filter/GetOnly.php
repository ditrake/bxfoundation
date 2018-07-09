<?php

namespace marvin255\bxfoundation\routing\filter;

/**
 * Фильтр, который пропускает только GET запросы.
 */
class GetOnly extends Method
{
    public function __construct()
    {
        parent::__construct('GET');
    }
}
