<?php

/**
 * Description of BackupPresenter
 *
 * @author viky
 */
class BackupPresenter extends SecuredPresenter {

    /** @var ServerCommander @inject */
    public $serverCmd;

    /** @var \DB\ServerRepository @inject */
    public $serverRepo;

    /** @var BackupModel @inject */
    public $backupModel;

    /** @var SystemConfigModel @inject */
    public $configModel;

    /** @var string */
    private $path;

    protected function startup() {
        parent::startup();
        $this->path = $this->serverRepo->getPath($this->selectedServerId);
        if (!$this->user->isAllowed('backup')) {
            $this->flashMessage('Nemáte oprávnění pro přístup!', 'error');
            $this->redirect('Homepage:');
        }
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentLoadBackup() {
        $form = new \Nette\Application\UI\Form();
        $form->addUpload('upload', 'Zip archiv se zálohou:')
                ->addRule(\Nette\Application\UI\Form::MIME_TYPE, "Soubor musí být zip archiv", "application/zip");
        $form->addSubmit('send', 'Nahrát');
        $form->onSuccess[] = $this->loadBackupSubmitted;
        $form->onError[] = function($form) {
            foreach ($form->errors as $er) {
                $form->getPresenter()->flashMessage($er, 'error');
            }
            $form->cleanErrors();
        };
        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     */
    public function loadBackupSubmitted($form) {
        $values = $form->getValues();
        $archive = $values->upload;
        if ($archive->isOK()) {
            $archive->move($this->path . 'backups/' . $archive->getName());
            $this->flashMessage('Archiv nahrán', 'success');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleMakeBackup() {
        $path = $this->serverRepo->getPath($this->selectedServerId);
        if ($this->backupModel->backup($path, $this->runtimeHash)) {
            $this->flashMessage('Záloha úspěšná', 'success');
        } else {
            $this->flashMessage('Něco se nepovedlo :/', 'error');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleRestoreBackup($file) {
        $executable = $this->serverRepo->getRunParams($this->selectedServerId)->executable;
        if ($this->backupModel->restore($this->path, $file, $this->runtimeHash, $executable)) {
            $this->flashMessage('Obnova úspěšná.', 'success');
        } else {
            $this->flashMessage('Něco se nepovedlo :/ ' . $file, 'error');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    public function handleDeleteBackup($file) {
        if ($this->backupModel->removeFile($this->path, $file)) {
            $this->flashMessage('Záloha ' . $file . ' odstraněna', 'success');
        } else {
            $this->flashMessage('Něco se nepovedlo :/ ', 'error');
        }
        if ($this->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    /**
     * @param string $file filename
     */
    public function handleDownload($file) {
        $fd = new FileDownload();
        $fd->sourceFile = $this->path . 'backups/' . $file;
        $fd->onBeforeDownloaderStarts[] = function($fd) {
            header('Content-Length: ' . $fd->sourceFileSize);
        };
        $fd->download();
    }

    public function renderDefault() {
        $this->template->backups = $this->backupModel->getBackups($this->path);
        $this->template->couldMakeNew = $this->configModel['backup']['number'] >= count($this->template->backups);
        $this->template->foreign = $this->configModel['backup']['foreign'];
    }

}
