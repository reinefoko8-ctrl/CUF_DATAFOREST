<?php
// includes/header.php
$nb_notif = isLoggedIn() ? getNbNotifications($_SESSION['user_id']) : 0;
$initiales = '';
if (isLoggedIn()) {
    $initiales = strtoupper(substr($_SESSION['prenom'] ?? 'U', 0, 1) . substr($_SESSION['nom'] ?? '', 0, 1));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?? 'CUF DataForest' ?> — CUF DataForest</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css" />
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/images/logo.png" />
</head>
<body>

<nav class="navbar">
    <a href="<?= SITE_URL ?>/<?= isAdmin() ? 'dashboard_admin.php' : 'dashboard_controleur.php' ?>" class="navbar-brand">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF" class="navbar-logo" />
        <span class="navbar-title">CUF <span>DataForest</span></span>
    </a>

    <div class="navbar-nav">
        <?php if (isAdmin()): ?>
            <a href="<?= SITE_URL ?>/dashboard_admin.php" class="nav-link">🏠 Tableau de bord</a>
            <a href="<?= SITE_URL ?>/pages/rapports_admin.php" class="nav-link">📋 Rapports</a>
            <a href="<?= SITE_URL ?>/pages/utilisateurs.php" class="nav-link">👥 Utilisateurs</a>
            <a href="<?= SITE_URL ?>/pages/statistiques.php" class="nav-link">📊 Statistiques</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/dashboard_controleur.php" class="nav-link">🏠 Tableau de bord</a>
            <a href="<?= SITE_URL ?>/pages/mes_rapports.php" class="nav-link">📋 Mes rapports</a>
            <a href="<?= SITE_URL ?>/pages/nouveau_rapport.php" class="nav-link">➕ Nouveau rapport</a>
        <?php endif; ?>
        <a href="<?= SITE_URL ?>/pages/notifications.php" class="nav-link">
            🔔 Notifications
            <?php if ($nb_notif > 0): ?>
                <span class="nav-badge"><?= $nb_notif ?></span>
            <?php endif; ?>
        </a>
    </div>

    <div class="navbar-user">
        <div class="user-avatar"><?= $initiales ?></div>
        <span><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></span>
        <a href="<?= SITE_URL ?>/logout.php" class="btn btn-sm" style="background:rgba(255,255,255,0.18);color:white;margin-left:8px;">Déconnexion</a>
    </div>
</nav>

<div class="layout">
<!-- SIDEBAR -->
<aside class="sidebar">
    <?php if (isAdmin()): ?>
    <div class="sidebar-section">
        <div class="sidebar-label">Administration</div>
        <a href="<?= SITE_URL ?>/dashboard_admin.php" class="sidebar-link"><span class="icon">🏠</span> Tableau de bord</a>
        <a href="<?= SITE_URL ?>/pages/rapports_admin.php" class="sidebar-link"><span class="icon">📋</span> Tous les rapports</a>
        <a href="<?= SITE_URL ?>/pages/utilisateurs.php" class="sidebar-link"><span class="icon">👥</span> Gestion utilisateurs</a>
        <a href="<?= SITE_URL ?>/pages/statistiques.php" class="sidebar-link"><span class="icon">📊</span> Statistiques</a>
        <a href="<?= SITE_URL ?>/pages/export_global.php" class="sidebar-link"><span class="icon">📥</span> Export global</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-label">Outils</div>
        <a href="<?= SITE_URL ?>/pages/notifications.php" class="sidebar-link"><span class="icon">🔔</span> Notifications <?php if ($nb_notif > 0) echo "<span class='nav-badge'>$nb_notif</span>"; ?></a>
        <a href="<?= SITE_URL ?>/pages/profil.php" class="sidebar-link"><span class="icon">👤</span> Mon profil</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><span class="icon">🚪</span> Déconnexion</a>
    </div>
    <?php else: ?>
    <div class="sidebar-section">
        <div class="sidebar-label">Mes activités</div>
        <a href="<?= SITE_URL ?>/dashboard_controleur.php" class="sidebar-link"><span class="icon">🏠</span> Tableau de bord</a>
        <a href="<?= SITE_URL ?>/pages/nouveau_rapport.php" class="sidebar-link"><span class="icon">➕</span> Nouveau rapport</a>
        <a href="<?= SITE_URL ?>/pages/mes_rapports.php" class="sidebar-link"><span class="icon">📋</span> Mes rapports</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-label">Fiches</div>
        <a href="<?= SITE_URL ?>/pages/fiche_parc_foret.php" class="sidebar-link"><span class="icon">🌳</span> Parc forêt</a>
        <a href="<?= SITE_URL ?>/pages/fiche_abattage.php" class="sidebar-link"><span class="icon">🪓</span> Abattage contrôlé</a>
        <a href="<?= SITE_URL ?>/pages/fiche_routes.php" class="sidebar-link"><span class="icon">🛤️</span> Routes forestières</a>
        <a href="<?= SITE_URL ?>/pages/fiche_tracabilite.php" class="sidebar-link"><span class="icon">📦</span> Traçabilité grumes</a>
        <a href="<?= SITE_URL ?>/pages/fiche_securite_tc.php" class="sidebar-link"><span class="icon">⚙️</span> Sécurité tronçonneuses</a>
        <a href="<?= SITE_URL ?>/pages/fiche_sortie_pieds.php" class="sidebar-link"><span class="icon">👣</span> Sortie pieds</a>
        <a href="<?= SITE_URL ?>/pages/fiche_debardage.php" class="sidebar-link"><span class="icon">🚜</span> Débardage</a>
        <a href="<?= SITE_URL ?>/pages/fiche_pont.php" class="sidebar-link"><span class="icon">🌉</span> Pont forestier</a>
        <a href="<?= SITE_URL ?>/pages/fiche_post_exploitation.php" class="sidebar-link"><span class="icon">🔄</span> Post exploitation</a>
        <a href="<?= SITE_URL ?>/pages/fiche_dechets.php" class="sidebar-link"><span class="icon">♻️</span> Déchets en forêt</a>
        <a href="<?= SITE_URL ?>/pages/fiche_base_mecanique.php" class="sidebar-link"><span class="icon">🔧</span> Base mécanique</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-label">Compte</div>
        <a href="<?= SITE_URL ?>/pages/notifications.php" class="sidebar-link"><span class="icon">🔔</span> Notifications <?php if ($nb_notif > 0) echo "<span class='nav-badge'>$nb_notif</span>"; ?></a>
        <a href="<?= SITE_URL ?>/pages/profil.php" class="sidebar-link"><span class="icon">👤</span> Mon profil</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><span class="icon">🚪</span> Déconnexion</a>
    </div>
    <?php endif; ?>
</aside>

<!-- MAIN -->
<main class="main-content">
