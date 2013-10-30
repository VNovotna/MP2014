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
    
    /** @var bool */
    private $allowedToEdit;

    /**
     * @param string $filePath
     * @param FileModel $fileModel
     * @param bool $allowedToEdit
     */
    public function __construct($filePath, $fileModel, $allowedToEdit) {
        parent::__construct();
        $this->filePath = $filePath;
        $this->fileModel = $fileModel;
        $this->allowedToEdit = $allowedToEdit;
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
        if (!$this->allowedToEdit) {
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
        if ($this->allowedToEdit) {
            $content = $form->getValues()->props;
            $this->fileModel->write($content, $this->filePath);
            $this->getPresenter()->flashMessage('Nastavení aktualizováno.', 'success');
        } else {
            $this->flashMessage('Nemáte právo editovat nastavení.', 'error');
        }
        if ($this->getPresenter()->isAjax()) {
            $this->getPresenter()->invalidateControl();
        } else {
            $this->getPresenter()->redirect('this');
        }
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/FileEditor.latte');
        $this->template->render();
    }

}
