<?php

/**
 * Генерация строки для config.local.php:
 * php tools/gen_password_hash.php "ваш_пароль"
 */
$pass = $argv[1] ?? '';
if ($pass === '') {
    fwrite(STDERR, "Usage: php tools/gen_password_hash.php \"password\"\n");
    exit(1);
}
echo password_hash($pass, PASSWORD_DEFAULT), PHP_EOL;
