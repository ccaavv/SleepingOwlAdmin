<?php

	namespace SleepingOwl\Admin\Display;

	use Request;
	use Illuminate\Support\Collection;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Pagination\LengthAwarePaginator;
	use SleepingOwl\Admin\Contracts\ModelConfigurationInterface;
	use SleepingOwl\Admin\Display\Extension\Columns;
	use SleepingOwl\Admin\Display\Extension\ColumnFilters;
	use SleepingOwl\Admin\Contracts\Display\ColumnInterface;
	use SleepingOwl\Admin\Contracts\Display\Extension\ColumnFilterInterface;

	/**
	 * Class DisplayCustom.
	 * @method Columns getColumns()
	 * @method $this setColumns(ColumnInterface | ColumnInterface [] $column)
	 *
	 * @method ColumnFilters getColumnFilters()
	 * @method $this setColumnFilters(ColumnFilterInterface $filters = null, ...$filters)
	 */
	class DisplayCustom extends Display
	{
		/**
		 * @var string
		 */
		protected $view = 'display.custom';

		/**
		 * @var array
		 */
		protected $parameters = [];

		/**
		 * @var int|null
		 */
		protected $paginate = 25;

		/**
		 * @var string
		 */
		protected $pageName = 'page';

		/**
		 * @var Collection
		 */
		protected $collection;

		/**
		 * @var string|null
		 */
		protected $newEntryButtonText;

		/**
		 * @var \Closure
		 */
		protected $displayCallback;

		/**
		 * @var \Closure
		 */
		protected $saveCallback;

		/**
		 * Display constructor.
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * Initialize display.
		 */
		public function initialize()
		{
			parent::initialize();
			if ($this->getModelConfiguration()->isRestorableModel()) {
				$this->setApply(function ($q) {
					return $q->withTrashed();
				});
			}
		}

		/**
		 * @return null|string
		 */
		public function getNewEntryButtonText()
		{
			if (is_null($this->newEntryButtonText)) {
				$this->newEntryButtonText = trans('sleeping_owl::lang.table.new-entry');
			}

			return $this->newEntryButtonText;
		}

		/**
		 * @param string $newEntryButtonText
		 *
		 * @return $this
		 */
		public function setNewEntryButtonText($newEntryButtonText)
		{
			$this->newEntryButtonText = $newEntryButtonText;

			return $this;
		}

		/**
		 * @param \Closure $callback
		 *
		 * @return $this
		 */
		public function setDisplay($callback)
		{
			$this->displayCallback = $callback;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDisplay()
		{
			if (is_callable($this->displayCallback)) {
				return call_user_func($this->displayCallback, $this->getModelConfiguration(), $this->getCollection());
			}

			return $this->displayCallback;
		}

		/**
		 * Get the evaluated contents of the object.
		 *
		 * @return string
		 */
		public function render()
		{
			$view = app('sleeping_owl.template')->view($this->getView(), $this->toArray());

			$blocks = $this->getExtensions()->placableBlocks();

			foreach ($blocks as $block => $data) {
				foreach ($data as $html) {
					if (! empty($html)) {
						$view->getFactory()->startSection($block);
						echo $html;
						$view->getFactory()->yieldSection();
					}
				}
			}

			return $view;
		}


		/**
		 * @param \Closure $callback
		 *
		 * @return $this
		 */
		public function setSaveCallback($callback)
		{
			$this->saveCallback = $callback;

			return $this;
		}

		/**
		 * @return \Closure
		 */
		public function getCallback()
		{
			return $this->saveCallback;
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return void
		 */
		public function save(ModelConfigurationInterface $model, \Illuminate\Http\Request $request)
		{
			$callback = $this->getCallback();
			if (is_callable($callback)) {
				call_user_func($callback, $model, $request);
			}
		}

		/**
		 * @return array
		 */
		public function getParameters()
		{
			return $this->parameters;
		}

		/**
		 * @param array $parameters
		 *
		 * @return $this
		 */
		public function setParameters($parameters)
		{
			$this->parameters = $parameters;

			return $this;
		}

		/**
		 * @param string $key
		 * @param mixed  $value
		 *
		 * @return $this
		 */
		public function setParameter($key, $value)
		{
			$this->parameters[$key] = $value;

			return $this;
		}

		/**
		 * @param int    $perPage
		 * @param string $pageName
		 *
		 * @return $this
		 */
		public function paginate($perPage = 25, $pageName = 'page')
		{
			$this->paginate = (int)$perPage;
			$this->pageName = $pageName;

			return $this;
		}

		/**
		 * @return $this
		 */
		public function disablePagination()
		{
			$this->paginate = 0;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function usePagination()
		{
			return $this->paginate > 0;
		}

		/**
		 * @return array
		 */
		public function toArray()
		{
			$model = $this->getModelConfiguration();
			$params = parent::toArray();
			$params['creatable'] = $model->isCreatable();
			$params['createUrl'] = $model->getCreateUrl($this->getParameters() + Request::all());
			$params['collection'] = $this->getCollection();
			$params['extensions'] = $this->getExtensions()->renderable()->sortByOrder();
			$params['newEntryButtonText'] = $this->getNewEntryButtonText();
			$params['content'] = $this->getDisplay();

			return $params;
		}

		/**
		 * @return Collection|LengthAwarePaginator|Builder
		 * @throws \Exception
		 */
		public function getCollection()
		{
			if (!$this->isInitialized()) {
				throw new \Exception('Display is not initialized');
			}
			if (!is_null($this->collection)) {
				return $this->collection;
			}
			$query = $this->getRepository()->getQuery();
			$this->modifyQuery($query);

			return $this->collection = $this->usePagination()
				? $query->paginate($this->paginate, ['*'], $this->pageName)->appends(request()->except($this->pageName))
				: $query->get();
		}

		/**
		 * @param \Illuminate\Database\Eloquent\Builder|Builder $query
		 */
		protected function modifyQuery(\Illuminate\Database\Eloquent\Builder $query)
		{
			$this->extensions->modifyQuery($query);
		}
	}
