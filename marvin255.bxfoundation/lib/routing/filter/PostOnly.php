<?php

namespace marvin255\bxfoundation\routing\filter;

/**
 * Фильтр, который пропускает только POST запросы.
 */
class PostOnly extends Method
{
    public function __construct()
    {
        parent::__construct('POST');
    }
}
