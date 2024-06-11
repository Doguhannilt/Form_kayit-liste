<?php
// Veritabanı bağlantı ayarları
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_database";

try {
    // PDO ile veritabanına bağlanma
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kullanıcıları seçme
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users");
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<h2>Kayıtlı Kullanıcılar</h2>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>İsim</th><th>Soyisim</th><th>E-posta</th></tr>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Kayıtlı kullanıcı bulunamadı.";
    }
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

$conn = null;
?>
