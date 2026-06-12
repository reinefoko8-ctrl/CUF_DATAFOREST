<?php
require_once '../includes/config.php';
requireLogin();
$db = getDB();
$uid = $_SESSION['user_id'];
$rid = (int)($_GET['rapport_id'] ?? 0);
if (!$rid) { header('Location:../dashboard_controleur.php'); exit; }

$stmt = $db->prepare("SELECT * FROM rapports WHERE id=?");
$stmt->bind_param('i', $rid);
$stmt->execute();
$rapport = $stmt->get_result()->fetch_assoc();
if (!$rapport) { header('Location:../dashboard_controleur.php'); exit; }

$existing = $db->query("SELECT * FROM fiche_debardage WHERE rapport_id=$rid")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rapport['statut'] === 'brouillon') {
    $tf  = sanitize($_POST['titre_forestier'] ?? '');
    $aac = sanitize($_POST['aac'] ?? '');
    $nc  = sanitize($_POST['nom_controleur'] ?? '');
    $dc  = sanitize($_POST['date_controle'] ?? date('Y-m-d'));
    $uc  = sanitize($_POST['uc'] ?? '');
    $c1  = sanitize($_POST['c1'] ?? '');
    $c2  = sanitize($_POST['c2'] ?? '');
    $c3  = sanitize($_POST['c3'] ?? '');
    $c4  = sanitize($_POST['c4'] ?? '');
    $c5  = sanitize($_POST['c5'] ?? '');
    $c6  = sanitize($_POST['c6'] ?? '');
    $c7  = sanitize($_POST['c7'] ?? '');
    $c8  = sanitize($_POST['c8'] ?? '');
    $c9  = sanitize($_POST['c9'] ?? '');
    $c10 = sanitize($_POST['c10'] ?? '');
    $tp  = sanitize($_POST['total_points'] ?? '0');
    $obs = sanitize($_POST['observations'] ?? '');
    $app = sanitize($_POST['appreciation'] ?? '');

    if ($existing) {
        $stmt2 = $db->prepare("UPDATE fiche_debardage SET
            titre_forestier=?, aac=?, nom_controleur=?, date_controle=?, uc=?,
            c1=?, c2=?, c3=?, c4=?, c5=?, c6=?, c7=?, c8=?, c9=?, c10=?,
            total_points=?, observations=?, appreciation=?
            WHERE rapport_id=?");
        $stmt2->bind_param('ssssssssssssssssssi',
            $tf,$aac,$nc,$dc,$uc,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tp,$obs,$app,$rid);
    } else {
        $stmt2 = $db->prepare("INSERT INTO fiche_debardage
            (rapport_id, titre_forestier, aac, nom_controleur, date_controle, uc,
             c1,c2,c3,c4,c5,c6,c7,c8,c9,c10, total_points, observations, appreciation)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt2->bind_param('issssssssssssssssss',
            $rid,$tf,$aac,$nc,$dc,$uc,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tp,$obs,$app);
    }

    if ($stmt2->execute()) {
        $msg = 'success';
        $existing = $db->query("SELECT * FROM fiche_debardage WHERE rapport_id=$rid")->fetch_assoc();
    } else {
        $msg = 'error:' . $db->error;
    }
}

$pageTitle = 'Fiche Débardage';
include '../includes/header.php';
function vdb($e, $f, $def = '') { return htmlspecialchars($e[$f] ?? $def); }
function svdb($e, $f) { return $e[$f] ?? ''; }
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🚜 Débardage</h1>
        <div class="breadcrumb">
            <a href="../dashboard_controleur.php">Tableau de bord</a> ›
            <a href="rapport_edit.php?id=<?= $rid ?>">Rapport #<?= $rid ?></a> › Débardage
        </div>
    </div>
    <a href="rapport_edit.php?id=<?= $rid ?>" class="btn btn-secondary">← Retour</a>
</div>

<?php if ($msg === 'success'): ?>
    <div class="alert alert-success">✅ Fiche enregistrée avec succès !</div>
<?php elseif (strpos($msg, 'error') === 0): ?>
    <div class="alert alert-error">❌ Erreur : <?= htmlspecialchars(substr($msg, 6)) ?></div>
<?php endif; ?>

<form method="POST" class="fiche-form-container fade-in">
<div class="fiche-form-header">
    <img src="../images/logo.png" alt="CUF"/>
    <div>
        <h2>FICHE DE CONTRÔLE — "Débardage"</h2>
        <p>Version 02, du 23/09/2025 &nbsp;|&nbsp; Rapport #<?= $rid ?></p>
    </div>
</div>
<div class="fiche-form-body">

    <div class="form-row-3 mb-3">
        <div class="form-group">
            <label>Titre forestier</label>
            <input type="text" name="titre_forestier" class="form-control"
                value="<?= vdb($existing, 'titre_forestier', $rapport['titre_forestier'] ?? '') ?>"/>
        </div>
        <div class="form-group">
            <label>AAC</label>
            <input type="text" name="aac" class="form-control"
                value="<?= vdb($existing, 'aac', $rapport['aac'] ?? '') ?>"/>
        </div>
        <div class="form-group">
            <label>Nom du contrôleur</label>
            <input type="text" name="nom_controleur" class="form-control"
                value="<?= vdb($existing, 'nom_controleur', $_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>"/>
        </div>
        <div class="form-group">
            <label>Date de contrôle</label>
            <input type="date" name="date_controle" class="form-control"
                value="<?= vdb($existing, 'date_controle', date('Y-m-d')) ?>"/>
        </div>
        <div class="form-group">
            <label>Unité de comptage (UC)</label>
            <input type="text" name="uc" class="form-control"
                value="<?= vdb($existing, 'uc') ?>"/>
        </div>
    </div>

    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Critères d'évaluation</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;">
        <strong>1</strong> = Conforme &nbsp;|&nbsp;
        <strong>0</strong> = Non conforme &nbsp;|&nbsp;
        <strong>NA</strong> = Non applicable
    </p>

    <?php $criteres = [
        ['c1',  'La piste de débardage crée respecte-t-elle le tracé initial (layons de pistage faits à la machette) ?'],
        ['c2',  'Les pistes secondaires de débardage existantes desservent-elles systématiquement un pied abattu valorisable ?'],
        ['c3',  'La jonction entre les pistes principale et secondaire forme-t-elle un angle obtu (arête de poisson) ?'],
        ['c4',  'Les cours d\'eau et les ravines sont-ils éloignés d\'au moins 30 m des pistes de débardage ?'],
        ['c5',  'Une largeur maximale de 5 m pour les pistes principales et 4 m pour les pistes secondaires est-elle respectée ?'],
        ['c6',  'Aucune double piste de débardage n\'est effective pour un pied abattu ?'],
        ['c7',  'Les tiges d\'avenir identifiées et marquées ont-elles été protégées ?'],
        ['c8',  'Le tronçonnage forêt (étêtage, éculage, division des longues grumes) est-il effectif et bien réalisé ?'],
        ['c9',  'Les purges ont-elles été réalisées sur les billes abandonnées en forêt ?'],
        ['c10', 'Tous les culées et houppiers portent-ils des N°DF10 + ligne et date d\'abattage ?'],
    ]; ?>

    <table class="criteres-table">
        <thead>
            <tr>
                <th style="width:40px;">N°</th>
                <th>Critère d'évaluation</th>
                <th style="width:160px;text-align:center;">Score</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($criteres as $i => [$key, $texte]): ?>
        <tr data-pts="1">
            <td class="critere-num"><?= $i + 1 ?></td>
            <td style="font-size:0.84rem;"><?= $texte ?></td>
            <td>
                <input type="hidden" name="<?= $key ?>" value="<?= svdb($existing, $key) ?>"/>
                <div class="critere-score">
                    <button type="button" class="score-btn <?= svdb($existing, $key) === '1'  ? 'selected-1'  : '' ?>" data-val="1">1</button>
                    <button type="button" class="score-btn <?= svdb($existing, $key) === '0'  ? 'selected-0'  : '' ?>" data-val="0">0</button>
                    <button type="button" class="score-btn <?= svdb($existing, $key) === 'NA' ? 'selected-na' : '' ?>" data-val="NA">NA</button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-bar">
        <span class="total-label">📊 Total des points :</span>
        <span class="total-value" id="total-display"><?= vdb($existing, 'total_points', '0') ?>/10</span>
    </div>
    <input type="hidden" name="total_points" id="total-hidden" value="<?= vdb($existing, 'total_points', '0') ?>"/>

    <div class="form-group mt-2">
        <label>Observations et/ou recommandations</label>
        <textarea name="observations" class="form-control" rows="4"
            placeholder="Vos observations..."><?= vdb($existing, 'observations') ?></textarea>
    </div>
    <div class="form-group">
        <label>Appréciation</label>
        <textarea name="appreciation" class="form-control" rows="2"
            placeholder="Appréciation générale..."><?= vdb($existing, 'appreciation') ?></textarea>
    </div>

    <?php if ($rapport['statut'] === 'brouillon'): ?>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer la fiche</button>
        <a href="rapport_edit.php?id=<?= $rid ?>" class="btn btn-secondary btn-lg">← Retour au rapport</a>
    </div>
    <?php else: ?>
    <div class="alert alert-info">ℹ️ Ce rapport est en mode lecture seule (statut : <?= $rapport['statut'] ?>).</div>
    <?php endif; ?>

</div>
</form>

<?php include '../includes/footer.php'; ?>
