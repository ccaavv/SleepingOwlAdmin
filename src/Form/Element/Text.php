<?php

namespace SleepingOwl\Admin\Form\Element;

class Text extends NamedFormElement
{
    public function __construct($path, $label = null)
    {
        parent::__construct($path, $label);

        $this->setHtmlAttributes([
            'class' => 'form-control',
            'type'  => 'text',
        ]);
    }

    /**
     * @var string
     */
    protected $view = 'form.element.text';

	public function min($min, $message = null)
	{
		$this->addValidationRule('min:' . $min, $message);

		return $this;
	}
}
