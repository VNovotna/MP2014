<?php

use Nette\Application\UI\Form;

/**
 * Description of GameSettingsPresenter
 *
 * @author viky
 */
class GameSettingsPresenter extends SecuredPresenter {

    /** @var DB\ServerRepository */
    private $serverRepo;

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('server-settings', 'view') and !$this->user->isAllowed('server-settings', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
        $this->serverRepo = $this->context->serverRepository;
    }

    /**
     * @return Nette\Application\UI\Form
     */
    protected function createComponentServerParams() {
        $form = new Form();
        $form->addGroup('runtime');
        $form->addText('name', 'Jméno: ', 30, 20)
                ->addRule(Form::FILLED, 'server musí mít jméno');
        $form->addText('path', 'Cesta: ', 30)
                ->addRule(Form::FILLED, 'je nutné specifikovat cestu');
        $form->addText('executable', 'jméno .jar: ', 30)
                ->addRule(Form::FILLED, 'je nutno specifikovat jméno .jar souboru');
        $form->addSubmit('update', 'Upravit');
        if (!$this->user->isAllowed('server-settings', 'edit')) {
            $form['update']->setDisabled();
        }
        //defaults
        $values = $this->serverRepo->findById($this->selectedServerId)->fetch();
        $form->setValues($values);
        $form->onSuccess[] = $this->serverParamsFormSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function serverParamsFormSubmitted(Form $form) {
        if ($this->user->isAllowed('server-settings', 'edit')) {
            $values = $form->getValues();
            if (substr($values->path, -1) != '/') {
                $values->path .= '/';
            }
            $this->serverRepo->updateServerParams(
                    $this->selectedServerId, $values->name, $values->path, $values->executable
            );
            $this->flashMessage('Nastavení aktualizováno.', 'success');
        } else {
            $this->flashMessage('Jako operátor nemáte právo editovat nastavení.', 'error');
        }
        $this->redirect('this');
    }

}
