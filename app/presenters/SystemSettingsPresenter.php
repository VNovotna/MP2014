<?php

use Nette\Application\UI\Form;

/**
 * Description of SystemSettingsPresenter
 *
 * @author viky
 */
class SystemSettingsPresenter extends SecuredPresenter {

    /** @var SystemConfigModel @inject */
    public $configModel;

    protected function startup() {
        parent::startup();
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
        $form->addRadioList('foreign', "Povolit nahrávat vlastní .jar:", array('1' => 'TRUE', '2' => 'FALSE'));
        $form->addText('stable', 'Zdroje stabilních aktualizací:', 36);
        $form->addText('unstable', 'Zdroje dev aktualizací:', 36);
        $form->addText('regex', 'Parsovací regex:', 36);
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = $this->settingsUpdateSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsUpdateSubmitted($form) {
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            if ($key == 'foreign') {
                $value == 2 ? $this->configModel['update'][$key] = FALSE : $this->configModel['update'][$key] = TRUE;
            } else {
                $this->configModel['update'][$key] = $value;
            }
        }
        $this->flashMessage('Uloženo', 'success');
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
        $form->addRadioList('foreign', "Povolit nahrávat vlastních záloh:", array('1' => 'TRUE', '2' => 'FALSE'));
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = $this->settingsBackupSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsBackupSubmitted($form) {
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            if ($key == 'foreign') {
                $value == 2 ? $this->configModel['backup'][$key] = FALSE : $this->configModel['backup'][$key] = TRUE;
            } else {
                $this->configModel['backup'][$key] = $value;
            }
        }
        $this->flashMessage('Uloženo', 'success');
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSettingsStorage() {
        $form = new Form();
        $form->addGroup('úložiště');
        $form->addText('common', "Úložistě herních dat:")
                ->addRule(Form::FILLED, "Musíte vyplnit 0 nebo cestu v systému končí lomítkem");
        $form['info'] = new InfoSpan('', 'Vyplňte 0 nebo cestu v systému končí lomítkem', 'icon info');
        $form->addSubmit('send', 'Uložit');
        $form->setDefaults($this->configModel['storage']);
        $form->onSuccess[] = $this->settingsStorageSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function settingsStorageSubmitted($form) {
        $this->configModel['storage']['common'] = $form->getValues()->common;
        $this->flashMessage('Uloženo', 'success');
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSettingsServer() {
        $form = new Form();
        $form->addGroup('MC server');
        $form->addText('number', "Maximální počet serverů na hráče:")
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
        $this->configModel['server']['number'] = $form->getValues()->number;
        $this->flashMessage('Uloženo', 'success');
    }

    public function renderDefault() {
        $defaults = SystemConfigModel::falseToTwo($this->configModel->getConfig());
        $this['settingsUpdate']->setDefaults($defaults['update']);
        $this['settingsBackup']->setDefaults($defaults['backup']);
    }

}
