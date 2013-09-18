<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

    public function beforeRender() {
        if ($this->isAjax()) {
            $this->invalidateControl('flashMessages');
        }
    }

}
