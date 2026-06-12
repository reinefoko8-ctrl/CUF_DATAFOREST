<?php
require_once 'includes/config.php';
requireLogin();
if (isAdmin()) { header('Location: dashboard_admin.php'); exit; }

$db  = getDB();
$uid = $_SESSION['user_id'];
$pageTitle = 'Mon tableau de bord';

// Stats contrôleur
$total   = $db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid")->fetch_row()[0];
$soumis  = $db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid AND statut='soumis'")->fetch_row()[0];
$valides = $db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid AND statut='validé'")->fetch_row()[0];
$rejetes = $db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid AND statut='rejeté'")->fetch_row()[0];

// Rapports récents
$rapports = $db->query("
    SELECT * FROM rapports WHERE controleur_id=$uid
    ORDER BY date_creation DESC LIMIT 8
")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🌿 Bonjour, <?= htmlspecialchars($_SESSION['prenom']) ?> !</h1>
        <div class="breadcrumb">Contrôleur — <?= date('d/m/Y') ?> &nbsp;|&nbsp; Titre: <?= htmlspecialchars($_SESSION['titre_forestier'] ?? '-') ?> &nbsp;|&nbsp; AAC: <?= htmlspecialchars($_SESSION['aac'] ?? '-') ?></div>
    </div>
    <a href="pages/nouveau_rapport.php" class="btn btn-primary btn-lg">➕ Nouveau rapport</a>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card fade-in" data-icon="📋">
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Mes rapports</div>
    </div>
    <div class="stat-card orange fade-in" data-icon="📨">
        <div class="stat-number"><?= $soumis ?></div>
        <div class="stat-label">En attente</div>
    </div>
    <div class="stat-card fade-in" data-icon="✅">
        <div class="stat-number"><?= $valides ?></div>
        <div class="stat-label">Validés</div>
    </div>
    <div class="stat-card rouge fade-in" data-icon="❌">
        <div class="stat-number"><?= $rejetes ?></div>
        <div class="stat-label">Rejetés</div>
    </div>
</div>

<!-- Fiches rapides -->
<div class="card fade-in mb-3">
    <div class="card-header">
        <span class="card-title">📝 Fiches d'évaluation disponibles</span>
        <a href="pages/nouveau_rapport.php" class="btn btn-sm btn-primary">Créer un rapport</a>
    </div>
    <div class="card-body">
        <div class="fiches-grid">
            <?php
            $fiches = [
                ['icon'=>'🌳','titre'=>'Parc Forêt',                    'desc'=>'Installation, marquage, critères du parc (10 critères)',               'url'=>'pages/fiche_parc_foret.php'],
                ['icon'=>'🪓','titre'=>'Abattage contrôlé',             'desc'=>'Opérations d\'abattage, piste de fuite, charnière (11 catégories)',    'url'=>'pages/fiche_abattage.php'],
                ['icon'=>'🛤️','titre'=>'Routes forestières',           'desc'=>'État et conformité des routes en forêt',                               'url'=>'pages/fiche_routes.php'],
                ['icon'=>'📦','titre'=>'Traçabilité grumes',            'desc'=>'Suivi et traçabilité des grumes forest',                               'url'=>'pages/fiche_tracabilite.php'],
                ['icon'=>'⚙️','titre'=>'Sécurité tronçonneuses',       'desc'=>'Éléments de sécurité des équipements (8 points)',                      'url'=>'pages/fiche_securite_tc.php'],
                ['icon'=>'👣','titre'=>'Sortie pieds',                  'desc'=>'Tracé pistes, arbres triés, matérialisation (10 critères)',            'url'=>'pages/fiche_sortie_pieds.php'],
                ['icon'=>'🚜','titre'=>'Débardage',                     'desc'=>'Pistes de débardage, cours d\'eau, tiges d\'avenir (10 critères)',     'url'=>'pages/fiche_debardage.php'],
                ['icon'=>'🌉','titre'=>'Pont forestier',                'desc'=>'Construction et conformité des ponts (10 critères)',                   'url'=>'pages/fiche_pont.php'],
                ['icon'=>'🔄','titre'=>'Post exploitation',             'desc'=>'Opérations après exploitation, fermeture pistes',                     'url'=>'pages/fiche_post_exploitation.php'],
                ['icon'=>'♻️','titre'=>'Gestion déchets forêt',        'desc'=>'Collecte, stockage et transfert des déchets (13 critères)',            'url'=>'pages/fiche_dechets.php'],
                ['icon'=>'🔧','titre'=>'Base mécanique forêt',         'desc'=>'Inspection complète de la base mécanique',                            'url'=>'pages/fiche_base_mecanique.php'],
            ];
            foreach ($fiches as $f):
            ?>
            <a href="<?= $f['url'] ?>" class="fiche-card fade-in">
                <span class="fiche-icon"><?= $f['icon'] ?></span>
                <h3><?= $f['titre'] ?></h3>
                <p><?= $f['desc'] ?></p>
                <div class="fiche-status pending">⊙ Remplir la fiche →</div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Rapports récents -->
<div class="card fade-in">
    <div class="card-header">
        <span class="card-title">🕐 Mes rapports récents</span>
        <a href="pages/mes_rapports.php" class="btn btn-sm btn-secondary">Voir tout</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Date rapport</th>
                        <th>Date création</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rapports as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['titre'] ?: 'Rapport #' . $r['id']) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['date_rapport'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($r['date_creation'])) ?></td>
                        <td>
                            <?php $badges=['brouillon'=>'secondary','soumis'=>'warning','validé'=>'success','rejeté'=>'danger']; ?>
                            <span class="badge badge-<?= $badges[$r['statut']] ?? 'secondary' ?>"><?= ucfirst($r['statut']) ?></span>
                        </td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="pages/rapport_detail.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">👁 Voir</a>
                            <?php if ($r['statut'] === 'brouillon'): ?>
                            <a href="pages/rapport_edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-secondary">✏️ Modifier</a>
                            <?php endif; ?>
                            <a href="pages/generer_pdf.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info" target="_blank">📄 PDF</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rapports)): ?>
                    <tr><td colspan="6" class="text-center" style="padding:28px;color:var(--gris-texte);">
                        Aucun rapport. <a href="pages/nouveau_rapport.php" style="color:var(--vert-moyen);font-weight:600;">Créer votre premier rapport →</a>
                    </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
