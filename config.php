<?php

// "sqlite:db.sqlite" could be used as weel
$db_dsn = (string)getenv('SHORTY_DB_DSN', 'mysql:dbname=shorty;host=localhost');
$db_user = (string)getenv('SHORTY_DB_USER', 'shorty');
$db_pass = (string)getenv('SHORTY_DB_PASS', '');

$connection = new PDO($db_dsn, $db_user, $db_pass);
// Consider using the local file as a SQLite backend if it exists
if (strpos($db_dsn, 'sqlite') === 0) {
    if (!is_writable(__DIR__)) {
        throw new Exception("No write access to database.");
    }
    if (! (function_exists('sqlite_open') || class_exists('SQLite3') || extension_loaded('sqlite3'))) {
        throw new Exception("database drive not installed.");
    }

    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->query(<<<EOF
CREATE TABLE IF NOT EXISTS urls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url VARCHAR(1000) NOT NULL,
    created DATETIME NOT NULL,
    accessed DATETIME,
    hits INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE (url)
);
EOF
    );
}

// Hostname for your URL shortener
$hostname = (string)getenv('SHORTY_HOSTNAME', $_SERVER['SERVER_NAME']);

// A password used for editing (to pass as $_GET['password'])
$password = (string)getenv('SHORTY_PASSWORD', '');

// Whether to track (use 0 or 1 if using environment)
$track = (bool)getenv('SHORTY_TRACK', true);

// Choose your character set (default)
$chars = (string)getenv('SHORTY_CHARS', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

// The following are shuffled strings of the default character set.
// You can uncomment one of the lines below to use a pre-generated set,
// or you can generate your own using the PHP str_shuffle function.
// Using shuffled characters will ensure your generated URLs are unique
// to your installation and are harder to guess.

// $chars = 'XPzSI6v5DqLuBtVWQARy2mfwkC14F8HUTOG0aJiYpNrl9Zxgbd3Khsno7jMeEc';
// $chars = 'PAC3mfIazxgF1lVK4wJ2WEHY0dcb87TrsZeBpL9vNUMGktROijnSoq5DX6yQhu';
// $chars = 'zFr7ALOJnGRxtKSs0oQT5NeZjdI1iX8DM2lHaCVyg4mUPp63BkEubc9qWfhwYv';
// $chars = 'u7oIws3pVWZMQjA4XhNtyvglkEer1C2J5YdT6zLiFm0ObPc8S9KaDHqRBnfUGx';
// $chars = 'gZ6hdO59XTJmUP31YMG7FvQyqjlKkf8zwitx0AcupDVs2RWCIBaNreob4nLHES';

// If you want your generated URLs to even harder to guess, you can set
// the salt value below to any non empty value. This is especially useful for
// encoding consecutive numbers.
$salt = (string)getenv('SHORTY_SALT', '');

// The padding length to use when the salt value is configured above.
$padding = (int)getenv('SHORTY_PADDING', 3);
