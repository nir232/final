<?php
// Initializes the local SQLite database used by the Render web service.
require __DIR__ . '/config.php';

$managerUsername = getenv('MANAGER_USERNAME') ?: 'manager1';
$managerPassword = getenv('MANAGER_PASSWORD') ?: 'manager123';
$managerName = getenv('MANAGER_NAME') ?: 'Hotel Manager';
$managerEmail = getenv('MANAGER_EMAIL') ?: 'manager@example.com';
$managerPhone = getenv('MANAGER_PHONE') ?: '0000000000';

try {
    $sql = file_get_contents(__DIR__ . '/database.sql');
    if ($sql === false || !$conn->multi_query($sql)) {
        throw new RuntimeException('Database schema initialization failed.');
    }

    $check = $conn->query("SELECT id FROM users WHERE role='manager' LIMIT 1");

    if ($check->num_rows === 0) {
        $hash = password_hash($managerPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, username, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'manager')"
        );
        $stmt->bind_param('sssss', $managerName, $managerUsername, $managerEmail, $managerPhone, $hash);
        $stmt->execute();
        echo "Manager account created: {$managerUsername}\n";
    } else {
        echo "Manager account already exists; no account changes made.\n";
    }

    echo "SQLite database initialization complete.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Database initialization failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
