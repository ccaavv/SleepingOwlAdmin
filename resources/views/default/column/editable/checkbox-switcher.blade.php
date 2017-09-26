@if($isEditable)

    <label class="switch">
        <input type="checkbox"
               @if($value == 1) checked @endif
               data-name="{{ $name }}"
               data-value="{{ $value }}"
               data-url="{{ $url }}"
               data-type="checklist"
               data-pk="{{ $id }}"
               data-source="{ 1 : '{{ $checkedLabel }}' }"
               data-emptytext="{{ $uncheckedLabel }}">
        <div class="slider round"></div>
    </label>

@else
    @if($value) {{ $checkedLabel }} @else {{ $uncheckedLabel }} @endif
@endif

{!! $append !!}
