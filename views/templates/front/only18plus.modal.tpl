{*
/**
 * NOTICE OF LICENSE
 *
 * only18plus is a module for blocking and verifying user age
 * Copyright (C) 2017 Yuri Blanc
 * Email: yuxblank@gmail.com
 * Website: www.yuriblanc.it
 * This program is distributed WITHOUT ANY WARRANTY;
 * @license GNU General Public License v3.0
 */
*}

<script>
    $.only18Plus({
        "redirectTo": "{$base_dir}",
        "minAge": {$only18plus.ONLY18PLUS_REQUIRED_AGE},
        "title": "{$only18plus.ONLY18PLUS_MODAL_TITLE}",
        "text": "{$only18plus.ONLY18PLUS_POLICY_TEXT}",
        "ajaxUrl" : '{$base_dir}modules/only18plus/ajaxCalls.php',
        "language" : '{$lang_iso}'
    });
</script>