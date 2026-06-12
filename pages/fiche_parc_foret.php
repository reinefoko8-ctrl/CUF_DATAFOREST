<?php
require_once '../includes/config.php';
requireLogin();
$db  = getDB();
$uid = $_SESSION['user_id'];
$rid = (int)($_GET['rapport_id'] ?? 0);
if (!$rid) { header('Location:../dashboard_controleur.php'); exit; }

$stmt = $db->prepare("SELECT * FROM rapports WHERE id=?");
$stmt->bind_param('i', $rid); $stmt->execute();
$rapport = $stmt->get_result()->fetch_assoc();
if (!$rapport) { header('Location:../dashboard_controleur.php'); exit; }

$existing = $db->query("SELECT * FROM fiche_parc_foret WHERE rapport_id=$rid")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rapport['statut'] === 'brouillon') {
    $ref  = sanitize($_POST['reference_parc_foret'] ?? '');
    $lon  = sanitize($_POST['longitude'] ?? '');
    $lat  = sanitize($_POST['latitude'] ?? '');
    $sup  = sanitize($_POST['superficie_parc'] ?? '');
    $nbd  = sanitize($_POST['nombre_pieds_debardés'] ?? '');
    $nc   = sanitize($_POST['nom_controleur'] ?? '');
    $dc   = sanitize($_POST['date_controle'] ?? date('Y-m-d'));
    $c1   = sanitize($_POST['c1_installation'] ?? '');
    $c2   = sanitize($_POST['c2_panneau_matricule'] ?? '');
    $c3   = sanitize($_POST['c3_pente_douce'] ?? '');
    $c4   = sanitize($_POST['c4_distance_nappe'] ?? '');
    $c5   = sanitize($_POST['c5_tiges_avenir'] ?? '');
    $c6   = sanitize($_POST['c6_marquage_grumes'] ?? '');
    $c7   = sanitize($_POST['c7_couche_debris'] ?? '');
    $c8   = sanitize($_POST['c8_culées_coin'] ?? '');
    $c9   = sanitize($_POST['c9_coursons'] ?? '');
    $c10  = sanitize($_POST['c10_marques_ab'] ?? '');
    $tp   = sanitize($_POST['total_points'] ?? '0');
    $obs  = sanitize($_POST['observations'] ?? '');
    $app  = sanitize($_POST['appreciation'] ?? '');

    if ($existing) {
        $stmt2 = $db->prepare("UPDATE fiche_parc_foret SET
            reference_parc_foret=?, longitude=?, latitude=?, superficie_parc=?,
            nombre_pieds_debardés=?, nom_controleur=?, date_controle=?,
            c1_installation=?, c2_panneau_matricule=?, c3_pente_douce=?,
            c4_distance_nappe=?, c5_tiges_avenir=?, c6_marquage_grumes=?,
            c7_couche_debris=?, c8_culées_coin=?, c9_coursons=?, c10_marques_ab=?,
            total_points=?, observations=?, appreciation=?
            WHERE rapport_id=?");
        $stmt2->bind_param('ssssssssssssssssssssi',
            $ref,$lon,$lat,$sup,$nbd,$nc,$dc,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tp,$obs,$app,$rid);
    } else {
        $stmt2 = $db->prepare("INSERT INTO fiche_parc_foret
            (rapport_id, reference_parc_foret, longitude, latitude, superficie_parc,
             nombre_pieds_debardés, nom_controleur, date_controle,
             c1_installation, c2_panneau_matricule, c3_pente_douce,
             c4_distance_nappe, c5_tiges_avenir, c6_marquage_grumes,
             c7_couche_debris, c8_culées_coin, c9_coursons, c10_marques_ab,
             total_points, observations, appreciation)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt2->bind_param('issssssssssssssssssss',
            $rid,$ref,$lon,$lat,$sup,$nbd,$nc,$dc,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tp,$obs,$app);
    }

    if ($stmt2->execute()) {
        $msg = 'success';
        $existing = $db->query("SELECT * FROM fiche_parc_foret WHERE rapport_id=$rid")->fetch_assoc();
    } else {
        $msg = 'error:' . $db->error;
    }
}

$pageTitle = 'Fiche Parc Forêt';
include '../includes/header.php';
function val($e, $f, $def = '') { return htmlspecialchars($e[$f] ?? $def); }
function sv($e, $f) { return $e[$f] ?? ''; }
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🌳 Fiche Parc Forêt</h1>
        <div class="breadcrumb">
            <a href="../dashboard_controleur.php">Tableau de bord</a> ›
            <a href="rapport_edit.php?id=<?= $rid ?>">Rapport #<?= $rid ?></a> › Parc Forêt
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
        <h2>FICHE DE CONTRÔLE — "Parc Forêt"</h2>
        <p>Version 03 du 01/04/2026 &nbsp;|&nbsp; Rapport #<?= $rid ?></p>
    </div>
</div>
<div class="fiche-form-body">

    <div class="form-row-3 mb-3">
        <div class="form-group"><label>Référence parc forêt (TF)</label>
            <input type="text" name="reference_parc_foret" class="form-control"
            value="<?= val($existing,'reference_parc_foret',$rapport['titre_forestier']??'') ?>"/></div>
        <div class="form-group"><label>AAC</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($rapport['aac']??'') ?>"
            readonly style="background:#f5f5f5;"/></div>
        <div class="form-group"><label>Date de contrôle</label>
            <input type="date" name="date_controle" class="form-control"
            value="<?= val($existing,'date_controle',date('Y-m-d')) ?>"/></div>
        <div class="form-group"><label>Longitude</label>
            <input type="text" name="longitude" class="form-control"
            value="<?= val($existing,'longitude') ?>" placeholder="Ex: 13.456"/></div>
        <div class="form-group"><label>Latitude</label>
            <input type="text" name="latitude" class="form-control"
            value="<?= val($existing,'latitude') ?>" placeholder="Ex: 3.789"/></div>
        <div class="form-group"><label>Superficie du parc (m²)</label>
            <input type="number" name="superficie_parc" class="form-control"
            value="<?= val($existing,'superficie_parc') ?>" placeholder="Ex: 2000"/></div>
        <div class="form-group"><label>Nb pieds débardés vers le parc</label>
            <input type="number" name="nombre_pieds_debardés" class="form-control"
            value="<?= val($existing,'nombre_pieds_debardés') ?>"/></div>
        <div class="form-group"><label>Nom du contrôleur</label>
            <input type="text" name="nom_controleur" class="form-control"
            value="<?= val($existing,'nom_controleur',$_SESSION['prenom'].' '.$_SESSION['nom']) ?>"/></div>
    </div>

    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Critères d'évaluation</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;">
        <strong>1</strong> = Conforme &nbsp;|&nbsp;
        <strong>0</strong> = Non conforme &nbsp;|&nbsp;
        <strong>NA</strong> = Non applicable
    </p>

    <?php $criteres = [
        ['c1_installation',     "L'installation du parc respecte-t-elle l'emplacement initial planifié ?"],
        ['c2_panneau_matricule',"Le parc est-il immatriculé (plaque signalétique &amp; information conforme) ?"],
        ['c3_pente_douce',      "Le parc est-il installé à plus de 30 m d'un plan d'eau ?"],
        ['c4_distance_nappe',   "Existe-t-il une pente douce permettant le drainage des eaux ?"],
        ['c5_tiges_avenir',     "La distance entre 2 parcs est-elle ≥ 250 m et la superficie totale < 2 000 m² ?"],
        ['c6_marquage_grumes',  "Les tiges d'avenir à proximité ont-elles été identifiées et maîtrisées ?"],
        ['c7_couche_debris',    "Le marquage des grumes est-il lisible et correct ?"],
        ['c8_culées_coin',      "La couche de débris est-elle entreposée dans un coin pour réutilisation future ?"],
        ['c9_coursons',         "Existe-t-il une des culées dans un coin pour la réutilisation future ?"],
        ['c10_marques_ab',      "Tous les coursons abandonnés sont-ils marqués 'AB' et portent-ils des N°DF10 ?"],
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
                <input type="hidden" name="<?= $key ?>" value="<?= sv($existing, $key) ?>"/>
                <div class="critere-score">
                    <button type="button" class="score-btn <?= sv($existing,$key)==='1'  ? 'selected-1'  : '' ?>" data-val="1">1</button>
                    <button type="button" class="score-btn <?= sv($existing,$key)==='0'  ? 'selected-0'  : '' ?>" data-val="0">0</button>
                    <button type="button" class="score-btn <?= sv($existing,$key)==='NA' ? 'selected-na' : '' ?>" data-val="NA">NA</button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-bar">
        <span class="total-label">📊 Total des points :</span>
        <span class="total-value" id="total-display"><?= val($existing,'total_points','0') ?>/10</span>
    </div>
    <input type="hidden" name="total_points" id="total-hidden" value="<?= val($existing,'total_points','0') ?>"/>

    <div class="form-group mt-2">
        <label>Observations et/ou recommandations</label>
        <textarea name="observations" class="form-control" rows="4"
            placeholder="Vos observations..."><?= val($existing,'observations') ?></textarea>
    </div>
    <div class="form-group">
        <label>Appréciation</label>
        <textarea name="appreciation" class="form-control" rows="2"
            placeholder="Appréciation générale..."><?= val($existing,'appreciation') ?></textarea>
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
