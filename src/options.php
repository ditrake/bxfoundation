<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'creative.phpmailer');

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot() . '/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl('tabControl', array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
    ),
));

if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {
    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage(array(
            'MESSAGE' => Loc::getMessage('REFERENCES_OPTIONS_RESTORED'),
            'TYPE' => 'OK',
        ));
    } else {
        $fields = [
        ];
        foreach ($fields as $field) {
            if ($request->getPost($field) !== null) {
                Option::set(
                    ADMIN_MODULE_NAME,
                    $field,
                    $request->getPost($field)
                );
            }
        }
        CAdminMessage::showMessage(array(
            'MESSAGE' => Loc::getMessage('REFERENCES_OPTIONS_SAVED'),
            'TYPE' => 'OK',
        ));
    }
}

$tabControl->begin();
?>

<form method="post" action="<?php echo sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID); ?>">
	<?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    /*
    ?>
    <tr>
        <td width="40%">
            <label><?php echo Loc::getMessage("CREATIVE_PHPMAILER_PREFERENCIES_SMTP_PORT") ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="smtp_port"
                   value="<?php echo htmlentities(Option::get(ADMIN_MODULE_NAME, "smtp_port"));?>"
                   />
        </td>
    </tr>
    <?php
    */
    $tabControl->buttons();
    ?>
	<input type="submit"
		   name="save"
		   value="<?php echo Loc::getMessage('MAIN_SAVE'); ?>"
		   title="<?php echo Loc::getMessage('MAIN_OPT_SAVE_TITLE'); ?>"
		   class="adm-btn-save"
		   />
	<input type="submit"
		   name="restore"
		   title="<?php echo Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS'); ?>"
		   onclick="return confirm('<?php echo  addslashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING')); ?>')"
		   value="<?php echo Loc::getMessage('MAIN_RESTORE_DEFAULTS'); ?>"
		   />
	<?php
    $tabControl->end();
    ?>
</form>
