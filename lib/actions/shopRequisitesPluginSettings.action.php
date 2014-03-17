<?php

class shopRequisitesPluginSettingsAction extends waViewAction {

    protected $plugin_id = array('shop', 'requisites');

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        $settings = $app_settings_model->get($this->plugin_id);
        $profiles = $app_settings_model->get($this->plugin_id, 'profiles');
        $profile_names = $app_settings_model->get($this->plugin_id, 'profile_names');
        $profiles = $profiles ? json_decode($profiles, true) : array();
        $profile_names = $profile_names ? json_decode($profile_names, true) : array();
        $this->view->assign('settings', $settings);
        $this->view->assign('profiles', $profiles);
        $this->view->assign('profile_names', $profile_names);
    }

}
