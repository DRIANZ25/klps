<?php
require_once __DIR__ . '/config/config.php';

echo "<h1>Database Test</h1>";
echo "Environment: " . ENVIRONMENT . "<br>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";

try {
    $pdo = db();
    echo "✅ Connected successfully!<br><br>";
    
    // Show tables using PDO::FETCH_NUM
    $stmt = $pdo->query("SHOW TABLES");
    echo "<h3>Tables:</h3>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "<li>📄 " . $row[0] . "</li>";
    }
    echo "</ul>";
    
    // Check users table
    echo "<h3>Users:</h3>";
    $stmt = $pdo->query("SELECT id, full_name, email, role FROM users");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Full Name</th><th>Email</th><th>Role</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>