<?php
require_once '../includes/config.php';
requireLogin();
if (isAdmin()) { header('Location: ../dashboard_admin.php'); exit; }

$db  = getDB();
$uid = $_SESSION['user_id'];
$pageTitle = 'Nouveau rapport';
$msg = '';

// Créer un brouillon de rapport si pas encore fait
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_rapport'])) {
    $titre    = sanitize($_POST['titre'] ?? '');
    $tf       = sanitize($_POST['titre_forestier'] ?? $_SESSION['titre_forestier'] ?? '');
    $aac      = sanitize($_POST['aac'] ?? $_SESSION['aac'] ?? '');
    $date_r   = sanitize($_POST['date_rapport'] ?? date('Y-m-d'));
    $stmt = $db->prepare("INSERT INTO rapports (controleur_id, titre, titre_forestier, aac, date_rapport, statut) VALUES (?,?,?,?,?,'brouillon')");
    $stmt->bind_param('issss', $uid, $titre, $tf, $aac, $date_r);
    $stmt->execute();
    $rapport_id = $db->insert_id;
    header("Location: rapport_edit.php?id=$rapport_id");
    exit;
}

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">➕ Nouveau rapport</h1>
        <div class="breadcrumb"><a href="../dashboard_controleur.php">Tableau de bord</a> › Nouveau rapport</div>
    </div>
</div>

<div class="card fade-in" style="max-width:600px;">
    <div class="card-header">
        <span class="card-title">📋 Informations générales du rapport</span>
    </div>
    <div class="card-body">
        <p style="color:var(--gris-texte);margin-bottom:20px;font-size:0.88rem;">
            Un rapport regroupe toutes vos fiches d'évaluation. Remplissez d'abord les informations générales, puis accédez à chaque fiche individuellement.
        </p>
        <form method="POST">
            <input type="hidden" name="create_rapport" value="1"/>
            <div class="form-group">
                <label>Titre du rapport</label>
                <input type="text" name="titre" class="form-control" placeholder="Ex: Contrôle AAC 6.1 - Mai 2026" required/>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Titre forestier</label>
                    <input type="text" name="titre_forestier" class="form-control" value="<?= htmlspecialchars($_SESSION['titre_forestier'] ?? '') ?>" placeholder="Ex: 09023"/>
                </div>
                <div class="form-group">
                    <label>AAC</label>
                    <input type="text" name="aac" class="form-control" value="<?= htmlspecialchars($_SESSION['aac'] ?? '') ?>" placeholder="Ex: 6.1"/>
                </div>
            </div>
            <div class="form-group">
                <label>Date du rapport</label>
                <input type="date" name="date_rapport" class="form-control" value="<?= date('Y-m-d') ?>" required/>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">🚀 Créer et remplir les fiches</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
