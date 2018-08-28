<?php

namespace Marvin255Bxfoundation;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use marvin255\bxfoundation\application\Application;
use CBitrixComponent;
use CIBlockSection;
use CIBlockElement;
use CFile;
use InvalidArgumentException;

/**
 * Класс для компонента: Пункты меню из инфоблока.
 */
class Menu extends CBitrixComponent
{
    /**
     * @var array
     */
    private static $cachedMenu;

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public function onPrepareComponentParams($p)
    {
        if (!Loader::includeModule('iblock')) {
            throw new LoaderException("Can't load module iblock");
        }

        $p['IBLOCK'] = empty($p['IBLOCK']) ? 'menu' : $p['IBLOCK'];

        $iblockLocator = Application::getInstance()->iblockLocator;
        $p['IBLOCK_ID'] = $iblockLocator->getIdByCode($p['IBLOCK']);
        if (!$p['IBLOCK_ID']) {
            throw new InvalidArgumentException(
                "Can't find iblock {$p['IBLOCK']}"
            );
        }

        if (empty($p['CODE'])) {
            throw new InvalidArgumentException(
                "CODE parameter can't be empty"
            );
        }

        return parent::onPrepareComponentParams($p);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function executeComponent()
    {
        return $this->buildMenuFromIblockByCode(
            $this->arParams['IBLOCK_ID'],
            $this->arParams['CODE']
        );
    }

    /**
     * Строит меню из данных инфоблока.
     *
     * @param int    $iblockId
     * @param string $code
     *
     * @return array|null
     *
     * @throws \InvalidArgumentException
     */
    protected function buildMenuFromIblockByCode($iblockId, $code)
    {
        $return = null;

        if (!isset(self::$cachedMenu[$iblockId])) {
            $sections = $this->loadSectionsFromIblock($iblockId);
            $elements = $this->loadElementsFromIblock($iblockId);
            self::$cachedMenu[$iblockId] = array_merge($sections, $elements);
        }

        foreach (self::$cachedMenu[$iblockId] as $item) {
            if ($item['type'] === 'section' && $item['code'] === $code && $item['depth'] === 1) {
                $return = self::sortMenu(self::$cachedMenu[$iblockId], $item['id']);
                break;
            }
        }

        return $return;
    }

    /**
     * Загружает все активные разделы из указанного инфоблока.
     *
     * @param int $iblockId
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function loadSectionsFromIblock($iblockId)
    {
        if (intval($iblockId) === 0) {
            throw new InvalidArgumentException('Empty iblockId');
        }

        $return = [];

        $res = CIBlockSection::GetList(
            ['depth_level' => 'asc'],
            [
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
            ],
            false
        );
        while ($section = $res->getNext()) {
            $return[] = [
                'id' => $section['ID'],
                'code' => $section['CODE'],
                'depth' => (int) $section['DEPTH_LEVEL'],
                'type' => 'section',
                'parent' => $section['IBLOCK_SECTION_ID'] ? $section['IBLOCK_SECTION_ID'] : null,
                'label' => $section['NAME'],
                'url' => $section['SECTION_PAGE_URL'],
                'sort' => $section['SORT'],
                'description' => $section['DESCRIPTION'],
                'image' => isset($section['PICTURE'])
                    ? CFile::GetFileArray($section['PICTURE'])
                    : null,
            ];
        }

        return $return;
    }

    /**
     * Загружает все активные элементы из указанного инфоблока.
     *
     * @param int $iblockId
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function loadElementsFromIblock($iblockId)
    {
        if (intval($iblockId) === 0) {
            throw new InvalidArgumentException('Empty iblockId');
        }

        $return = [];

        $res = CIBlockElement::GetList(
            ['sort' => 'asc', 'name' => 'asc'],
            [
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
            ],
            false,
            false,
            [
                'ID',
                'CODE',
                'NAME',
                'IBLOCK_SECTION_ID',
                'SORT',
                'PREVIEW_TEXT',
                'PREVIEW_PICTURE',
            ]
        );
        while ($element = $res->Fetch()) {
            $return[] = [
                'id' => $element['ID'],
                'type' => 'element',
                'parent' => $element['IBLOCK_SECTION_ID'] ? $element['IBLOCK_SECTION_ID'] : null,
                'label' => $element['NAME'],
                'url' => $element['CODE'],
                'sort' => $element['SORT'],
                'description' => $element['PREVIEW_TEXT'],
                'image' => isset($element['PREVIEW_PICTURE'])
                    ? CFile::GetFileArray($element['PREVIEW_PICTURE'])
                    : null,
            ];
        }

        return $return;
    }

    /**
     * Сортирует меню и выстраивает в иерархическом порядке.
     *
     * @param array $menu
     *
     * @return array
     */
    protected static function sortMenu(&$menu, $parent = null, $depth = 1)
    {
        $return = [];
        $sort = [];
        $i = 0;
        foreach ($menu as $item) {
            if ($item['parent'] !== $parent) {
                continue;
            }
            $return[$i] = [
                $item['label'],
                $item['url'],
                [],
                [
                    'FROM_IBLOCK' => true,
                    'IS_PARENT' => false,
                    'DEPTH_LEVEL' => $depth,
                    'ID' => $item['id'],
                    'IMAGE' => $item['image'],
                    'DESCRIPTION' => $item['description'],
                    'MENU_ITEM_TYPE' => $item['type'],
                ],
                '',
            ];
            $sort[$i] = $item['sort'];
            ++$i;
        }
        array_multisort($sort, SORT_ASC, $return);
        $new = [];
        foreach ($return as $key => $item) {
            $children = null;
            if ($item[3]['MENU_ITEM_TYPE'] === 'section') {
                $children = self::sortMenu($menu, $item[3]['ID'], $depth + 1);
            }
            if ($children) {
                $item[3]['IS_PARENT'] = true;
                $new[] = $item;
                $new = array_merge($new, $children);
            } else {
                $new[] = $item;
            }
        }

        return $new;
    }
}
