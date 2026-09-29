<?php
session_start();

/**
 * SQLite-backed database adapter.
 *
 * The application originally used MySQLi. This small compatibility layer keeps
 * the existing PHP pages and their SQL calls working while Render runs a
 * single PHP/Apache service with a local SQLite database file.
 */

class SQLiteResult
{
    private array $rows;
    private int $position = 0;

    public int $num_rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc(): ?array
    {
        if ($this->position >= count($this->rows)) {
            return null;
        }
        return $this->rows[$this->position++];
    }
}

class SQLiteStatement
{
    private PDOStatement $statement;
    private array $params = [];

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function bind_param(string $types, ...$values): bool
    {
        $this->params = array_values($values);
        return true;
    }

    public function execute(): bool
    {
        return $this->statement->execute($this->params);
    }

    public function get_result(): SQLiteResult
    {
        return new SQLiteResult($this->statement->fetchAll(PDO::FETCH_ASSOC));
    }
}

class SQLiteConnection
{
    private PDO $pdo;

    public function __construct(string $path)
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    public function prepare(string $sql): SQLiteStatement
    {
        return new SQLiteStatement($this->pdo->prepare($this->normalizeSql($sql)));
    }

    public function query(string $sql): SQLiteResult
    {
        $statement = $this->pdo->query($this->normalizeSql($sql));
        return new SQLiteResult($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function multi_query(string $sql): bool
    {
        // The schema file contains ordinary CREATE/INSERT statements only.
        foreach ($this->splitSqlStatements($sql) as $statement) {
            $this->pdo->exec($this->normalizeSql($statement));
        }
        return true;
    }

    public function set_charset(string $charset): bool
    {
        return true;
    }

    public function close(): void
    {
        unset($this->pdo);
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    private function normalizeSql(string $sql): string
    {
        // SQLite equivalent of MySQL's INSERT IGNORE.
        $sql = preg_replace('/^\s*INSERT\s+IGNORE\s+/i', 'INSERT OR IGNORE ', trim($sql));
        return $sql;
    }

    private function splitSqlStatements(string $sql): array
    {
        $sql = preg_replace('/^\s*(CREATE\s+DATABASE.*?;|USE\s+[^;]+;)/ims', '', $sql);
        $parts = preg_split('/;\s*(?:\R|$)/', trim($sql));
        return array_values(array_filter(array_map('trim', $parts), static fn($part) => $part !== ''));
    }
}

$dbPath = getenv('DB_PATH') ?: (__DIR__ . '/data/hotel_booking.sqlite');

try {
    $conn = new SQLiteConnection($dbPath);
} catch (Throwable $e) {
    http_response_code(500);
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}
