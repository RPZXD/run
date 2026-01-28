<?php
class Database
{
    private $host = 'localhost';
    private $db_name = 'phichai_run_2026';
    // default/primary credentials (kept for backwards compatibility)
    private $defaultUsername = 'phichairun2026';
    private $defaultPassword = '%cRWTxa@q4n94dhj';
    private $conn;

    /**
     * Try to connect using multiple credential sets and return the first successful PDO connection.
     * This allows supporting both the old 'root' account and the provided production account.
     */
    public function connect()
    {
        $this->conn = null;

        $credentialSets = [
            [
                'username' => $this->defaultUsername,
                'password' => $this->defaultPassword,
            ],
            // alternative credential provided
            [
                'username' => 'root',
                'password' => '', // ใส่รหัสผ่าน Database ของคุณที่นี่
            ],
        ];

        $lastException = null;

        foreach ($credentialSets as $creds) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
                $pdo = new PDO($dsn, $creds['username'], $creds['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                // successful connection
                $this->conn = $pdo;
                return $this->conn;
            } catch (PDOException $e) {
                // store last exception and try next credentials
                $lastException = $e;
            }
        }

        // if we reach here, none of the credentials worked
        if ($lastException) {
            // avoid exposing credentials; show generic message
            echo "Connection Error: Unable to connect to database. Please check configuration.";
        }

        return $this->conn;
    }
}
?>