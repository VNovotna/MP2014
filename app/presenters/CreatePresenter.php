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

    /** @var int id of new server */
    private $newServerId;

    protected function startup() {
        parent::startup();
        $this->config = $this->context->systemConfigModel->getConfig();
        $this->serverRepo = $this->context->serverRepository;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentNewServerForm() {
        $form = new Form();
        $form->addGroup('Vytvořit nový server');
        $form->addText('name', "Jméno serveru:")
                ->addRule(Form::FILLED, "Server musíte nějak pojmenovat.")
                ->addRule(Form::PATTERN, 'Jméno serveru musí obsahovat pouze písmena bez diakritiky a čísla.', '[a-zA-Z_0-9]+');
        if ($this->config['storage']['common'] === 0) {
            $form->addText('path', 'Cesta v systému souborů:', 36)
                    ->addRule(Form::FILLED, " ")
                    ->addRule(Form::PATTERN, "Toto není platná cesta ke složce, ty začínají a končí lomítkem.", "^/[a-z/]+[/]$");
        } else {
            $form->addHidden('path', $this->config['storage']['common']);
        }
        $form->addHidden('storage', $this->config['storage']['common']);
        $form->addSubmit('send', 'Pokračovat');
        $form->onSuccess[] = $this->newServerFormSubmitted;
        $servers = $this->serverRepo->findBy(array('user_id' => $this->user->id))->fetchPairs('id', 'name');
        if (count($servers) >= $this->config['server']['number']) {
            $form['send']->setDisabled();
            $form['name']->setDisabled();
            $form['span'] = new InfoSpan(NULL, "Máte maximální počet(" . $this->config['server']['number'] . ") serverů na hráče!", "icon warning");
        }
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function newServerFormSubmitted($form) {
        $values = $form->getValues();
        $servers = $this->serverRepo->findBy(array('user_id' => $this->user->id))->fetchPairs('id', 'name');
        if (!in_array($values->name, $servers)) {
            $port = $this->serverRepo->findFreePort();
            try {
                if ($values->storage === 0) {
                    $id = $this->serverRepo->addServer($this->user->id, $values->name, $values->path, 'placeholder', $port);
                } else {
                    $id = $this->serverRepo->addServer($this->user->id, $values->name, $values->path, 'placeholder', $port, $values->name);
                }
                $this->redirect('download', array('newServerId' => $id));
            } catch (Nette\InvalidArgumentException $e) {
                $this->flashMessage($e->getMessage(), "error");
                $this->redirect('this');
            }
        } else {
            $this->flashMessage("Server s tímto jménem už jste vytvořili", "error");
            $this->redirect('this');
        }
    }

    protected function createComponentDownload() {
        $updates = new Updates($this->context->gameUpdateModel, $this->path);
        $updates->setForImmediateUse($this->serverRepo, $this->newServerId, "Create:summary");
        return $updates;
    }

    public function handleDowComplete($newServerId) {
        $this->handleSwitchServer($newServerId, FALSE);
        $this->redirect('summary');
    }

    public function actionDownload($newServerId) {
        $this->newServerId = $newServerId;
        $this->path = $this->serverRepo->getPath($newServerId);
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->template->registerHelper('getVersion', '\gameUpdateModel::getVersionFromFileName');
    }

    public function createComponentServerList() {
        return new ServerList($this->serverRepo, $this->user->id);
    }

    public function renderDownload($newServerId) {
        $this->template->newServerId = $newServerId;
    }

    public function renderSummary() {
        $this->template->params = $this->serverRepo->getRunParams($this->selectedServerId);
        $this->flashMessage("Server byl úspěšně vytvořen. Níže jsou nějaké detaily, které se mohou hodit.", 'success');
    }

}
