@foreach ($children as $entry)
	<li class="dd-item dd3-item {{ $reorderable ? '' : 'dd3-not-reorderable' }}" data-id="{{ $entry->id }}">
		@if ($reorderable)
			<div class="dd-handle dd3-handle"></div>
		@endif
		<div class="dd3-content">
			@if (is_callable($value))
				{!! $value($entry) !!}
			@else
				<?php $name = explode('.', $value); ?>
				@if(isset($name[1]))
					{{ $entry->{$name[0]}->{$name[1]} }}
				@else
					{{ $entry->{$name[0]} }}
				@endif
			@endif
			<div class="pull-right">
				@foreach ($controls as $control)

					@if($control instanceof \SleepingOwl\Admin\Contracts\Display\ColumnInterface)
						<?php $control->setModel($entry); ?><?php
						$control->initialize();
						?>
					@endif

					{!! $control->render() !!}
				@endforeach
			</div>
		</div>
		@if ($entry->children && $entry->children->count() > 0)
			<ol class="dd-list">
				@include(AdminTemplate::getViewPath('display.tree_children'), ['children' => $entry->children])
			</ol>
		@endif
	</li>
@endforeach
