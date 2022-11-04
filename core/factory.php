<?php

/* 加强后台编辑器功能 */
if (Helper::options()->JEditor !== 'off') {
    Typecho_Plugin::factory('admin/write-post.php')->richEditor  = array('Editor', 'Edit');
    Typecho_Plugin::factory('admin/write-page.php')->richEditor  = array('Editor', 'Edit');
}

class Editor
{
    public static function Edit()
    {
?>
        <link rel="stylesheet" type="text/css" href="<?php echo autoCdnUrl('assets/css-local/npm/APlayer.min.css'); ?>">
        <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/prism-theme-one-light-dark@1.0.4/prism-onedark.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo autoCdnUrl('library/joe.write/css/joe.write.min.css'); ?>">
        <script>
            window.JoeConfig = {
                uploadAPI: '<?php Helper::security()->index('/action/upload'); ?>',
                emojiAPI: '<?php echo autoCdnUrl('library/joe.write/json/emoji.json') ?>',
                expressionAPI: '<?php echo autoCdnUrl('library/joe.write/json/expression.json') ?>',
                characterAPI: '<?php echo autoCdnUrl('library/joe.write/json/character.json') ?>',
                playerAPI: '<?php Helper::options()->JCustomPlayer ? Helper::options()->JCustomPlayer() : Helper::options()->themeUrl('player.php?url=') ?>',
                autoSave: <?php Helper::options()->autoSave(); ?>,
                themeURL: '<?php echo autoCdnUrl(); ?>',
                canPreview: false
            }
        </script>
        <script src="<?php echo autoCdnUrl('assets/js-local/npm/APlayer.min.js'); ?>"></script>
        <script src="https://fastly.jsdelivr.net/npm/typecho-joe-next@6.2.4/plugin/prism/prism.min.js"></script>
        <script src="<?php echo autoCdnUrl('library/joe.write/parse/parse.min.js') ?>"></script>
        <script src="<?php echo autoCdnUrl('library/joe.write/dist/index.bundle.js') ?>"></script>
        <script src="<?php echo autoCdnUrl('assets/js/joe.short.min.js') ?>"></script>
<?php
    }
}
