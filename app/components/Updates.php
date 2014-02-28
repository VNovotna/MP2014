<?php

/**
 * Nette Control, allows you to check for udates
 *
 * @author viky
 */
class Updates extends Nette\Application\UI\Control {

    /** @var GameUpdateModel */
    private $gameUpdater;

    /** @var string */
    private $path;

    /** @var boolean */
    private $immediateUse = FALSE;

    /** @var \DB\ServerRepository */
    private $serverRepo;

    /** @var int */
    private $serverId;

    /**
     * @param GameUpdateModel $gameUpdater
     * @param string $path
     */
    public function __construct($gameUpdater, $path) {
        parent::__construct();
        $this->gameUpdater = $gameUpdater;
        $this->path = $path;
    }

    /**
     * call whether the downloaded file should be set as default jar
     * @param \DB\ServerRepository $serverRepo
     * @param int $serverId
     */
    public function setForImmediateUse($serverRepo, $serverId) {
        $this->immediateUse = TRUE;
        $this->serverRepo = $serverRepo;
        $this->serverId = $serverId;
    }

    /**
     * @param string $url
     * @param string $version
     */
    public function handleDownload($url, $version) {
        $filename = $this->gameUpdater->download($url, $this->path, $version);
        if ($this->immediateUse) {
            $this->serverRepo->setExecutable($this->serverId, $filename);
        }
        $this->getPresenter()->flashMessage('Staženo.', 'success');
        if ($this->getPresenter()->isAjax()) {
            $this->getPresenter()->redrawControl();
        } else {
            $this->getPresenter()->redirect('this');
        }
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Udates.latte');
        $this->template->dowJars = array_merge($this->gameUpdater->getSnapshotsJars(), $this->gameUpdater->getStableJars());
        $this->template->render();
    }

}
