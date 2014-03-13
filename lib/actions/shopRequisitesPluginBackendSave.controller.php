<?php

class shopRequisitesPluginBackendSaveController extends waJsonController {

    protected $plugin_id = array('shop', 'requisites');

    public function execute() {
        try {
            $app_settings_model = new waAppSettingsModel();
            $shop_requisites = waRequest::post('shop_requisites');

            foreach($shop_requisites as $name => $value) {
                $app_settings_model->set($this->plugin_id, $name, $value);
            }
            $plugin_id = 'consignmentru';
            $plugin = waSystem::getInstance()->getPlugin($plugin_id);
            
            $plugin->saveSettings($shop_requisites);
            waSystem::popActivePlugin();

            $this->response['message'] = "Сохранено";
        } catch (Exception $e) {
            $this->setError($e->getMessage());
        }
    }

}
