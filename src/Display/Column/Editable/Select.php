<?php

namespace SleepingOwl\Admin\Display\Column\Editable;


use Illuminate\Support\Collection;
use SleepingOwl\Admin\Form\FormDefault;
use SleepingOwl\Admin\Display\Column\NamedColumn;
use SleepingOwl\Admin\Contracts\Display\ColumnEditableInterface;

class Select extends NamedColumn implements ColumnEditableInterface
{
    /**
     * @var string
     */
    protected $view = 'column.editable.select';

    protected $options = [];

    /**
     * @var string
     */
    protected $url = null;

    /**
     * Checkbox constructor.
     *
     * @param             $name
     * @param string|null $checkedLabel
     * @param string|null $uncheckedLabel
     */
    public function __construct($name, $options = [])
    {
        parent::__construct($name);
        if ($options) {
            if (is_array($options)) {
                $this->options = $options;
            } elseif ($options instanceof Collection) {
                $this->options = $options->map(function ($item) {
                    return ['id' => $item->id, 'name' => $item->name];
                });
            }
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!$this->url) {
            return request()->url();
        }

        return $this->url;
    }

    /**
     * @param $url
     * @return string
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return parent::toArray() + [
                'id'         => $this->getModel()->getKey(),
                'value'      => $this->getModelValue(),
                'isEditable' => $this->getModelConfiguration()->isEditable($this->getModel()),
                'options'    => $this->options,
                'url'        => $this->getUrl(),
            ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function save(\Illuminate\Http\Request $request)
    {
        $form = new FormDefault([
            new \SleepingOwl\Admin\Form\Element\Select(
                $this->getName()
            ),
        ]);
        $model = $this->getModel();
        $request->offsetSet($this->getName(), $request->input('value', null));
        $form->setModelClass(get_class($model));
        $form->initialize();
        $form->setId($model->getKey());
        $form->saveForm($request);
    }
}
