<?php

class shopRequisitesPluginSettingsAction extends waViewAction {

    protected $plugin_id = array('shop', 'requisites');

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        $settings = $app_settings_model->get($this->plugin_id);
        $this->view->assign('settings', $settings);
    }

}
