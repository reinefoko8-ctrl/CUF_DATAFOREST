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

$existing = $db->query("SELECT * FROM fiche_pont_forestier WHERE rapport_id=$rid")->fetch_assoc();
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

    // Infos supplémentaires stockées en JSON dans ponts_json
    $ref_ouv  = sanitize($_POST['reference_ouvrage'] ?? '');
    $pont_num = sanitize($_POST['pont_forestier'] ?? '');
    $lon      = sanitize($_POST['longitude'] ?? '');
    $lat      = sanitize($_POST['latitude'] ?? '');
    $larg     = sanitize($_POST['largeur_pont'] ?? '');
    $long_p   = sanitize($_POST['longueur_pont'] ?? '');

    $ponts_json = json_encode([
        'reference_ouvrage' => $ref_ouv,
        'pont_forestier'    => $pont_num,
        'longitude'         => $lon,
        'latitude'          => $lat,
        'largeur_pont'      => $larg,
        'longueur_pont'     => $long_p,
    ]);

    if ($existing) {
        $stmt2 = $db->prepare("UPDATE fiche_pont_forestier SET
            titre_forestier=?, aac=?, nom_controleur=?, date_controle=?, uc=?,
            c1=?, c2=?, c3=?, c4=?, c5=?, c6=?, c7=?, c8=?, c9=?, c10=?,
            total_points=?, observations=?, appreciation=?, ponts_json=?
            WHERE rapport_id=?");
        $stmt2->bind_param('sssssssssssssssssssi',
            $tf, $aac, $nc, $dc, $uc,
            $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10,
            $tp, $obs, $app, $ponts_json, $rid);
    } else {
        $stmt2 = $db->prepare("INSERT INTO fiche_pont_forestier
            (rapport_id, titre_forestier, aac, nom_controleur, date_controle, uc,
             c1, c2, c3, c4, c5, c6, c7, c8, c9, c10,
             total_points, observations, appreciation, ponts_json)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt2->bind_param('isssssssssssssssssss',
            $rid, $tf, $aac, $nc, $dc, $uc,
            $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10,
            $tp, $obs, $app, $ponts_json);
    }

    if ($stmt2->execute()) {
        $msg = 'success';
        $existing = $db->query("SELECT * FROM fiche_pont_forestier WHERE rapport_id=$rid")->fetch_assoc();
    } else {
        $msg = 'error:' . $db->error;
    }
}

// Lire les infos pont depuis ponts_json
$pont_info = [];
if ($existing && !empty($existing['ponts_json'])) {
    $pont_info = json_decode($existing['ponts_json'], true) ?? [];
}

$pageTitle = 'Fiche Pont Forestier';
include '../includes/header.php';
function vpt($e, $f, $def = '') { return htmlspecialchars($e[$f] ?? $def); }
function svpt($e, $f) { return $e[$f] ?? ''; }
function vpi($pi, $f, $def = '') { return htmlspecialchars($pi[$f] ?? $def); }
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🌉 Construction d'un Pont Forestier</h1>
        <div class="breadcrumb">
            <a href="../dashboard_controleur.php">Tableau de bord</a> ›
            <a href="rapport_edit.php?id=<?= $rid ?>">Rapport #<?= $rid ?></a> › Pont forestier
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
        <h2>FICHE DE CONTRÔLE — "Construction d'un pont forestier"</h2>
        <p>Version 02, du 23/09/2025 &nbsp;|&nbsp; Rapport #<?= $rid ?></p>
    </div>
</div>
<div class="fiche-form-body">

    <!-- Infos générales -->
    <div class="form-row-3 mb-3">
        <div class="form-group">
            <label>Titre forestier</label>
            <input type="text" name="titre_forestier" class="form-control"
                value="<?= vpt($existing, 'titre_forestier', $rapport['titre_forestier'] ?? '') ?>"/>
        </div>
        <div class="form-group">
            <label>AAC</label>
            <input type="text" name="aac" class="form-control"
                value="<?= vpt($existing, 'aac', $rapport['aac'] ?? '') ?>"/>
        </div>
        <div class="form-group">
            <label>Nom du contrôleur</label>
            <input type="text" name="nom_controleur" class="form-control"
                value="<?= vpt($existing, 'nom_controleur', $_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>"/>
        </div>
        <div class="form-group">
            <label>Date de contrôle</label>
            <input type="date" name="date_controle" class="form-control"
                value="<?= vpt($existing, 'date_controle', date('Y-m-d')) ?>"/>
        </div>
        <div class="form-group">
            <label>Unité de comptage (UC)</label>
            <input type="text" name="uc" class="form-control"
                value="<?= vpt($existing, 'uc') ?>"/>
        </div>
    </div>

    <!-- Caractéristiques du pont -->
    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">🌉 Caractéristiques de l'ouvrage d'art</span>
        </div>
        <div class="card-body">
            <div class="form-row-3">
                <div class="form-group">
                    <label>Réf. ouvrage de franchissement</label>
                    <input type="text" name="reference_ouvrage" class="form-control"
                        value="<?= vpi($pont_info, 'reference_ouvrage') ?>"/>
                </div>
                <div class="form-group">
                    <label>Pont forestier n°</label>
                    <input type="text" name="pont_forestier" class="form-control"
                        value="<?= vpi($pont_info, 'pont_forestier') ?>"/>
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="text" name="longitude" class="form-control"
                        value="<?= vpi($pont_info, 'longitude') ?>" placeholder="Ex: 13.456"/>
                </div>
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="text" name="latitude" class="form-control"
                        value="<?= vpi($pont_info, 'latitude') ?>" placeholder="Ex: 3.789"/>
                </div>
                <div class="form-group">
                    <label>Largeur du pont (m)</label>
                    <input type="number" step="0.01" name="largeur_pont" class="form-control"
                        value="<?= vpi($pont_info, 'largeur_pont') ?>"/>
                </div>
                <div class="form-group">
                    <label>Longueur du pont (m)</label>
                    <input type="number" step="0.01" name="longueur_pont" class="form-control"
                        value="<?= vpi($pont_info, 'longueur_pont') ?>"/>
                </div>
            </div>
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
        ['c1',  'La construction du pont réduit-elle la largeur du lit du cours d\'eau de moins de 20% mesurée à partir de la ligne des hautes eaux ?'],
        ['c2',  'L\'extrémité du ponceau dans le cours d\'eau dépasse-t-elle la base du remblai qui étaye la route ?'],
        ['c3',  'Les longrines de stabilisation ont-elles été installées sur terre ferme ?'],
        ['c4',  'Y a-t-il stabilisation du lit du cours d\'eau à l\'entrée et à la sortie du ponceau sans obstruer le passage des poissons ?'],
        ['c5',  'Les zones en bordure du cours d\'eau (20 m de chaque côté) ont-elles été protégées de tout terrassement ?'],
        ['c6',  'Les billes de rétention de la terre sont-elles prévues ?'],
        ['c7',  'Si cours d\'eau navigable, le tablier du pont est-il d\'au moins 1,5 m au dessus du niveau des hautes eaux ?'],
        ['c8',  'Le pont est-il positionné sur une ligne droite ?'],
        ['c9',  'Le lit du cours d\'eau est-il dégagé de tout obstacle (absence de sédiments dans le cours d\'eau) ?'],
        ['c10', 'Les essences commerciales utilisées comme culées ou longrines sont-elles martelées et déclarées sur DF10 ?'],
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
                <input type="hidden" name="<?= $key ?>" value="<?= svpt($existing, $key) ?>"/>
                <div class="critere-score">
                    <button type="button" class="score-btn <?= svpt($existing, $key) === '1'  ? 'selected-1'  : '' ?>" data-val="1">1</button>
                    <button type="button" class="score-btn <?= svpt($existing, $key) === '0'  ? 'selected-0'  : '' ?>" data-val="0">0</button>
                    <button type="button" class="score-btn <?= svpt($existing, $key) === 'NA' ? 'selected-na' : '' ?>" data-val="NA">NA</button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-bar">
        <span class="total-label">📊 Total des points :</span>
        <span class="total-value" id="total-display"><?= vpt($existing, 'total_points', '0') ?>/10</span>
    </div>
    <input type="hidden" name="total_points" id="total-hidden" value="<?= vpt($existing, 'total_points', '0') ?>"/>

    <div class="form-group mt-2">
        <label>Observations et recommandations</label>
        <textarea name="observations" class="form-control" rows="4"
            placeholder="Vos observations..."><?= vpt($existing, 'observations') ?></textarea>
    </div>
    <div class="form-group">
        <label>Appréciation</label>
        <textarea name="appreciation" class="form-control" rows="2"
            placeholder="Appréciation générale..."><?= vpt($existing, 'appreciation') ?></textarea>
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
