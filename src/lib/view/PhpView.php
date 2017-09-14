<?php

namespace creative\foundation\view;

/**
 * Шаблонизатор, который подключает указанный php файл.
 */
class PhpView implements ViewInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \creative\foundation\view\Exception
     */
    public function render($viewName, array $data = array())
    {
        if (!preg_match('/^[a-zA-Z0-9_\/]+$/', $viewName)) {
            throw new Exception("Wrong view name: {$viewName}");
        }
        $viewPath = realpath(preg_replace('/\/{2,}/', '/', $viewName) . '.php');
        if (!$viewPath || !file_exists($viewPath)) {
            throw new Exception("Can't find file {$viewPath} for view {$viewName}");
        }

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                throw new Exception("Numeric key in data: {$key}");
            }
        }

        return $this->renderInternal($viewPath, $data);
    }

    /**
     * Включает указанный файл и передает в него параметры.
     *
     * @param string $___path___ Путь к указанному файлу
     * @param array  $___data___ Данные, которые нужно передать в шаблон
     *
     * @return string
     */
    protected function renderInternal($___path___, array $___data___)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($___data___);
        require $___path___;

        return ob_get_clean();
    }

    /**
     * @inheritdoc
     */
    public function getFileExtensions()
    {
        return ['php'];
    }
}
