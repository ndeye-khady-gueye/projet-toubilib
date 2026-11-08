<?php

use Psr\Container\ContainerInterface;

return [
    'toubilib.pdo' => function (ContainerInterface $c) {
        $dsn = sprintf(
            "%s:host=%s;dbname=%s",
            $_ENV['DRIVER'] ?? 'pgsql',
            $_ENV['HOST'] ?? 'toubilib.db',
            $_ENV['DATABASE'] ?? 'toubilib'
        );
        $user = $_ENV['USERNAME'] ?? 'toubilib';
        $password = $_ENV['PASSWORD'] ?? 'toubilib';

        return new \PDO($dsn, $user, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    },
];