<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'                => 'Значение не принято.',
    'active_url'              => 'Адрес неверен.',
    'after'                   => 'Дата должна быть после :date.',
    'alpha'                   => 'Неверный формат.',
    'alpha_dash'              => 'Неверный формат.',
    'alpha_num'               => 'Неверный формат.',
    'array'                   => 'Должно быть массивом.',
    'before'                  => 'Дата должна быть до :date.',
    'between'                 => [
        'numeric' => 'Должно быть между :min и :max.',
        'file'    => 'Размер файла должен быть от :min до :max килобайт.',
        'string'  => 'Длина должна быть от :min до :max символов.',
        'array'   => 'Должно быть от :min до :max элементов.',
    ],
    'confirmed'               => 'Подтверждение этого поля не совпадает.',
    'date'                    => 'Неверная дата.',
    'date_format'             => 'Неверный формат.',
    'different'               => 'Поля :attribute и :other должны отличаться.',
    'digits'                  => 'Поле :attribute должно состоять из :digits цифр.',
    'digits_between'          => 'Поле :attribute должно быть от :min до :max цифр.',
    'email'                   => 'Поле :attribute должно быть корректным email адресом.',
    'exists'                  => 'Выбранное поле :attribute уже существует.',
    'image'                   => 'Должно быть выбрано изображение (jpeg, png, gif, bmp).',
    'in'                      => 'Выбранное поле :attribute не корректно.',
    'integer'                 => 'Поле :attribute должно быть целочисленным числом.',
    'ip'                      => 'Поле :attribute должно быть корректным IP адресом.',
    'max'                     => [
        'numeric' => 'Поле :attribute должно быть не больше чем :max.',
        'file'    => 'Поле :attribute должно быть не больше чем :max килобайт.',
        'string'  => 'Поле :attribute должно быть не больше чем :max символов.',
        'array'   => 'Поле :attribute должно иметь не больше чем :max элементов.',
    ],
    'mimes'                   => 'Поле :attribute должно быть файлом типа: :values.',
    'min'                     => [
        'numeric' => 'Поле :attribute должно быть минимум :min.',
        'file'    => 'Поле :attribute должно быть минимум :min килобайт.',
        'string'  => 'Поле :attribute должно быть минимум :min символов.',
        'array'   => 'Поле :attribute должно содержать минимум :min элементов.',
    ],
    'not_in'                  => 'Поле :attribute не корректно.',
    'not_php'                 => 'Неверный тип файла.',
    'numeric'                 => 'Поле :attribute должно быть числом.',
    'regex'                   => 'Неверный формат поля.',
    'required'                => 'Необходимо заполнить это поле.',
    'required_only_on_create' => 'Необходимо заполнить это поле.',
    'required_if'             => 'Поле :attribute обязательно, когда :other имеет значение :value.',
    'required_with'           => 'Поле :attribute обязательно, когда значения :values выбраны.',
    'required_with_all'       => 'Поле :attribute обязательно, когда значения :values выбраны.',
    'required_without'        => 'Поле :attribute обязательно, когда значения :values не выбраны.',
    'required_without_all'    => 'Поле :attribute обязательно, когда ни одно из значений :values не выбрано.',
    'same'                    => 'Поля :attribute и :other должны совпадать.',
    'size'                    => [
        'numeric' => 'Поле :attribute должно иметь длину :size символов.',
        'file'    => 'Поле :attribute должно содержать файл объемом :size килобайт.',
        'string'  => 'Поле :attribute должно содержать :size символов.',
        'array'   => 'Поле :attribute должно содержать :size элементов.',
    ],
    'unique'                  => 'Это поле должно быть уникальным. Подобная запись уже существует.',
    'url'                     => 'Неверный формат поля :attribute',
    'url_stub'                => 'Неверный формат поля.',
    'url_stub_full'           => 'Неверный формат поля.',
    'not_image'               => 'Файл не является изображением',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
