<?php
// ============================================================
// CUF DataForest - Configuration base de données
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // Modifier selon votre config XAMPP
define('DB_PASS', '');             // Modifier selon votre config XAMPP
define('DB_NAME', 'cuf_dataforest');
define('SITE_NAME', 'CUF DataForest');
define('SITE_URL', 'http://localhost/cuf_dataforest');

function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('Erreur de connexion à la base de données : ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
}

function isControleur() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'controleur';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/dashboard_controleur.php');
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function getNbNotifications($user_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND lu=0");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($nb);
    $stmt->fetch();
    return $nb;
}
?>
