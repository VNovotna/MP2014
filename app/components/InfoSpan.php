<?php

/**
 * It's for adding info spans to forms.
 * 
 * @author viky
 */
class InfoSpan extends Nette\Forms\Controls\BaseControl {

    /** @var string */
    private $text;

    /** @var string */
    private $iconClass;

    public function __construct($label = NULL, $text = NULL, $iconClass = NULL) {
        parent::__construct($label);
        $this->text = $text;
        $this->iconClass = $iconClass;
    }

    public function setValue($value) {
        $this->text = $value;
    }

    public function getValue() {
        return $this->text;
    }

    public function getControl() {
        $this->setOption('rendered', TRUE);
        return Nette\Utils\Html::el()->add("<span class='$this->iconClass'>$this->text</span>");
    }

}
