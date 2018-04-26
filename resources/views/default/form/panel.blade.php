
    <form {!! $attributes !!}>
        @if($disabled === true) <fieldset disabled> @endif
        @include(AdminTemplate::getViewPath('form.partials.elements'), ['items' => $items])

        <input type="hidden" name="_method" value="post" />
        <input type="hidden" name="_redirectBack" value="{{ $backUrl }}" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

        @if($disabled === true) </fieldset> @endif
        @if($buttons) {!! $buttons->render() !!} @endif

    </form>

