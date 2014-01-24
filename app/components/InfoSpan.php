<?php

/**
 * It's for adding info spans to forms.
 * NOT WORKING
 * @author viky
 */
class InfoSpan extends \Nette\Application\UI\Control {

    /** @var string */
    private $text;

    /** @var string */
    private $iconClass;

    /**
     * @param string $text
     * @param string $iconClass
     */
    public function __construct($text, $iconClass) {
        parent::__construct();
        $this->text = $text;
        $this->iconClass = $iconClass;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/InfoSpan.latte');
        $this->template->text = $this->text;
        $this->template->iconClass = $this->iconClass;
        $this->template->render();
    }

}
