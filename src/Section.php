<?php

	namespace SleepingOwl\Admin;

	use SleepingOwl\Admin\Model\SectionModelConfiguration;

	class Section extends SectionModelConfiguration
	{
		public function can($action, \Illuminate\Database\Eloquent\Model $model)
		{
			return checkAdminAccess($this->alias . '.' . $action);
		}
	}
