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

    /** @var array */
    private $unchangeableLines;

    /**
     * @param string $filePath
     * @param FileModel $fileModel
     * @param bool $allowedToEdit
     * @param array $unchangeableLines format: array('patern'=>'replacement', 'patern'=>'replacement'); passed to preg_replace
     */
    public function __construct($filePath, $fileModel, $allowedToEdit, $unchangeableLines = array()) {
        parent::__construct();
        $this->filePath = $filePath;
        $this->fileModel = $fileModel;
        $this->allowedToEdit = $allowedToEdit;
        $this->unchangeableLines = $unchangeableLines;
    }

    /**
     * @return Nette\Application\UI\Form
     */
    protected function createComponentServerProps() {
        $form = new Form();
        $form->addTextArea('props', '', 70, 36);
        $form->addSubmit('submit', 'Nastavit')->setAttribute('class', 'ajax');
        try {
            $value = $this->fileModel->open($this->filePath, FALSE);
            $form->setValues(array('props' => implode('', $value)));
        } catch (\Nette\FileNotFoundException $e) {
            $form->setValues(array('props' => 'Soubor ještě nebyl vytvořen!'));
            $form['submit']->setDisabled();
        }
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
            $data = $this->fileModel->checkUnchangeableLines($content, $this->unchangeableLines);
            $this->fileModel->write($data, $this->filePath);
            $this->getPresenter()->flashMessage('Nastavení aktualizováno.', 'success');
        } else {
            $this->flashMessage('Nemáte právo editovat nastavení.', 'error');
        }
        if ($this->getPresenter()->isAjax()) {
            $this->getPresenter()->redrawControl();
        } else {
            $this->getPresenter()->redirect('this');
        }
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/FileEditor.latte');
        $this->template->render();
    }

}
