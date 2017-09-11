<?php

namespace creative\foundation\routing\rule;

use creative\foundation\routing\Exception;
use creative\foundation\request\RequestInterface;
use Bitrix\Main\Loader;

/**
 * Правило, которое использует настройки инфоблока для проверки url.
 */
class Iblock extends Base
{
    /**
     * Символьный код или идентификатор инфоблока.
     *
     * @var string
     */
    protected $iblock = null;
    /**
     * Массив с сущностями, которые нужно отображать.
     * iblock - инфоблок
     * section - раздел инфоблока
     * element - элемент инфоблока.
     *
     * @var array
     */
    protected $entities = ['iblock', 'section', 'element'];

    /**
     * Конструктор.
     *
     * @param string       $iblock   Символьный код или идентификатор инфоблока
     * @param array|string $entities Массив с сущностями, которые нужно отображать
     * @param array        $filters
     *
     * @throws \creative\foundation\routing\Exception
     */
    public function __construct($iblock, $entities = null, array $filters = null)
    {
        if (empty($iblock)) {
            throw new Exception('Empty iblock identity');
        }
        $this->iblock = $iblock;
        if ($entities !== null) {
            $entities = is_array($entities) ? array_unique($entities) : [$entities];
            if ($diff = array_diff($entities, ['iblock', 'section', 'element'])) {
                throw new Exception('Undefined entities: ' . implode(', ', $diff));
            }
            $this->entities = $entities;
        }
        if ($filters) {
            $this->attachFilters($filters);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \creative\foundation\routing\Exception
     */
    protected function parseByRule(RequestInterface $request)
    {
        $return = null;

        $iblock = $this->getIblockArrayByIdentity($this->iblock);
        if ($iblock === null) {
            throw new Exception('Wrong iblock identity: ' . $this->iblock);
        }

        $path = trim($request->getPath(), " \t\n\r\0\x0B/");
        $link = null;
        if (
            in_array('element', $this->entities)
            && ($regexp = $this->convertBitrixLinkToRegexp($iblock['DETAIL_PAGE_URL']))
            && preg_match($regexp, $path)
        ) {
            $link = $iblock['DETAIL_PAGE_URL'];
        } elseif (
            in_array('section', $this->entities)
            && ($regexp = $this->convertBitrixLinkToRegexp($iblock['SECTION_PAGE_URL']))
            && preg_match($regexp, $path)
        ) {
            $link = $iblock['SECTION_PAGE_URL'];
        } elseif (
            in_array('iblock', $this->entities)
            && ($regexp = $this->convertBitrixLinkToRegexp($iblock['LIST_PAGE_URL']))
            && preg_match($regexp, $path)
        ) {
            $link = $iblock['LIST_PAGE_URL'];
        }

        if ($link !== null) {
            $return = $this->processBitrixSef($link, $request);
            if ($return !== null) {
                $return['IBLOCK_ID'] = (int) $iblock['ID'];
                $return['IBLOCK_TYPE_ID'] = $iblock['IBLOCK_TYPE_ID'];
            }
        }

        return $return;
    }

    /**
     * Запускает механизм битрикса для определения данных из ЧПУ.
     *
     * @param string                                        $link    Ссылка, для которой нужно попробовать определить ЧПУ
     * @param \creative\foundation\request\RequestInterface $request Ссылка на текущий запрос
     *
     * @return array|null
     */
    protected function processBitrixSef($link, RequestInterface $request)
    {
        $engine = new \CComponentEngine();
        $engine->addGreedyPart('#SECTION_CODE_PATH#');
        $engine->setResolveCallback(['CIBlockFindTools', 'resolveComponentEngine']);
        $arVariables = [];
        $componentPage = $engine->guessComponentPath(
            '/',
            ['resolved' => trim($link, '/')],
            $arVariables,
            rtrim($request->getPath(), '/')
        );

        return $componentPage === 'resolved' ? $arVariables : null;
    }

    /**
     * Конвертирует ссылку с заменами битрикса в регулярное выражение для
     * предварительной проверки.
     *
     * @param string $link
     *
     * @return string
     */
    protected function convertBitrixLinkToRegexp($link)
    {
        $return = null;
        $path = trim($link, " \t\n\r\0\x0B/");
        $arPath = explode('/', $path);
        foreach ($arPath as $chainItem) {
            if (preg_match('/^#[^#]+#$/', $chainItem)) {
                $return[] = '[^\/]+';
            } else {
                $return[] = preg_quote($chainItem);
            }
        }

        return $return ? '/^' . implode('\/', $return) . '$/' : null;
    }

    /**
     * Возвращает массив с описанием инфоблока по его id или коду.
     *
     * @param string $iblock
     *
     * @return array
     */
    protected function getIblockArrayByIdentity($iblock)
    {
        $return = null;

        foreach (static::getIblocks() as $arIblock) {
            if ((int) $arIblock['ID'] !== (int) $iblock && $arIblock['CODE'] !== $iblock) {
                continue;
            }
            $return = $arIblock;
            break;
        }

        return $return;
    }

    /**
     * Список кэшированных инфоблоков для данного правила.
     *
     * Получаем одним запросом все инфоблоки и сохраняем в статическую переменную,
     * которой сможет пользоваться каждый инстанс данного правила.
     *
     * @var array
     */
    protected static $loadedIblocks = null;

    /**
     * Возвращает список всех доступных на сайте инфоблоков.
     *
     * Получает инфоблоки при первом же запросе и кэширует список
     * в статическую переменную, при каждом последующем обращении
     * возвращает данные из этой переменной.
     *
     * @return array
     */
    protected static function getIblocks()
    {
        if (static::$loadedIblocks === null) {
            Loader::includeModule('iblock');
            $res = \CIBlock::getList([], [
                'CHECK_PERMISSIONS' => 'N',
                'SITE_ID' => SITE_ID,
            ]);
            while ($ob = $res->fetch()) {
                static::$loadedIblocks[] = $ob;
            }
        }

        return static::$loadedIblocks;
    }
}
