<?php

namespace marvin255\bxfoundation\view;

/**
 * Шаблонизатор, который подключает указанный php файл.
 */
class PhpView implements ViewInterface
{
    /**
     * Массив с папками, в которых следует искать файлы для подключения.
     *
     * @var array
     */
    protected $folders = [];

    /**
     * Магия. Наглый хак, чтобы стандартные шаблоны не падали при обращении к
     * несуществующим методам представления. Связано прежде всего с обращениями
     * из представления в компонент.
     *
     * @param string $name
     * @param array  $params
     */
    public function __call($name, $params)
    {
        return null;
    }

    /**
     * Конструктор.
     *
     * @param array $folders Массив с папками, в которых следует искать файлы для подключения
     *
     * @throws \marvin255\bxfoundation\view\Exception
     */
    public function __construct(array $folders)
    {
        if (empty($folders)) {
            throw new Exception('PhpView folders parameter must be set');
        }
        foreach ($folders as $folder) {
            $folder = $folder === '/' ? $folder : rtrim(trim($folder), '/');
            if (!is_dir($folder)) {
                throw new Exception("{$folder} is not a directory");
            }
            $this->folders[] = $folder;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\view\Exception
     */
    public function render($viewName, array $data = array())
    {
        $viewPath = $this->findPathToView($viewName);
        if (!$viewPath) {
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
     * Пробует найти файл шаблона в указанных в конструкторе папках.
     *
     * @param string $view
     *
     * @return string|null
     */
    protected function findPathToView($view)
    {
        $return = null;
        $viewPath = str_replace(
            ['./', '../'],
            '',
            trim($view, " \t\n\r\0\x0B./")
        );
        $viewPath = preg_replace('/\/{2,}/', '/', $viewPath) . '.php';
        foreach ($this->folders as $folder) {
            $testedPath = $folder . '/' . $viewPath;
            if (!file_exists($testedPath)) {
                continue;
            }
            $return = $testedPath;
            break;
        }

        return $return;
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
