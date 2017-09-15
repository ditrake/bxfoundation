<?php

namespace creative\foundation\installer;

use Composer\Script\Event;
use Composer\Factory;
use Composer\Util\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use InvalidArgumentException;

/**
 * Класс-установщик, который необходим для того, чтобы скопировать файлы модуля
 * из папки composer внутрь структуры битрикса. Это требуется для того, чтобы
 * модуль был внутри структуры битрикса и обрабатывался как именно как модуль
 * битрикса. Весь код библиотеки не нужен, следует переносить только папку src.
 */
class Composer
{
    /**
     * Устанавливает модуль в структуру битрикса.
     *
     * **Внимание** перед установкой или обновлением удаляет страрую весрию.
     *
     * @param \Composer\Script\Event $event
     */
    public static function injectModule(Event $event)
    {
        $composer = $event->getComposer();

        $bitrixModulesFolder = self::getModulesFolder($event);
        if (!$bitrixModulesFolder) {
            throw new InvalidArgumentException('Can\'t find modules\' folder');
        }
        $bitrixModulesFolder .= '/marvin255.bxfoundation';

        $libraryFolder = self::getLibraryFolder($event);
        if (!$libraryFolder) {
            throw new InvalidArgumentException('Can\'t find src folder');
        }

        $fileSystem = new Filesystem();
        if (is_dir($bitrixModulesFolder)) {
            $fileSystem->removeDirectory($bitrixModulesFolder);
        }

        if (!self::copy($libraryFolder, $bitrixModulesFolder, $fileSystem)) {
            throw new InvalidArgumentException('Can\'t project src folder');
        }
    }

    /**
     * Возвращает полный путь до папки модулей.
     *
     * @param \Composer\Script\Event $event
     *
     * @return string
     */
    protected static function getModulesFolder(Event $event)
    {
        $projectRootPath = rtrim(dirname(Factory::getComposerFile()), '/');

        $extras = $event->getComposer()->getPackage()->getExtra();
        if (!empty($extras['install-bitrix-modules'])) {
            $bitrixModulesFolder = $extras['install-bitrix-modules'];
        } else {
            $bitrixModulesFolder = 'web/local/modules';
        }

        return (string) realpath($projectRootPath . '/' . trim($bitrixModulesFolder, '/'));
    }

    /**
     * Возвращает путь до папки, в которую установлена бибилиотека.
     *
     * @param \Composer\Script\Event $event
     *
     * @return string
     */
    protected static function getLibraryFolder(Event $event)
    {
        $srcFolder = false;
        $composer = $event->getComposer();
        $repositoryManager = $composer->getRepositoryManager();
        $installationManager = $composer->getInstallationManager();
        $localRepository = $repositoryManager->getLocalRepository();
        $packages = $localRepository->getPackages();
        foreach ($packages as $package) {
            if ($package->getName() === 'marvin255/bxfoundation') {
                $srcFolder = realpath(rtrim($installationManager->getInstallPath($package), '/') . '/marvin255.bxfoundation');
                break;
            }
        }

        return (string) $srcFolder;
    }

    /**
     * Копирует содержимое одной папки в другую.
     *
     * @param string $source
     * @param string $target
     *
     * @return bool
     */
    protected static function copy($source, $target, $fileSystem)
    {
        if (!is_dir($source)) {
            return copy($source, $target);
        }
        $it = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);
        $fileSystem->ensureDirectoryExists($target);

        $result = true;
        foreach ($ri as $file) {
            $targetPath = $target . DIRECTORY_SEPARATOR . $ri->getSubPathName();
            if ($file->isDir()) {
                $fileSystem->ensureDirectoryExists($targetPath);
            } else {
                $result = $result && copy($file->getPathname(), $targetPath);
            }
        }

        return $result;
    }
}
