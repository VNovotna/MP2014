<?php

/**
 * Description of SystemSettingsPresenter
 *
 * @author viky
 */
class SystemSettingsPresenter extends SecuredPresenter {

    /** @var SystemConfigModel */
    private $configModel;

    protected function startup() {
        parent::startup();
        $this->configModel = $this->context->systemConfigModel;
    }

    public function renderDefault() {
        dump($this->configModel->getConfig());
    }

}
