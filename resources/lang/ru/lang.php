<?php

return [
    'dashboard' => 'Панель',
    '404' => 'Страница не найдена.',
    'auth' => [
        'title' => 'Авторизация',
        'username' => 'Логин',
        'password' => 'Пароль',
        'login' => 'Войти',
        'logout' => 'Выйти',
        'wrong-username' => 'Неверный логин',
        'wrong-password' => 'или пароль',
        'since' => 'Зарегистрирован :date',
    ],
    'model' => [
        'create' => 'Создание документа в разделе :title',
        'edit' => 'Редактирование записи в разделе :title',
        'view' => 'Просмотр записи в разделе :title',
    ],
    'links' => [
        'index_page' => 'На сайт',
    ],
    'ckeditor' => [
        'upload' => [
            'success' => 'Файл был успешно загружен: \\n- Размер: :size кб \\n- ширина/высота: :width x :height',
            'error' => [
                'common' => 'Возникла ошибка при загрузке файла.',
                'wrong_extension' => 'Файл ":file" имеет неверный тип.',
                'filesize_limit' => 'Максимальный размер файла :size кб.',
                'imagesize_max_limit' => 'Ширина x Высота = :width x :height \\n Максимальный размер изображение должен быть: :maxwidth x :maxheight',
                'imagesize_min_limit' => 'Ширина x Высота = :width x :height \\n Минимальный размер изображение должен быть: :minwidth x :minheight',
            ],
        ],
        'image_browser' => [
            'title' => 'Вставка изображения с сервера',
            'subtitle' => 'Выберите изображение для вставки',
        ],
    ],
    'table' => [
        'no-action' => 'Нет действия',
        'make-action' => 'Отправить',
        'new-entry' => 'Новая запись',
        'edit' => 'Редактировать',
        'restore' => 'Восстановить',
        'delete' => 'Удалить',
        'delete-confirm' => 'Вы уверены, что хотите удалить эту запись?',
        'delete-error' => 'Невозможно удалить эту запись. Необходимо предварительно удалить все связанные записи.',
        'destroy' => 'Удалить',
        'destroy-confirm' => 'Вы уверены, что хотите удалить эту запись?',
        'destroy-error' => 'Невозможно удалить эту запись. Необходимо предварительно удалить все связанные записи.',
        'moveUp' => 'Подвинуть вверх',
        'moveDown' => 'Подвинуть вниз',
        'error' => 'В процессе обработки вашего запроса возникла ошибка',
        'filter' => 'Показать подобные записи',
        'filter-goto' => 'Перейти',
        'save' => 'Сохранить',
        'save_and_close' => 'Сохранить и закрыть',
        'save_and_create' => 'Сохранить и создать',
        'cancel' => 'Отменить',
        'duplicate' => 'Дублировать',
        'download' => 'Скачать',
        'all' => 'Все',
        'processing' => '<i class="fa fa-5x fa-circle-o-notch fa-spin"></i>',
        'loadingRecords' => 'Подождите...',
        'lengthMenu' => 'Отображать _MENU_ записей',
        'zeroRecords' => 'Не найдено подходящих записей.',
        'info' => 'Записи с _START_ по _END_ из _TOTAL_',
        'infoEmpty' => 'Записи с 0 по 0 из 0',
        'infoFiltered' => '(отфильтровано из _MAX_ записей)',
        'infoThousands' => '',
        'infoPostFix' => '',
        'search' => 'Поиск: ',
        'emptyTable' => 'Нет записей',
        'paginate' => [
            'first' => 'Первая',
            'previous' => '&larr;',
            'next' => '&rarr;',
            'last' => 'Последняя',
        ],
                'filters'=>[
                    'control'=>'Фильтр',
                ],
    ],
    'tree' => [
        'expand' => 'Развернуть все',
        'collapse' => 'Свернуть все',
    ],
    'editable' => [
        'checkbox' => [
            'checked' => 'Да',
            'unchecked' => 'Нет',
        ],
    ],
    'select' => [
        'nothing' => 'Ничего не выбрано',
        'selected' => 'выбрано',
        'placeholder' => 'Выберите из списка',
    ],
    'image' => [
        'browse' => 'Выбор изображения',
        'browseMultiple' => 'Выбор изображений',
        'remove' => 'Удалить',
        'removeMultiple' => 'Удалить',
    ],
    'file' => [
        'browse' => 'Выбор файла',
        'remove' => 'Удалить',
    ],
    'button' => [
        'yes' => 'Да',
        'no' => 'Нет',
    ],
    'message' => [
        'created' => '<i class="fa fa-check fa-lg"></i> Запись успешно создана',
        'updated' => '<i class="fa fa-check fa-lg"></i> Запись успешно обновлена',
        'deleted' => '<i class="fa fa-check fa-lg"></i> Запись успешно удалена',
        'duplicated' => '<i class="fa fa-check fa-lg"></i> Запись :id успешно дублирована',
        'restored' => '<i class="fa fa-check fa-lg"></i> Запись успешно восстановлена',
        'something_went_wrong' => 'Что-то пошло не так!',
        'are_you_sure' => 'Вы уверены?',
        'access_denied' => 'Доступ запрещен',
        'validation_error' => 'Ошибка валидации',
    ],
];
