<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?php if (!empty($arResult['area']['detail_text'])): ?>
    <div class="include-text">
        <div class="include-text__content">
            <?php echo $arResult['area']['detail_text']; ?>
        </div>
    </div>
<?php endif; ?>
