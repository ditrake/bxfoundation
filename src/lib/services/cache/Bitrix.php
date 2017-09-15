<?php

namespace marvin255\bxfoundation\services\cache;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Data\TaggedCache;
use marvin255\bxfoundation\services\Exception;

/**
 * Объект для кэширования, который упрощает обращение к битриксовому кэшу.
 */
class Bitrix implements CacheInterface
{
    /**
     * Ссылка на битриксовый объект для кэширования.
     *
     * \Bitrix\Main\Data\Cache
     */
    protected $cache = null;
    /**
     * Ссылка на битриксовый объект для тэггирования кэша.
     *
     * \Bitrix\Main\Data\TaggedCache|null
     */
    protected $taggedCache = null;
    /**
     * Время по умолчанию, на которое кэшируются объекты.
     *
     * @param int
     */
    protected $defaultTime = 0;

    /**
     * Конструктор.
     *
     * @param \Bitrix\Main\Data\Cache       $cache       Ссылка на битриксовый объект для кэширования
     * @param \Bitrix\Main\Data\TaggedCache $taggedCache Ссылка на битриксовый объект для тэггирования кэша
     * @param int                           $defaultTime Время по умолчанию, на которое кэшируются объекты
     */
    public function __construct(Cache $cache, TaggedCache $taggedCache = null, $defaultTime = 3600)
    {
        $this->cache = $cache;
        $this->taggedCache = $taggedCache;
        $this->defaultTime = (int) $defaultTime;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\services\Exception
     */
    public function set($key, $data, $duration = null, array $tags = null)
    {
        if (trim($key) === '') {
            throw new Exception('key parameter for cache setting can\'t be empty');
        }
        $time = $duration !== null ? (int) $duration : $this->defaultTime;
        if ($time) {
            $this->cache->initCache($time, $key, $this->getFolder($key));
            $this->cache->startDataCache();
            if ($this->taggedCache && $tags) {
                $this->taggedCache->startTagCache($this->getFolder($key));
                foreach ($tags as $tag) {
                    $this->taggedCache->registerTag($tag);
                }
                $this->taggedCache->endTagCache();
            }
            $this->cache->endDataCache($data);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function get($key)
    {
        if ($this->cache->initCache(100, $key, $this->getFolder($key))) {
            return $this->cache->getVars();
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\services\Exception
     */
    public function clear($key)
    {
        if (trim($key) === '') {
            throw new Exception('key parameter for cache clearing can\'t be empty');
        }
        $this->cache->initCache(100, $key, $this->getFolder($key));
        $this->cache->cleanDir($this->getFolder($key));

        return $this;
    }

    /**
     * Возвращает каталог для сохранения данных кэша.
     *
     * @param string $key Ключ кэша
     *
     * @return string
     */
    protected function getFolder($key)
    {
        return '/' . md5($key);
    }
}
