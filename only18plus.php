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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Only18plus extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'only18plus';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1-RC2';
        $this->author = 'Yuri Blanc';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('only18plus');
        $this->description = $this->l('only18plus is a module for blocking and verifying user age.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module');

    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayTop') &&
            Configuration::updateValue('ONLY18PLUS_LIVE_MODE', false) &&
            /*            Configuration::updateValue('ONLY18PLUS_POLICY_TEXT', $this->l('To buy and access this site you must be of legal age . Please include your birth date in the following fields to continue')) &&
                        Configuration::updateValue('ONLY18PLUS_MODAL_TITLE', $this->l('Confirm your age')) &&*/
            Configuration::updateValue('ONLY18PLUS_REQUIRED_AGE', 18);


    }

    public function uninstall()
    {

        return parent::uninstall() &&
            Configuration::deleteByName('ONLY18PLUS_LIVE_MODE') &&
            /*        Configuration::deleteByName('ONLY18PLUS_POLICY_TEXT') &&
                    Configuration::deleteByName('ONLY18PLUS_MODAL_TITLE') &&*/
            Configuration::deleteByName('ONLY18PLUS_REQUIRED_AGE');
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitOnly18plusModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitOnly18plusModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    public function renderList()
    {

    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable'),
                        'name' => 'ONLY18PLUS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable the module'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    /*array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'ONLY18PLUS_MODAL_TITLE',
                        'label' => $this->l('Modal Title'),
                        'desc' => $this->l('Title of the modal'),
                        'required' => true,
                        'lang' => true,
                        'hint' => $this->l ( 'Title of the modal')
                    ),
                    array(
                        'col' => 6,
                        'type' => 'textarea',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a text to be displayed as modal content where you explain your age checker policy'),
                        'name' => 'ONLY18PLUS_POLICY_TEXT',
                        'autoload_rte' => true,
                        'label' => $this->l('Warning text to be displayed'),
                        'required' => true,
                        'lang' => true,
                        'rows' => 10,
                        'cols' => 100,
                        'hint' => $this->l ( 'Invalid characters:' ).' <>;=#{}'
                    ),*/
                    array(
                        'col' => 3,
                        'type' => 'select',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Manimum age to allow access'),
                        'name' => 'ONLY18PLUS_REQUIRED_AGE',
                        'autoload_rte' => true,
                        'label' => $this->l('Minimum age to allow access to the website'),
                        'required' => true,
                        'hint' => $this->l('Enter a number'),
                        'options' => array(
                            'query' => $this->getAgeOptions(),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'updateConfig'
                ),
            ),
        );
    }


    private function getAgeOptions()
    {
        $age = [];
        for ($i = 1; $i < 101; $i++) {
            $age[] = array(
                "id" => $i,
                "name" => $i
            );
        }
        return $age;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'ONLY18PLUS_LIVE_MODE' => Configuration::get('ONLY18PLUS_LIVE_MODE'),
            /*            'ONLY18PLUS_POLICY_TEXT' => Configuration::get('ONLY18PLUS_POLICY_TEXT'),
                        'ONLY18PLUS_MODAL_TITLE' => Configuration::get('ONLY18PLUS_MODAL_TITLE'),*/
            'ONLY18PLUS_REQUIRED_AGE' => Configuration::get('ONLY18PLUS_REQUIRED_AGE')
        );

    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {

        if (Tools::isSubmit('submitOnly18plusModule')) {
            $form_values = $this->getConfigFormValues();

            foreach (array_keys($form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key, ''));
            }
        }
    }


    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookDisplayHeader()
    {
        $this->hookHeader();
    }

    public function setCookie()
    {
        $this->context->cookie->only18plus = true;
    }

    public function verifyAge($data)
    {
        return $this->getUserAge($data) >= Configuration::get('ONLY18PLUS_REQUIRED_AGE', '');
    }

    /**
     * Calculate user age from server timezone.
     * Todo define timezone from PS. This
     * @param $data
     * @return int
     */
    private function getUserAge($data)
    {
        try {
            $tz = new DateTimeZone(Configuration::get('PS_TIMEZONE'));
        } catch (Exception $exception){
            $tz = new DateTimeZone(ini_get('date.timezone'));
        }
        $age = DateTime::createFromFormat('d-m-Y', $data, $tz)
            ->diff(new DateTime('now', $tz))
            ->y;
        return $age;
    }

    public function hookDisplayTop()
    {
        $configValues = $this->getConfigFormValues();
        $configValues = array_merge($configValues, $this->getContextValues());
        $age = Configuration::get('ONLY18PLUS_REQUIRED_AGE');

        $configValues['policy_text'] = sprintf($configValues['policy_text'], $age);
        $this->context->smarty->assign('only18plus', $configValues);
        $this->context->smarty->assign("lang_iso", $this->context->language->iso_code);
        $this->context->smarty->assign("base_dir", $this->getBaseDir());

            if (!$this->context->customer->isLogged() && !$this->context->cookie->only18plus && $configValues['ONLY18PLUS_LIVE_MODE']) {
            return $this->display(__FILE__, 'only18plus.modal.tpl');
        }
    }

    private function getContextValues(){
        $months =  [
            $this->l('January', $this->name),
            $this->l('February',$this->name),
            $this->l('March',$this->name),
            $this->l('April',$this->name),
            $this->l('May', $this->name),
            $this->l('June',$this->name),
            $this->l('July',$this->name),
            $this->l('August', $this->name),
            $this->l('September',$this->name),
            $this->l('October',$this->name),
            $this->l('November',$this->name),
            $this->l('December',$this->name)];
        return array(
            'months' => $months,
            'submit_label' => $this->l('Submit', $this->name),
            'thank_you' => $this->l('Thank you!', $this->name),
            'access' => $this->l('Now you can access our store...', $this->name),
            'warning' => $this->l('Warning', $this->name),
            'no_access' => $this->l('You can not login to our shop as it is required greater age for buying.', $this->name),
            'invalid_day' => $this->l('Day missing or invalid', $this->name),
            'invalid_month' => $this->l('Month missing or invalid', $this->name),
            'invalid_year' => $this->l('Year missing or invalid', $this->name),
            'service_error' => $this->l('Unable to verify service, please come back later', $this->name),
            'policy_text' => $this->l('To continue, you must at least be %s years old, please verify your age by entering the date of birth in the form below .', $this->name),
            'modal_title' => $this->l('Confirm your age', $this->name),
        );
    }


    private function getBaseDir(){
        $url =  Tools::getShopDomain(true) . "/";
        if(Configuration::get('PS_SSL_ENABLED') && strpos($url, "http") !== false){
            return str_replace("http", "https", $url);
        }
        return $url;
    }
}
