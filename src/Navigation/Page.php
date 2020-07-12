<?php

namespace SleepingOwl\Admin\Navigation;

use SleepingOwl\Admin\Contracts\Navigation\PageInterface;
use SleepingOwl\Admin\Contracts\ModelConfigurationInterface;

class Page extends \KodiComponents\Navigation\Page implements PageInterface
{
    /**
     * Menu item related model class.
     *
     * @var string
     */
    protected $model;

    /**
     * Menu item by url id.
     *
     * @var string
     */
    protected $aliasId;

    public $guardedActions = [
        'display' => 'Список/просмотр',
        'edit'    => 'Редактирование',
        'create'  => 'Создание',
        'delete'  => 'Удаление',
    ];

    /**
     * @param string|null $modelClass
     */
    public function __construct($modelClass = null)
    {
        parent::__construct();
        $this->setModel($modelClass);
        if ($this->hasModel()) {
            $this->setIcon($this->getModelConfiguration()->getIcon());
        }
    }

    /**
     * @return @array
     */
    public function getGuardedActions()
    {
        return $this->guardedActions;
    }

    /**
     * @return @array
     */
    public function setGuardedActions($list)
    {
        if (is_array($list)) {
            foreach ($list as $k => $v) {
                if (!isset($this->guardedActions[$k]))
                    $this->guardedActions[$k] = $v;
            }
        }
        return $this;
    }

    /**
     * Set Alias Id.
     */
    public function setAliasId()
    {
        $url = parse_url($this->getUrl(), PHP_URL_PATH);
        if ($url) {
            $this->aliasId = md5($url);
        }
    }

    /**
     * @return string
     */
    public function getAliasId()
    {
        return $this->aliasId;
    }

    /**
     * @return ModelConfigurationInterface
     */
    public function getModelConfiguration()
    {
        if (!$this->hasModel()) {
            return;
        }

        return app('sleeping_owl')->getModel($this->model);
    }

    /**
     * @return bool
     */
    public function hasModel()
    {
        return !is_null($this->model) and class_exists($this->model);
    }

    /**
     * @return string
     */
    public function getId()
    {
        if (is_null($this->id) and $this->hasModel()) {
            return $this->model;
        }

        return parent::getId();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if (is_null($this->title) and $this->hasModel()) {
            return $this->getModelConfiguration()->getTitle();
        }

        return parent::getTitle();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (is_null($this->url) and $this->hasModel()) {
            return $this->getModelConfiguration()->getDisplayUrl();
        }

        return parent::getUrl();
    }

    /**
     * @return \Closure
     */
    public function getAccessLogic()
    {
        if (!is_callable($this->accessLogic)) {
            if ($this->hasModel()) {
                return function () {
                    return $this->getModelConfiguration()->isDisplayable();
                };
            }
        }

        return parent::getAccessLogic();
    }

    /**
     * @param string|null $view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render($view = null)
    {
        $data = $this->toArray();
        if (!is_null($view)) {
            return view($view, $data)->render();
        }

        return app('sleeping_owl.template')->view('_partials.navigation.page', $data)->render();
    }

    /**
     * @param string $model
     *
     * @return $this
     */
    protected function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->isActive() and !$this->hasClassProperty($class = config('navigation.class.active', 'active'))) {
            $this->setHtmlAttribute('class', $class);
        }

        if ($this->hasChild() and !$this->hasClassProperty($class = config('navigation.class.has_child', 'has-child'))) {
            $this->setHtmlAttribute('class', $class);
            $this->setHtmlAttribute('class', 'treeview');
        }

        return parent::toArray();
    }
}
