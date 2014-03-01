<?php

use Nette\Application\UI\Form;

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
        if (!$this->user->isAllowed('system')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSettingsUpdate() {
        $form = new Form();
        $form->addGroup('aktualizace');
        $form->addText('number', "Max. počet uložených .jar:")
                ->setAttribute('type', 'number')
                ->addRule(Form::INTEGER, "Hodnota musí být číslo.");
        $form->addRadioList('foreignBool', "Povolit nahrávat vlastní .jar:", array(FALSE=>'FALSE',TRUE=>'TRUE'));
        $form->addTextArea('url', 'Zdroje aktualizací:',36,3);
        $form->addTextArea('regex', 'Parsovací regex:',36,3);
        $form->addSubmit('send', 'Uložit');
        $form->setDefaults($this->configModel->getConfig('update'));
        $form->onSuccess[] = $this->settingsUpdateSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsUpdateSubmitted($form) {
        
    }
   /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSettingsBackup() {
        $form = new Form();
        $form->addGroup('zálohy');
        $form->addText('number', "Max. počet uložených záloh:")
                ->setAttribute('type', 'number')
                ->addRule(Form::INTEGER, "Hodnota musí být číslo.");
        $form->addRadioList('foreignBool', "Povolit nahrávat vlastních záloh:", array(TRUE=>'TRUE', FALSE=>'FALSE'));
        $form->addSubmit('send', 'Uložit');
        $form->setDefaults($this->configModel->getConfig('backup'));
        $form->onSuccess[] = $this->settingsBackupSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsBackupSubmitted($form) {
        
    }
       /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSettingsStorage() {
        $form = new Form();
        $form->addGroup('úložiště');
        $form->addText('common', "Úložistě herních dat:")
                ->addRule(Form::FILLED, "Musíte vyplnit 0 nebo cestu v systému");
        $form->addSubmit('send', 'Uložit');
        $form->setDefaults($this->configModel->getConfig('storage'));
        $form->onSuccess[] = $this->settingsStorageSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsStorageSubmitted($form) {
        
    }       /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSettingsServer() {
        $form = new Form();
        $form->addGroup('MC server');
        $form->addText('number', "Max. počet serverů na hráče:")
                ->setAttribute('type', 'number')
                ->addRule(Form::INTEGER, "Hodnota musí být číslo.");
        $form->addSubmit('send', 'Uložit');
        $form->setDefaults($this->configModel->getConfig('server'));
        $form->onSuccess[] = $this->settingsServerSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsServerSubmitted($form) {
        
    }
    public function renderDefault() {
        //dump($this->configModel->getConfig());
    }

}
