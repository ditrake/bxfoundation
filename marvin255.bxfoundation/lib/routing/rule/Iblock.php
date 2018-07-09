<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\services\iblock\Locator;
use marvin255\bxfoundation\Exception;
use CComponentEngine;

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
     * Сущность, для которой будет построен url.
     *
     * iblock - инфоблок
     * section - раздел инфоблока
     * element - элемент инфоблока
     *
     * @var string
     */
    protected $entity;
    /**
     * Массив с сущностями, которые можно отображать. Массив вида
     * "человекопонятное название" => "название поля в битриксе".
     *
     * @var array
     */
    protected $entities = [
        'iblock' => 'LIST_PAGE_URL',
        'section' => 'SECTION_PAGE_URL',
        'element' => 'DETAIL_PAGE_URL',
    ];

    /**
     * Конструктор.
     *
     * @param \marvin255\bxfoundation\services\iblock\Locator $locator Ссылка на объект для поиска инфоблоков
     * @param string                                          $iblock  Символьный код или идентификатор инфоблока
     * @param string                                          $entity  Сущност, для которой будет работать правило
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    public function __construct(Locator $locator, $iblock, $entity = 'element')
    {
        $this->locator = $locator;

        if (empty($iblock)) {
            throw new Exception('Empty iblock identity');
        }
        $this->iblock = $iblock;

        if (isset($this->entities[$entity])) {
            $this->entity = $this->entities[$entity];
        } else {
            throw new Exception(
                "There is no '{$entity}' entity for Iblock rule"
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function parseByRule(RequestInterface $request)
    {
        $return = null;

        $iblock = $this->getIblockArrayByIdentity($this->iblock);
        if ($iblock === null) {
            throw new Exception(
                "Can't find iblock by identity: {$this->iblock}"
            );
        }

        $link = $iblock[$this->entity];
        if ($link) {
            $return = $this->processBitrixSef($link, $request);
            if ($return !== null) {
                $return['IBLOCK_ID'] = (int) $iblock['ID'];
                $return['IBLOCK_TYPE_ID'] = $iblock['IBLOCK_TYPE_ID'];
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function createUrlByRule(array $params)
    {
    }

    /**
     * Запускает механизм битрикса для определения данных из ЧПУ.
     *
     * @param string                                           $link    Ссылка, для которой нужно попробовать определить ЧПУ
     * @param \marvin255\bxfoundation\request\RequestInterface $request Ссылка на текущий запрос
     *
     * @return array|null
     */
    protected function processBitrixSef($link, RequestInterface $request)
    {
        $engine = new CComponentEngine();
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
