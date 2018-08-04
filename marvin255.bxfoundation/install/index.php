<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class marvin255_bxfoundation extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'marvin255.bxfoundation';
        $this->MODULE_NAME = Loc::getMessage('BX_FOUNDATION_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BX_FOUNDATION_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('BX_FOUNDATION_MODULE_PARTNER_NAME');
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installFiles();
        $this->installDB();
    }

    public function doUninstall()
    {
        $this->unInstallFiles();
        $this->uninstallDB();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    /**
     * Вносит в базу данных изменения, требуемые модулем
     *
     * @return bool
     */
    public function installDB()
    {
    }

    /**
     * Удаляет из базы данных изменения, требуемые модулем
     *
     * @return bool
     */
    public function uninstallDB()
    {
    }

    /**
     * Копирует файлы модуля в битрикс
     *
     * @return bool
     */
    public function installFiles()
    {
        CopyDirFiles($this->getInstallatorPath() . '/admin', $this->getComponentPath('admin'), true, true);
        CopyDirFiles(
            $this->getInstallatorPath() . '/components',
            $this->getComponentPath('components') . '/' . $this->MODULE_ID,
            true,
            true
        );

        return true;
    }

    /**
     * Удаляет файлы модуля из битрикса.
     *
     * @return bool
     */
    public function unInstallFiles()
    {
        DeleteDirFiles($this->getInstallatorPath() . '/admin', $this->getComponentPath('admin'));
        if (is_dir($this->getInstallatorPath() . '/components')) {
            Directory::deleteDirectory($this->getComponentPath('components') . '/' . $this->MODULE_ID);
        }

        return true;
    }

    /**
     * Возвращает путь к папке с модулем
     *
     * @return string
     */
    public function getInstallatorPath()
    {
        return str_replace('\\', '/', __DIR__);
    }

    /**
     * Возвращает путь к папке, в которую будут установлены компоненты модуля.
     *
     * @param string $type тип компонентов для установки (components, js, admin и т.д.)
     *
     * @return string
     */
    public function getComponentPath($type = 'components')
    {
        if ($type === 'admin') {
            $base = Application::getDocumentRoot() . '/bitrix';
        } else {
            $base = dirname(dirname(dirname($this->getInstallatorPath())));
        }

        return $base . '/' . str_replace(['/', '.'], '', $type);
    }
}
