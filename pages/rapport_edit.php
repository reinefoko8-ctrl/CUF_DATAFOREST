<?php
require_once '../includes/config.php';
requireLogin();
if (isAdmin()) { header('Location: ../dashboard_admin.php'); exit; }

$db  = getDB();
$uid = $_SESSION['user_id'];
$rid = (int)($_GET['id'] ?? 0);

// Charger le rapport
$stmt = $db->prepare("SELECT * FROM rapports WHERE id=? AND controleur_id=?");
$stmt->bind_param('ii', $rid, $uid);
$stmt->execute();
$rapport = $stmt->get_result()->fetch_assoc();
if (!$rapport) { header('Location: mes_rapports.php'); exit; }

$pageTitle = 'Modifier rapport #' . $rid;

// Vérifier quelles fiches sont remplies
function ficheRemplie($db, $table, $rid) {
    $r = $db->query("SELECT COUNT(*) FROM $table WHERE rapport_id=$rid")->fetch_row();
    return $r[0] > 0;
}

$fiches_status = [
    'parc_foret'         => ficheRemplie($db, 'fiche_parc_foret', $rid),
    'abattage'           => ficheRemplie($db, 'fiche_abattage', $rid),
    'routes'             => ficheRemplie($db, 'fiche_routes_forestieres', $rid),
    'tracabilite'        => ficheRemplie($db, 'fiche_tracabilite_grumes', $rid),
    'securite_tc'        => ficheRemplie($db, 'fiche_securite_tronconneuses', $rid),
    'sortie_pieds'       => ficheRemplie($db, 'fiche_sortie_pieds', $rid),
    'debardage'          => ficheRemplie($db, 'fiche_debardage', $rid),
    'pont'               => ficheRemplie($db, 'fiche_pont_forestier', $rid),
    'post_exploitation'  => ficheRemplie($db, 'fiche_post_exploitation', $rid),
    'dechets'            => ficheRemplie($db, 'fiche_dechets_foret', $rid),
    'base_mecanique'     => ficheRemplie($db, 'fiche_base_mecanique', $rid),
];

$nb_remplies = array_sum($fiches_status);

// Soumettre le rapport
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soumettre'])) {
    $avis = sanitize($_POST['avis_global'] ?? '');
    $stmt2 = $db->prepare("UPDATE rapports SET statut='soumis', avis_global=?, date_soumission=NOW() WHERE id=? AND controleur_id=?");
    $stmt2->bind_param('sii', $avis, $rid, $uid);
    $stmt2->execute();

    // Notifier les admins (requete preparee)
    $admins = $db->query("SELECT id FROM users WHERE role='administrateur' AND statut='actif'")->fetch_all(MYSQLI_ASSOC);
    $nm = "Le controleur " . $_SESSION['prenom'] . ' ' . $_SESSION['nom'] . " a soumis le rapport #" . $rid . ".";
    $type_notif = 'rapport_soumis';
    $stmt_notif = $db->prepare("INSERT INTO notifications (user_id, type, message, rapport_id) VALUES (?, ?, ?, ?)");
    foreach ($admins as $admin) {
        $ai = (int)$admin['id'];
        $stmt_notif->bind_param('issi', $ai, $type_notif, $nm, $rid);
        $stmt_notif->execute();
    }
    $msg = 'success';
    header("Location: rapport_detail.php?id=$rid&submitted=1");
    exit;
}

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">📝 <?= htmlspecialchars($rapport['titre'] ?: 'Rapport #'.$rid) ?></h1>
        <div class="breadcrumb">
            <a href="../dashboard_controleur.php">Tableau de bord</a> ›
            <a href="mes_rapports.php">Mes rapports</a> ›
            Rapport #<?= $rid ?>
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="generer_pdf.php?id=<?= $rid ?>" class="btn btn-secondary" target="_blank">📄 Aperçu PDF</a>
        <?php if ($rapport['statut'] === 'brouillon'): ?>
        <button class="btn btn-primary" onclick="document.getElementById('submit-modal').style.display='flex'">📨 Soumettre à l'admin</button>
        <?php endif; ?>
    </div>
</div>

<?php if ($rapport['statut'] !== 'brouillon'): ?>
<div class="alert alert-info">ℹ️ Ce rapport a le statut <strong><?= $rapport['statut'] ?></strong> et ne peut plus être modifié.</div>
<?php endif; ?>

<!-- Progression -->
<div class="card fade-in mb-3">
    <div class="card-body" style="display:flex;align-items:center;gap:20px;">
        <div>
            <div style="font-weight:700;font-size:1.1rem;color:var(--vert-fonce);"><?= $nb_remplies ?>/11 fiches remplies</div>
            <div style="font-size:0.82rem;color:var(--gris-texte);">TF: <?= htmlspecialchars($rapport['titre_forestier']??'-') ?> &nbsp;|&nbsp; AAC: <?= htmlspecialchars($rapport['aac']??'-') ?> &nbsp;|&nbsp; Date: <?= date('d/m/Y', strtotime($rapport['date_rapport'])) ?></div>
        </div>
        <div style="flex:1;height:10px;background:var(--gris-moyen);border-radius:5px;overflow:hidden;">
            <div style="height:100%;width:<?= round($nb_remplies/11*100) ?>%;background:linear-gradient(90deg,var(--vert-fonce),var(--vert-clair));border-radius:5px;transition:width 0.5s;"></div>
        </div>
        <div style="font-weight:700;color:var(--vert-moyen);"><?= round($nb_remplies/11*100) ?>%</div>
    </div>
</div>

<!-- Grille des fiches -->
<div class="fiches-grid">
<?php
$fiches = [
    ['key'=>'parc_foret',       'icon'=>'🌳','titre'=>'Parc Forêt',                 'url'=>'fiche_parc_foret.php'],
    ['key'=>'abattage',         'icon'=>'🪓','titre'=>'Abattage contrôlé',          'url'=>'fiche_abattage.php'],
    ['key'=>'routes',           'icon'=>'🛤️','titre'=>'Routes forestières',        'url'=>'fiche_routes.php'],
    ['key'=>'tracabilite',      'icon'=>'📦','titre'=>'Traçabilité grumes',         'url'=>'fiche_tracabilite.php'],
    ['key'=>'securite_tc',      'icon'=>'⚙️','titre'=>'Sécurité tronçonneuses',    'url'=>'fiche_securite_tc.php'],
    ['key'=>'sortie_pieds',     'icon'=>'👣','titre'=>'Sortie pieds',               'url'=>'fiche_sortie_pieds.php'],
    ['key'=>'debardage',        'icon'=>'🚜','titre'=>'Débardage',                  'url'=>'fiche_debardage.php'],
    ['key'=>'pont',             'icon'=>'🌉','titre'=>'Pont forestier',             'url'=>'fiche_pont.php'],
    ['key'=>'post_exploitation','icon'=>'🔄','titre'=>'Post exploitation',          'url'=>'fiche_post_exploitation.php'],
    ['key'=>'dechets',          'icon'=>'♻️','titre'=>'Déchets en forêt',          'url'=>'fiche_dechets.php'],
    ['key'=>'base_mecanique',   'icon'=>'🔧','titre'=>'Base mécanique',             'url'=>'fiche_base_mecanique.php'],
];
foreach ($fiches as $f):
    $ok = $fiches_status[$f['key']];
?>
<a href="<?= $f['url'] ?>?rapport_id=<?= $rid ?>" class="fiche-card <?= $ok ? 'remplie' : '' ?> fade-in">
    <span class="fiche-icon"><?= $f['icon'] ?></span>
    <h3><?= $f['titre'] ?></h3>
    <div class="fiche-status <?= $ok ? 'ok' : 'pending' ?>">
        <?= $ok ? '✅ Remplie — cliquer pour modifier' : '⊙ Non remplie — cliquer pour remplir' ?>
    </div>
</a>
<?php endforeach; ?>
</div>

<!-- Modal soumission -->
<div id="submit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:var(--radius);padding:32px;max-width:500px;width:90%;box-shadow:var(--ombre-forte);">
        <h3 style="color:var(--vert-fonce);margin-bottom:16px;">📨 Soumettre le rapport</h3>
        <p style="color:var(--gris-texte);margin-bottom:20px;font-size:0.88rem;">
            Ajoutez votre avis global sur cette évaluation avant de l'envoyer à l'administrateur.
            Une fois soumis, vous ne pourrez plus modifier le rapport.
        </p>
        <form method="POST">
            <input type="hidden" name="soumettre" value="1"/>
            <div class="form-group">
                <label>Votre avis global (optionnel)</label>
                <textarea name="avis_global" class="form-control" rows="4" placeholder="Résumé de vos observations, points saillants, recommandations générales..."></textarea>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('submit-modal').style.display='none'">Annuler</button>
                <button type="submit" class="btn btn-primary">✅ Confirmer la soumission</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
