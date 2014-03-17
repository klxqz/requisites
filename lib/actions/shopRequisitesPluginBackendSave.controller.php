<?php

class shopRequisitesPluginBackendSaveController extends waJsonController {

    protected $plugin_id = array('shop', 'requisites');
    protected $plugins = array(
        'consignmentru' => array(
            'type' => 'printform',
            'replaces' => array(),
        ),
        'invoiceru' => array(
            'type' => 'printform',
            'replaces' => array(),
        ),
        'invoicephys' => array(
            'type' => 'payment',
            'replaces' => array(
                'COMPANYNAME' => 'company_name',
                'BANK_ACCOUNT_NUMBER' => 'bank_account_number',
                'INN' => 'inn',
                'KPP' => 'kpp',
                'BANKNAME' => 'bank_name',
                'BANK_KOR_NUMBER' => 'bank_kor_number',
                'BIK' => 'bik',
            )
        ),
        'invoicejur' => array(
            'type' => 'payment',
            'replaces' => array(
                'COMPANYNAME' => 'company_name',
                'COMPANYADDRESS' => 'company_address',
                'COMPANYPHONE' => 'company_phone',
                'BANK_ACCOUNT_NUMBER' => 'bank_account_number',
                'INN' => 'inn',
                'KPP' => 'kpp',
                'BANKNAME' => 'bank_name',
                'BANK_KOR_NUMBER' => 'bank_kor_number',
                'BIK' => 'bik',
            )
        ),   
    );

    public function execute() {
        try {
            $app_settings_model = new waAppSettingsModel();
            $shop_requisites = waRequest::post('shop_requisites', array());
            $profiles = waRequest::post('profiles', array());
            $profile_names = waRequest::post('profile_names', array());


            foreach ($shop_requisites as $name => $value) {
                $app_settings_model->set($this->plugin_id, $name, $value);
            }

            $app_settings_model->set($this->plugin_id, 'profiles', json_encode($profiles));
            $app_settings_model->set($this->plugin_id, 'profile_names', json_encode($profile_names));
            $profile_id = $shop_requisites['profile'];
            if ($profile_id && isset($profiles[$profile_id])) {
                $profile = $profiles[$profile_id];

                foreach ($this->plugins as $plugin_id => $plugin_settings) {

                    if ($plugin_settings['type'] == 'printform') {
                        $plugin = waSystem::getInstance()->getPlugin($plugin_id);
                        $plugin->saveSettings($profile);
                        waSystem::popActivePlugin();
                    } elseif ($plugin_settings['type'] == 'payment') {
                        $settings = array();
                        foreach ($profile as $key => $value) {
                            if (isset($plugin_settings['replaces'][$key])) {
                                $_key = $plugin_settings['replaces'][$key];
                                $settings[$_key] = $value;
                            } else {
                                $settings[$key] = $value;
                            }
                        }

                        $model = new shopPluginModel();
                        $payment_plugins = $model->listPlugins(shopPluginModel::TYPE_PAYMENT, array('all' => true));
                        foreach ($payment_plugins as $payment_plugin) {
                            if ($payment_plugin['plugin'] == $plugin_id) {                                
                                $payment_plugin['settings'] = $settings;
                                $payment_plugin['shipping'] = $model->listPlugins(shopPluginModel::TYPE_SHIPPING, array('payment' => $payment_plugin['id'], 'all' => true));
                                shopPayment::savePlugin($payment_plugin);
                            }
                        }
                    }
                }
            }
            $this->response['message'] = "Сохранено";
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }

}
