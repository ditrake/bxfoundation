<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = [
    'NAME' => Loc::getMessage('MARVIN255_BXFOUNDATION_INCLUDES_NAME'),
    'DESCRIPTION' => Loc::getMessage('MARVIN255_BXFOUNDATION_INCLUDES_DESCRIPTION'),
    'SORT' => 320,
    'COMPLEX' => 'N',
    'PATH' => [
        'ID' => 'content',
        'NAME' => Loc::getMessage('MARVIN255_BXFOUNDATION_INCLUDES_CONTENT'),
        'CHILD' => [
            'ID' => 'marvin255.bxfoundation',
            'NAME' => Loc::getMessage('MARVIN255_BXFOUNDATION_INCLUDES_NAMESPACE'),
        ],
    ],
];
