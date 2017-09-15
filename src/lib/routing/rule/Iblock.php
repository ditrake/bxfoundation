<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\routing\Exception;
use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\services\iblock\Locator;

/**
 * Правило, которое использует настройки инфоблока для проверки url.
 */
class Iblock extends Base
{
    /**
     * Ссылка на объект для поиска инфоблоков.
     *
     * @var \marvin255\bxfoundation\services\iblock\Locator
     */
    protected $locator = null;
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
     * @param \marvin255\bxfoundation\services\iblock\Locator $locator  Ссылка на объект для поиска инфоблоков
     * @param string                                       $iblock   Символьный код или идентификатор инфоблока
     * @param array|string                                 $entities Массив с сущностями, которые нужно отображать
     * @param array                                        $filters
     *
     * @throws \marvin255\bxfoundation\routing\Exception
     */
    public function __construct(Locator $locator, $iblock, $entities = null, array $filters = null)
    {
        $this->locator = $locator;
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
     * @throws \marvin255\bxfoundation\routing\Exception
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
     * @param \marvin255\bxfoundation\request\RequestInterface $request Ссылка на текущий запрос
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
        $field = is_numeric($iblock) ? 'ID' : 'CODE';

        return $this->locator->findBy($field, $iblock);
    }
}
