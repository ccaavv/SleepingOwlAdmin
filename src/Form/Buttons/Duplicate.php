<?php

	namespace SleepingOwl\Admin\Form\Buttons;

	/**
	 * Class Duplicate.
	 */
	class Duplicate extends FormButton
	{
		protected $show = true;
		protected $view = 'form.link-button';
		protected $name = 'duplicate';
		protected $iconClass = 'fa-clone';

		public function __construct()
		{
			$this->setText(trans('sleeping_owl::lang.table.duplicate'));
		}

		/**
		 * Init Cancel Button.
		 */
		public function initialize()
		{
			parent::initialize();
			$this->setHtmlAttributes([
				'name' => 'duplicate',
				'class' => 'btn btn-warning'
			]);
			$this->setUrl($this->getModelConfiguration()->getDuplicateUrl($this->getModel()->getKey()));
		}

		/**
		 * Show policy.
		 *
		 * @return bool
		 */
		public function canShow()
		{
			if (is_null($this->getModel()->getKey()) || !$this->show) {
				$this->show = false;

				return false;
			}
			$this->show = $this->getModelConfiguration()->isDuplicatable($this->getModel());
			parent::canShow();
		}
	}
