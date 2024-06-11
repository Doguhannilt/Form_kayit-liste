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

    // Form verilerini alma ve valide etme
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $birthDate = trim($_POST['birthDate']);
    $gender = trim($_POST['gender']);

    $errors = [];

    if (empty($firstName)) {
        $errors[] = 'İsim gerekli';
    }
    if (empty($lastName)) {
        $errors[] = 'Soyisim gerekli';
    }
    if (empty($email)) {
        $errors[] = 'E-posta gerekli';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçersiz e-posta formatı';
    } else {
        // E-posta benzersizliğini kontrol etme
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Bu e-posta zaten kayıtlı';
        }
    }
    if (empty($password)) {
        $errors[] = 'Şifre gerekli';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Şifre en az 6 karakter olmalı';
    }
    if (empty($birthDate)) {
        $errors[] = 'Doğum tarihi gerekli';
    }
    if (empty($gender)) {
        $errors[] = 'Cinsiyet seçilmeli';
    }

    // Eğer hata yoksa veritabanına ekleme
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, birth_date, gender) VALUES (:firstName, :lastName, :email, :password, :birthDate, :gender)");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':birthDate', $birthDate);
        $stmt->bindParam(':gender', $gender);
        $stmt->execute();

        echo "Kayıt başarıyla eklendi!";
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

$conn = null;
?>
