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
     * @param string $url
     * @param string $version
     */
    public function handleDownload($url, $version) {
        $this->gameUpdater->download($url, $this->path, $version);
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
