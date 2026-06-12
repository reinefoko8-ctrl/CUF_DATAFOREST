<?php
require_once 'includes/config.php';
requireAdmin();

$db = getDB();
$pageTitle = 'Tableau de bord Admin';

// Stats globales
$stats = [];
$stats['total_rapports']  = $db->query("SELECT COUNT(*) FROM rapports")->fetch_row()[0];
$stats['rapports_soumis'] = $db->query("SELECT COUNT(*) FROM rapports WHERE statut='soumis'")->fetch_row()[0];
$stats['rapports_valides']= $db->query("SELECT COUNT(*) FROM rapports WHERE statut='validé'")->fetch_row()[0];
$stats['controleurs']     = $db->query("SELECT COUNT(*) FROM users WHERE role='controleur' AND statut='actif'")->fetch_row()[0];
$stats['en_attente']      = $db->query("SELECT COUNT(*) FROM users WHERE statut='en_attente'")->fetch_row()[0];

// Derniers rapports soumis
$rapports = $db->query("
    SELECT r.*, u.nom, u.prenom, u.titre_forestier
    FROM rapports r
    JOIN users u ON r.controleur_id = u.id
    ORDER BY r.date_creation DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Activité récente
$activite = $db->query("
    SELECT r.*, u.nom, u.prenom
    FROM rapports r
    JOIN users u ON r.controleur_id = u.id
    WHERE r.date_soumission IS NOT NULL
    ORDER BY r.date_soumission DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🏠 Tableau de bord</h1>
        <div class="breadcrumb">Administrateur — <?= date('d/m/Y') ?></div>
    </div>
    <a href="pages/rapports_admin.php" class="btn btn-primary">📋 Voir tous les rapports</a>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card fade-in" data-icon="📋">
        <div class="stat-number"><?= $stats['total_rapports'] ?></div>
        <div class="stat-label">Rapports au total</div>
    </div>
    <div class="stat-card orange fade-in" data-icon="📨">
        <div class="stat-number"><?= $stats['rapports_soumis'] ?></div>
        <div class="stat-label">En attente de validation</div>
    </div>
    <div class="stat-card fade-in" data-icon="✅">
        <div class="stat-number"><?= $stats['rapports_valides'] ?></div>
        <div class="stat-label">Rapports validés</div>
    </div>
    <div class="stat-card bleu fade-in" data-icon="👷">
        <div class="stat-number"><?= $stats['controleurs'] ?></div>
        <div class="stat-label">Contrôleurs actifs</div>
    </div>
    <div class="stat-card rouge fade-in" data-icon="⏳">
        <div class="stat-number"><?= $stats['en_attente'] ?></div>
        <div class="stat-label">Comptes en attente</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">

    <!-- Derniers rapports -->
    <div class="card fade-in">
        <div class="card-header">
            <span class="card-title">📋 Derniers rapports reçus</span>
            <a href="pages/rapports_admin.php" class="btn btn-sm btn-secondary">Voir tout</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Contrôleur</th>
                            <th>Titre forestier</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rapports as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></strong></td>
                            <td><?= htmlspecialchars($r['titre_forestier'] ?? '-') ?></td>
                            <td><?= date('d/m/Y', strtotime($r['date_rapport'])) ?></td>
                            <td>
                                <?php
                                $badges = [
                                    'brouillon' => 'secondary',
                                    'soumis'    => 'warning',
                                    'validé'    => 'success',
                                    'rejeté'    => 'danger',
                                ];
                                $b = $badges[$r['statut']] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $b ?>"><?= ucfirst($r['statut']) ?></span>
                            </td>
                            <td>
                                <a href="pages/rapport_detail.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">👁 Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rapports)): ?>
                        <tr><td colspan="5" class="text-center" style="padding:24px;color:var(--gris-texte);">Aucun rapport pour l'instant.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions rapides & comptes en attente -->
    <div>
        <div class="card fade-in">
            <div class="card-header">
                <span class="card-title">⚡ Actions rapides</span>
            </div>
            <div class="card-body">
                <a href="pages/utilisateurs.php" class="btn btn-secondary btn-block mb-1">👥 Gérer les utilisateurs</a>
                <a href="pages/statistiques.php" class="btn btn-secondary btn-block mb-1">📊 Voir les statistiques</a>
                <a href="pages/export_global.php" class="btn btn-secondary btn-block mb-1">📥 Exporter les données</a>
                <?php if ($stats['en_attente'] > 0): ?>
                <div class="alert alert-warning mt-2">
                    ⚠️ <?= $stats['en_attente'] ?> compte(s) en attente d'activation.
                    <a href="pages/utilisateurs.php?filter=en_attente" style="font-weight:700;">Activer →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card fade-in">
            <div class="card-header">
                <span class="card-title">🕐 Activité récente</span>
            </div>
            <div class="card-body" style="padding:12px 0;">
                <?php foreach ($activite as $a): ?>
                <div style="padding:10px 20px;border-bottom:1px solid var(--gris-moyen);font-size:0.83rem;">
                    <strong><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></strong>
                    a soumis un rapport
                    <div style="color:var(--gris-texte);font-size:0.77rem;"><?= date('d/m/Y H:i', strtotime($a['date_soumission'])) ?></div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($activite)): ?>
                    <div style="padding:16px 20px;color:var(--gris-texte);font-size:0.83rem;">Aucune activité récente.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
