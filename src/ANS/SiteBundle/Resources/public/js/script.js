$(document).ready(function() {
    $('[href="#add-comment"], [href="#edit-comment"]').on('click', function() {
        var url = $(this).data('action-url');
        var container = $(this).is('[href="#add-comment"]') ? $('.add-form') : $(this).siblings('.edit-form');

        container.text('Загрузка...');

        $.get(url, function(data) {
            container.html(data);

            container.on('submit', function() {
                var postData = container.find('form').serialize();
                container.text('Отправка...');

                $.post(url, postData, function(data) {
                    if (data === 'ok') {
                        container.text('Сообщение отправлено');
                    } else {
                        container.text('Ошибка');
                    }
                });
                return false;
            });
        });

        return false;
    });
});