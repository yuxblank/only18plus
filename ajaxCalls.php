<?php
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
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');

$module = ModuleCore::getInstanceByName('only18plus');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        switch (Tools::getValue('action')) {
            case 'verifyAge' :
                $requestData = Tools::getValue('requestData');

                if (isset($requestData['date']) && $module->verifyAge($requestData['date'])) {
                    $module->setCookie();
                    die(Tools::jsonEncode(array('result' => 'ok')));
                } else {
                    die(Tools::jsonEncode(array('result' => 'ko')));
                }
                break;
            default:
                exit;
        }
}
exit;
