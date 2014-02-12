<?php

use \Nette\Application\UI\Form;

/**
 * For new server creation
 *
 * @author viky
 */
class CreatePresenter extends SecuredPresenter {

    /** @var array */
    private $config;

    /** @var DB\ServerRepository */
    private $serverRepo;

    /** @var string path of the new server */
    private $path;

    protected function startup() {
        parent::startup();
        $this->config = $this->context->systemConfigModel->getConfig();
        $this->serverRepo = $this->context->serverRepository;
    }

    protected function createComponentNewServerForm() {
        $form = new Form();
        $form->addGroup();
        $form->addText('name', "Jméno serveru:")
                ->addRule(Form::FILLED, "Server musíte nějak pojmenovat.");
        if ($this->config['storage']['common'] == 0) {
            $form->addText('path', 'Cesta v systému souborů:', 36)
                    ->addRule(Form::FILLED, " ")
                    ->addRule(Form::PATTERN, "Toto není platná cesta ke složce, ty začínají a končí lomítkem.", "^/[a-z/]+[/]$");
        } else {
            $form->addHidden('path', $this->config['storage']['common']);
        }
        $form->addSubmit('send', 'Pokračovat');
        $form->onSuccess[] = $this->newServerFormSubmitted;
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function newServerFormSubmitted($form) {
        $values = $form->getValues();
        $servers = $this->serverRepo->findBy(array('user_id' => $this->user->id))->fetchPairs('id', 'name');
        //TODO check if path is useable
        if (count($servers) < $this->config['server']['number']) {
            if (!in_array($values->name, $servers)) {
                $port = $this->serverRepo->findFreePort();
                $id = $this->serverRepo->addServer($this->user->id, $values->name, $values->path, 'placeholder', $port);
                $this->redirect('phase2', array('newServerId' => $id));
            } else {
                $this->flashMessage("Server s tímto jménem už jste vytvořili", "error");
                $this->redirect('this');
            }
        } else {
            $this->flashMessage("Máte maximální počet (" . $this->config['server']['number'] . ") serverů na hráče.", "error");
            $this->redirect('this');
        }
    }

    protected function createComponentPhase2Form() {
        $form = new Form();
        $form->addGroup('--');
        $form->addText('path', ":")
                ->addRule(Form::FILLED, " ");
        $form->addSubmit('send', 'V');
        $form->onSuccess[] = $this->newServerFormSubmitted;
        return $form;
    }

    public function phase2FormSubmitted($form) {
        $this->redirect('phase3', array('name' => NULL, 'path' => NULL));
    }

    protected function createComponentDownload() {
        return new Updates($this->context->gameUpdateModel, $this->path);
    }

    public function actionPhase2($newServerId) {
        $this->path = $this->serverRepo->getPath($newServerId);
    }

    public function renderPhase2($newServerId) {
        $this->template->$newServerId = $newServerId;
    }

}
