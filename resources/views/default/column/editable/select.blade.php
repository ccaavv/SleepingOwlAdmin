@if($isEditable)

    <select class="editable-select form-control input-sm"
            data-name="{{ $name }}"
            data-value="{{ $value }}"
            data-url="{{ $url }}"
            data-type="select"
            data-pk="{{ $id }}">
        @foreach($options as $option)
            <option value="{{ $option['id'] }}" @if($value == $option['id']) selected @endif>{{ $option['name'] }}
            </option>
        @endforeach
    </select>

@else
    @if($value) {{ $value }} @endif
@endif

{!! $append !!}
