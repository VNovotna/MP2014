<?php

/**
 * VersionManagerPresenter manages versions of minecraft server
 *
 * @author viky
 */
class VersionManagerPresenter extends SecuredPresenter {

    /** @var GameUpdateModel */
    private $gameUpdater;

    protected function startup() {
        parent::startup();
        if (!$this->user->isAllowed('server-settings', 'edit')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
        $this->gameUpdater = $this->context->gameUpdateModel;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentUpdateForm() {
        $form = new Form();
        if ($this->user->isAllowed('server-settings', 'edit')) {
            $form->addGroup('aktualizace');
            $items = array_merge($this->gameUpdater->getSnapshotsJars(), $this->gameUpdater->getStableJars());
            dump($this->gameUpdater->getAvailableJars($this->serverRepo->getPath($this->selectedServerId)));
            $form->addSelect('version', 'Dostupné verze:', $items);
            $form->addSubmit('update', 'Aktualizovat');
            $form->onSuccess[] = $this->updateFormSubmitted;
        }
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function updateFormSubmitted(Form $form) {
        $values = $form->getValues();

        $this->flashMessage('Server byl aktualizován. Změny se projeví při přístím startu hry.', 'success');
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }
        public function renderDefault() {
        $this->template->versions = array_merge($this->gameUpdater->getSnapshotsJars(), $this->gameUpdater->getStableJars());
    }

}
