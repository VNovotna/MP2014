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

    /** @var FileModel */
    private $fileModel;

    /** @var boolean */
    private $commonStorage;

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('server-settings', 'view') and !$this->user->isAllowed('server-settings', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
        $this->context->systemConfigModel['storage']['common'] == "0" ? $this->commonStorage = FALSE : $this->commonStorage = TRUE;
        $this->serverRepo = $this->context->serverRepository;
        $this->fileModel = $this->context->fileModel;
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
                ->addRule(Form::FILLED, 'je nutné specifikovat cestu')
                ->addRule(Form::PATTERN, "Toto není platná cesta ke složce, ty začínají a končí lomítkem.", "^/[^/].*/$");
        $form->addText('executable', 'jméno .jar: ', 30)
                ->addRule(Form::FILLED, 'je nutno specifikovat jméno .jar souboru');
        $form->addSubmit('update', 'Upravit')->setAttribute('class', 'ajax');
        if($this->commonStorage){
            $form['path']->setDisabled();
            $form['executable']->setDisabled();
        }
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
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @return FileEditor Component
     */
    protected function createComponentServerProps() {
        return new FileEditor(
                $this->serverRepo->getPath($this->selectedServerId) . 'server.properties', $this->fileModel, $this->user->isAllowed('server-settings', 'edit'), array('/server-port=[0-9]*/' => 'server-port=' . $this->serverRepo->getRunParams($this->selectedServerId)['port']));
    }

    /**
     * @return FileEditor Component
     */
    protected function createComponentBannedIPs() {
        return new FileEditor($this->serverRepo->getPath($this->selectedServerId) . 'banned-ips.json', $this->fileModel, $this->user->isAllowed('server-settings', 'edit'));
    }

    /**
     * @return FileEditor Component
     */
    protected function createComponentBannedPlayers() {
        return new FileEditor($this->serverRepo->getPath($this->selectedServerId) . 'banned-players.json', $this->fileModel, $this->user->isAllowed('server-settings', 'edit'));
    }

    /**
     * @return FileEditor Component
     */
    protected function createComponentWhiteList() {
        return new FileEditor($this->serverRepo->getPath($this->selectedServerId) . 'whitelist.json', $this->fileModel, $this->user->isAllowed('server-settings', 'edit'));
    }

}
