<?php

namespace Marvin255Bxfoundation;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use marvin255\bxfoundation\application\Application;
use CBitrixComponent;
use CIBlockElement;
use CIBlock;
use CFile;
use InvalidArgumentException;

/**
 * Класс для компонента: Включаемая область.
 */
class Includes extends CBitrixComponent
{
    /**
     * Список всех включаемых областей, сгруппированный по инфоблокам.
     *
     * @var array
     */
    private static $areas = [];

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
        if (!Loader::includeModule('marvin255.bxfoundation')) {
            throw new LoaderException("Can't load module marvin255.bxfoundation");
        }

        $p['IBLOCK'] = empty($p['IBLOCK']) ? 'includes' : $p['IBLOCK'];

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
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    public function executeComponent()
    {
        $this->arResult['area'] = $this->getAreaInIblockByCode(
            $this->arParams['IBLOCK_ID'],
            $this->arParams['CODE']
        );

        //выводим возможность редактирования для битриксового интерфейса
        if (!empty($this->arResult['area'])) {
            global $USER;
            global $APPLICATION;
            if ($USER->IsAuthorized() && $APPLICATION->GetShowIncludeAreas()) {
                $arButtons = CIBlock::GetPanelButtons(
                    $this->arResult['area']['iblock_id'],
                    $this->arResult['area']['id'],
                    null
                );
                $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu(
                    $APPLICATION->GetPublicShowMode(),
                    $arButtons
                ));
            }
            $this->includeComponentTemplate();
        }

        return $this->arResult;
    }

    /**
     * Возвращает включаемую область по ее коду.
     *
     * @param int    $iblockId
     * @param string $code
     *
     * @return array|null
     *
     * @throws \InvalidArgumentException
     */
    protected function getAreaInIblockByCode($iblockId, $code)
    {
        $iblockAreas = $this->getAreasForIblock($iblockId);

        return empty($iblockAreas[$code]) ? null : $iblockAreas[$code];
    }

    /**
     * Возвращает список всех включаемых областей для указанного инфоблока.
     *
     * @param int $iblockId
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getAreasForIblock($iblockId)
    {
        if (!isset(static::$areas[$iblockId])) {
            $cache = Application::getInstance()->cache;
            $cid = get_class($this) . '_' . $iblockId;
            $duration = 60 * 60 * 24;
            static::$areas[$iblockId] = $cache->get($cid, $duration);
            if (static::$areas[$iblockId] === false) {
                static::$areas[$iblockId] = $this->getIblockElementsList($iblockId);
                $cache->set($cid, static::$areas[$iblockId], $duration, ["iblock_id_{$iblockId}"]);
            }
        }

        return static::$areas[$iblockId];
    }

    /**
     * Загружает все включаемые области для инфоблока из бд.
     *
     * @param int $iblockId
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getIblockElementsList($iblockId)
    {
        if (intval($iblockId) === 0) {
            throw new InvalidArgumentException('Empty iblockId');
        }

        $return = [];
        $res = CIBlockElement::getList(
            [],
            [
                '=IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'CODE',
                'NAME',
                'PREVIEW_TEXT',
                'DETAIL_TEXT',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
            ]
        );
        while ($ob = $res->getNext()) {
            $return[$ob['CODE']] = [
                'id' => $ob['ID'],
                'iblock_id' => $ob['IBLOCK_ID'],
                'code' => $ob['CODE'],
                'name' => $ob['NAME'],
                'preview_text' => $ob['PREVIEW_TEXT'],
                'detail_text' => $ob['DETAIL_TEXT'],
                'preview_picture' => $ob['PREVIEW_PICTURE']
                    ? CFile::getFileArray($ob['PREVIEW_PICTURE'])
                    : null,
                'detail_picture' => $ob['DETAIL_PICTURE']
                    ? CFile::getFileArray($ob['DETAIL_PICTURE'])
                    : null,
            ];
        }

        return $return;
    }
}
