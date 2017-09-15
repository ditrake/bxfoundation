<?php

namespace marvin255\bxfoundation\view;

/**
 * Интерфейс для объектов-шаблонизаторов.
 */
interface ViewInterface
{
    /**
     * Формирует html на оновнии имени шаблона и данных.
     *
     * @param string       $viewName Имя шаблона для отображения
     * @param string array $data
     *
     * @return string
     */
    public function render($viewName, array $data = array());

    /**
     * Возвращает список расширений файлов, к которым применим шаблонизатор.
     *
     * @return array
     */
    public function getFileExtensions();
}
