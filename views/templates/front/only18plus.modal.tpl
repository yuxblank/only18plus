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

    var only18PlusConfig = {
        redirectTo: "{$base_dir}",
        minAge: {$only18plus.ONLY18PLUS_REQUIRED_AGE},
        ajaxUrl: "{$base_dir}modules/only18plus/ajaxCalls.php",
        language: "{$lang_iso}",
        thank_you : "{$only18plus.thank_you}",
        access : "{$only18plus.access}",
        warning : "{$only18plus.warning}",
        no_access : "{$only18plus.no_access}",
        invalid_day : "{$only18plus.invalid_day}",
        invalid_month : "{$only18plus.invalid_month}",
        invalid_year : "{$only18plus.invalid_year}",
        service_error : "{$only18plus.service_error}",
        policy_text : "{$only18plus.policy_text}",
        modal_title : "{$only18plus.modal_title}",
        submit_label : "{$only18plus.submit_label}",
        months :  [
            {foreach $only18plus.months as $month}
            "{$month}",
            {/foreach} ]
    };
</script>