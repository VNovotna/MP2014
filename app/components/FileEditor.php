<?php

use Nette\Application\UI\Form;

/**
 * Nette Control, allows you to view and edit files
 *
 * @author viky
 */
class FileEditor extends Nette\Application\UI\Control {

    /** @var string */
    private $filePath;

    /** @var FileModel */
    private $fileModel;

    /** @var \Nette\Security\User */
    private $user;

    /**
     * @param string $filePath
     * @param FileModel $fileModel
     * @param \Nette\Security\User user
     */
    public function __construct($filePath, $fileModel, $user) {
        parent::__construct();
        $this->filePath = $filePath;
        $this->fileModel = $fileModel;
        $this->user = $user;
    }

    /**
     * @return Nette\Application\UI\Form
     */
    protected function createComponentServerProps() {
        $form = new Form();
        $form->addTextArea('props', '', 70, 36);
        $form->addSubmit('submit', 'Nastavit')->setAttribute('class', 'ajax');
        $value = $this->fileModel->open($this->filePath, TRUE);
        $form->setValues(array('props' => implode('', $value)));
        if (!$this->user->isAllowed('server-settings', 'edit')) {
            $form['submit']->setDisabled();
        }
        $form->onSuccess[] = $this->serverPropsFormSubmitted;
        return $form;
    }

    /**
     * 
     * @param \Nette\Application\UI\Form $form
     */
    public function serverPropsFormSubmitted(Form $form) {
        if ($this->user->isAllowed('server-settings', 'edit')) {
            $content = $form->getValues()->props;
            $this->fileModel->write($content, $this->filePath);
            $this->flashMessage('Nastavení aktualizováno.', 'success');
        } else {
            $this->flashMessage('Nemáte právo editovat nastavení.', 'error');
        }
        if ($this->isAjax()) {
            $this->invalidateControl();
        } else {
            $this->redirect('this');
        }
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/FileEditor.latte');
        $this->template->render();
    }

}
