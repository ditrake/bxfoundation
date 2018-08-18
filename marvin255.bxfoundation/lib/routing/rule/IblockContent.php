<?php

namespace marvin255\bxfoundation\routing\rule;

use marvin255\bxfoundation\request\RequestInterface;
use marvin255\bxfoundation\services\iblock\Locator;
use marvin255\bxfoundation\Exception;
use CIBlockSection;
use CIBlockElement;
use CFile;

/**
 * Правило, которое пробует найти элемент или раздел инфоблока по иерархии разделов
 * по символьным кодам.
 */
class IblockContent extends Base
{
    /**
     * Ссылка на объект для поиска инфоблоков.
     *
     * @var \marvin255\bxfoundation\services\iblock\Locator
     */
    protected $locator;
    /**
     * Символьный код или идентификатор инфоблока.
     *
     * @var string
     */
    protected $iblock;
    /**
     * Название флага, который указывает, что в элементе комплексный компонент.
     *
     * @var string
     */
    protected $isComplexProperty;
    /**
     * Дополнительные свойства, которые нужно запросить из элемента или раздела.
     *
     * @var array
     */
    protected $additionalProperties;

    /**
     * Конструктор.
     *
     * @param \marvin255\bxfoundation\services\iblock\Locator $locator           Ссылка на объект для поиска инфоблоков
     * @param string                                          $iblock            Символьный код или идентификатор инфоблока
     * @param string                                          $isComplexProperty Название флага, который указывает, что в элементе комплексный компонент
     * @param array                                           $isComplexProperty Дополнительные свойства, которые нужно запросить из элемента или раздела
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    public function __construct(Locator $locator, $iblock, $isComplexProperty = 'is_complex', array $additionalProperties = [])
    {
        $this->locator = $locator;

        if (empty($iblock)) {
            throw new Exception('Empty iblock identity');
        }
        $this->iblock = $iblock;

        $this->isComplexProperty = trim($isComplexProperty);
        $this->additionalProperties = array_map('trim', $additionalProperties);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function parseByRule(RequestInterface $request)
    {
        //разбиваем url на массив, элементы которого считаем кодами
        $codes = $this->parseCodesFromRequest($request);

        //пробуем найти подходящий по коду и по родителю раздел
        $path = [];
        $lastOneId = false;
        if (count($codes) > 1 || $codes[0] !== '') {
            $sections = $this->loadSectionsByCodes($codes);
            foreach ($codes as $code) {
                $isCodeFinded = false;
                foreach ($sections as $section) {
                    if ($section['code'] === $code && $section['parent_id'] === $lastOneId) {
                        $isCodeFinded = true;
                        $lastOneId = $section['id'];
                        $path[] = $section;
                        break;
                    }
                }
                if (!$isCodeFinded) {
                    break;
                }
            }
        }

        //если не нашли полный путь по разделам,
        //то пробуем найти последнее звено как элемент
        //или непоследнее звено как элемент с комплексным флагом
        if (count($codes) !== count($path)) {
            $element = $this->loadElementByCodeAndSection(
                $codes[count($path)],
                $lastOneId
            );
            if (
                !$element
                || (count($codes) - count($path)) > 1 && !$element['is_complex']
            ) {
                $path = null;
            } else {
                $path[] = $element;
            }
        }

        //возвращаем всю информацию, которую смогли найти
        $return = null;
        if ($path) {
            $arIblock = $this->getIblockArrayByIdentity();
            $return = [
                'PATH' => $path,
                'IBLOCK_ID' => (int) $arIblock['ID'],
                'IBLOCK_TYPE_ID' => $arIblock['IBLOCK_TYPE_ID'],
            ];
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
     * Разбивает url на части по символу "/".
     *
     * @param \marvin255\bxfoundation\request\RequestInterface $request
     *
     * @return array
     */
    protected function parseCodesFromRequest(RequestInterface $request)
    {
        $path = $request->getPath();
        $codes = array_map('trim', explode('/', trim($path, '/')));

        return $codes;
    }

    /**
     * Загружает описания разделов по массиву кодов.
     *
     * @param array $codes
     *
     * @return array
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function loadSectionsByCodes(array $codes)
    {
        $arIblock = $this->getIblockArrayByIdentity();

        $select = [
            'ID',
            'NAME',
            'CODE',
            'PICTURE',
            'DESCRIPTION',
            'DETAIL_PICTURE',
            'IBLOCK_SECTION_ID',
            'DEPTH_LEVEL',
            'SECTION_PAGE_URL',
        ];
        foreach ($this->additionalProperties as $property) {
            $select[] = 'UF_' . strtoupper($property);
        }

        $res = CIBlockSection::getList(
            ['depth_level' => 'asc', 'id' => 'asc'],
            [
                'IBLOCK_ID' => $arIblock['ID'],
                'CODE' => $codes,
                'ACTIVE' => 'Y',
            ],
            false,
            $select
        );

        $sections = [];
        while ($ob = $res->getNext()) {
            $section = [
                'id' => (int) $ob['ID'],
                'type' => 'section',
                'name' => $ob['NAME'],
                'code' => $ob['CODE'],
                'url' => $ob['SECTION_PAGE_URL'],
                'preview_text' => '',
                'preview_picture' => !empty($ob['PICTURE'])
                    ? CFile::getFileArray($ob['PICTURE'])
                    : null,
                'detail_text' => $ob['DESCRIPTION'],
                'detail_picture' => !empty($ob['DETAIL_PICTURE'])
                    ? CFile::getFileArray($ob['DETAIL_PICTURE'])
                    : null,
                'parent_id' => (int) $ob['IBLOCK_SECTION_ID'] ?: false,
                'depth' => (int) $ob['DEPTH_LEVEL'],
            ];
            foreach ($this->additionalProperties as $property) {
                $ufName = 'UF_' . strtoupper($property);
                $section[$property] = isset($ob[$ufName]) ? $ob[$ufName] : null;
            }
            $sections[] = $section;
        }

        return $sections;
    }

    /**
     * Загружает описание элементов по коду.
     *
     * @param string   $code
     * @param int|bool $sectionId
     *
     * @return array|null
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function loadElementByCodeAndSection($code, $sectionId = false)
    {
        $arIblock = $this->getIblockArrayByIdentity();

        $select = [
            'ID',
            'NAME',
            'CODE',
            'PREVIEW_TEXT',
            'PREVIEW_PICTURE',
            'DETAIL_TEXT',
            'DETAIL_PICTURE',
            'IBLOCK_SECTION_ID',
            'DETAIL_PAGE_URL',
        ];
        if ($this->isComplexProperty) {
            $select[] = 'PROPERTY_' . $this->isComplexProperty;
        }
        foreach ($this->additionalProperties as $property) {
            $select[] = 'PROPERTY_' . $property;
        }

        $res = CIBlockElement::getList(
            ['id' => 'asc'],
            [
                'IBLOCK_ID' => $arIblock['ID'],
                'CODE' => $code,
                'SECTION_ID' => $sectionId,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
            ],
            false,
            false,
            $select
        );

        $element = null;
        if ($ob = $res->getNext()) {
            $element = [
                'id' => (int) $ob['ID'],
                'type' => 'element',
                'name' => $ob['NAME'],
                'code' => $ob['CODE'],
                'url' => $ob['DETAIL_PAGE_URL'],
                'preview_text' => $ob['PREVIEW_TEXT'],
                'preview_picture' => !empty($ob['PREVIEW_PICTURE'])
                    ? CFile::getFileArray($ob['PREVIEW_PICTURE'])
                    : null,
                'detail_text' => $ob['DETAIL_TEXT'],
                'detail_picture' => !empty($ob['DETAIL_PICTURE'])
                    ? CFile::getFileArray($ob['DETAIL_PICTURE'])
                    : null,
                'parent_id' => (int) $ob['IBLOCK_SECTION_ID'] ?: false,
                'is_complex' => $this->isComplexProperty
                    && !empty($ob[strtoupper("PROPERTY_{$this->isComplexProperty}_VALUE")]),
            ];
            foreach ($this->additionalProperties as $property) {
                $propertyName = 'PROPERTY_' . strtoupper($property) . '_VALUE';
                $element[$property] = isset($ob[$propertyName]) ? $ob[$propertyName] : null;
            }
        }

        return $element;
    }

    /**
     * Возвращает массив с описанием инфоблока по его id или коду.
     *
     * @return array
     *
     * @throws \marvin255\bxfoundation\Exception
     */
    protected function getIblockArrayByIdentity()
    {
        $field = is_numeric($this->iblock) ? 'ID' : 'CODE';
        $return = $this->locator->findBy($field, $this->iblock);

        if ($return === null) {
            throw new Exception(
                "Can't find iblock by identity: {$this->iblock}"
            );
        }

        return $return;
    }
}
