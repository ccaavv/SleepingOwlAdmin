<?php

namespace SleepingOwl\Admin\Form\Element;

class Color extends NamedFormElement
{
    public function __construct($path, $label = null)
    {
        parent::__construct($path, $label);

        $this->setHtmlAttributes([
            'class' => 'form-control',
            'type'  => 'color',
        ]);
    }

    /**
     * @var string
     */
    protected $view = 'form.element.color';
}
