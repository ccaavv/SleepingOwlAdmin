<?php

namespace SleepingOwl\Admin\Factories;

use SleepingOwl\Admin\AliasBinder;
use SleepingOwl\Admin\Display\Column\Editable\Text;
use SleepingOwl\Admin\Display\Column\Editable\Select;
use SleepingOwl\Admin\Display\Column\Editable\Checkbox;
use SleepingOwl\Admin\Display\Column\Editable\CheckboxSwitcher;
use SleepingOwl\Admin\Contracts\Display\DisplayColumnEditableFactoryInterface;

/**
 * @method Checkbox checkbox($name)
 * @method CheckboxSwitcher switcher($name)
 */
class DisplayColumnEditableFactory extends AliasBinder implements DisplayColumnEditableFactoryInterface
{
    /**
     * DisplayColumnEditableFactory constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $application
     */
    public function __construct(\Illuminate\Contracts\Foundation\Application $application)
    {
        parent::__construct($application);
        $this->register([
            'switcher' => CheckboxSwitcher::class,
            'checkbox' => Checkbox::class,
            'select'   => Select::class,
            'text'     => Text::class,
        ]);
    }
}
