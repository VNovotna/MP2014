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
                    //TODO
                    ->addRule(Form::PATTERN, "Toto není platná cesta ke složce, ty začínají a končí lomítkem.", "\/[a-z]+\/");
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
        if (count($servers) < $this->config['server']['number']) {
            if (!in_array($values->name, $servers)) {
                $port = $this->serverRepo->findFreePort();
                $this->serverRepo->addServer($this->user->id, $values->name, $values->path, 'placeholder', $port);
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

    public function renderPhase2($name) {
        $this->template->name = $name;
    }

    public function renderPhase3($name, $path) {
        $this->template->name = $name;
    }

}
