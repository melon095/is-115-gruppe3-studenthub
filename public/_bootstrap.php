$database_host = "127.0.0.1";
$database_navn = "studenthub";
$database_bruker = "root";
$database_passord = "";

try {
    $pdo = new PDO(
        "mysql:host={$database_host};dbname={$database_navn};charset=utf8mb4",
        $database_bruker,
        $database_passord,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    exit("Kunne ikke koble til databasen.");
}