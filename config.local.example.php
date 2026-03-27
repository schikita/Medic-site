<?php

/**
 * Скопируйте в config.local.php и задайте хеш пароля:
 * php -r "echo password_hash('ВАШ_ПАРОЛЬ', PASSWORD_DEFAULT), PHP_EOL;"
 */
return [
    'password_hash' => '',
    /*
     * Контент сайта: при наличии расширения PHP pdo_sqlite создаётся data/site.sqlite
     * (первый заход на сайт / админку). Копия в data/site.json обновляется при сохранении в админке.
     * Медиа: public/uploads/… + записи в таблице media.
     */
];
