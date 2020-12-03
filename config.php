<?php
// Hostname for your URL shortener
$hostname = 'http://example.com';
$local_db = __DIR__ . '/database.sqlite';

// Consider using the local file as a SQLite backend if it exists
if (
    file_exists($local_db)
    && function_exists('sqlite_open')
    && is_writable(__DIR__)
    && is_writable($local_db)
) {
    $connection = new PDO('sqlite:'.$local_db);
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
} else {
    // PDO connection a MySQL the database
    $connection = new PDO('mysql:dbname=shorty;host=localhost', 'user', 'password');
}

// A password used for editing (to pass as $_GET['password'])
define('PASSWORD', getenv('SHORTY_PASSWORD', ''));

// Whether to track
$track = getenv(SHORTY_TRACK, true);

// Choose your character set (default)
$chars = getenv(SHORTY_CHARS, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

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
$salt = getenv(SHORTY_SALT, '');

// The padding length to use when the salt value is configured above.
$padding = getenv(SHORTY_PADDING, 3);
