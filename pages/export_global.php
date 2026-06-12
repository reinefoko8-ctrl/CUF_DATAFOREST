<?php
// pages/export_global.php
require_once '../includes/config.php';
requireAdmin();
$db = getDB();
$pageTitle = 'Export Global';

// Export CSV si demandé
if (isset($_GET['export'])) {
    $type = sanitize($_GET['export']);

    if ($type === 'rapports') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="cuf_rapports_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($out, ['ID', 'Contrôleur', 'Titre', 'TF', 'AAC', 'Date rapport', 'Statut', 'Date soumission', 'Avis contrôleur', 'Commentaire admin'], ';');
        $rows = $db->query("SELECT r.*,u.nom,u.prenom FROM rapports r JOIN users u ON r.controleur_id=u.id ORDER BY r.date_creation DESC")->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'],
                $r['prenom'] . ' ' . $r['nom'],
                $r['titre'] ?? '',
                $r['titre_forestier'] ?? '',
                $r['aac'] ?? '',
                $r['date_rapport'],
                $r['statut'],
                $r['date_soumission'] ?? '',
                $r['avis_global'] ?? '',
                $r['commentaire_admin'] ?? '',
            ], ';');
        }
        fclose($out);
        exit;
    }

    if ($type === 'utilisateurs') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="cuf_utilisateurs_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Nom', 'Prénom', 'Email', 'Rôle', 'Titre forestier', 'AAC', 'Statut', 'Date inscription'], ';');
        $rows = $db->query("SELECT * FROM users ORDER BY role,date_creation DESC")->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $u) {
            fputcsv($out, [$u['id'], $u['nom'], $u['prenom'], $u['email'], $u['role'], $u['titre_forestier'] ?? '', $u['aac'] ?? '', $u['statut'], $u['date_creation']], ';');
        }
        fclose($out);
        exit;
    }

    if ($type === 'parc_foret') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="cuf_parc_foret_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['Rapport ID', 'Contrôleur', 'TF', 'AAC', 'Date contrôle', 'Longitude', 'Latitude', 'Superficie', 'Nb pieds',
            'C1','C2','C3','C4','C5','C6','C7','C8','C9','C10','Total','Observations'], ';');
        $rows = $db->query("
            SELECT f.*, u.nom, u.prenom, r.aac
            FROM fiche_parc_foret f
            JOIN rapports r ON f.rapport_id = r.id
            JOIN users u ON r.controleur_id = u.id
            ORDER BY f.rapport_id DESC
        ")->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $f) {
            fputcsv($out, [
                $f['rapport_id'], $f['prenom'].' '.$f['nom'], $f['reference_parc_foret']??'', $f['aac']??'',
                $f['date_controle']??'', $f['longitude']??'', $f['latitude']??'',
                $f['superficie_parc']??'', $f['nombre_pieds_debardés']??'',
                $f['c1_installation']??'',$f['c2_panneau_matricule']??'',$f['c3_pente_douce']??'',
                $f['c4_distance_nappe']??'',$f['c5_tiges_avenir']??'',$f['c6_marquage_grumes']??'',
                $f['c7_couche_debris']??'',$f['c8_culées_coin']??'',$f['c9_coursons']??'',
                $f['c10_marques_ab']??'',$f['total_points']??'',$f['observations']??''
            ], ';');
        }
        fclose($out);
        exit;
    }

    if ($type === 'grumes') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="cuf_grumes_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['Rapport ID','Contrôleur','TF','AAC','Date contrôle','N°','Essence','N° DF10','Code barre','Date abattage','Volume','Ø PB','Ø GB','Long','N° LV','Affectation'], ';');
        $rows = $db->query("
            SELECT f.*, u.nom, u.prenom, r.titre_forestier, r.aac
            FROM fiche_tracabilite_grumes f
            JOIN rapports r ON f.rapport_id = r.id
            JOIN users u ON r.controleur_id = u.id
        ")->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $f) {
            $grumes = json_decode($f['grume_json'] ?? '[]', true) ?? [];
            foreach ($grumes as $i => $g) {
                fputcsv($out, [
                    $f['rapport_id'], $f['prenom'].' '.$f['nom'],
                    $f['titre_forestier']??'', $f['aac']??'', $f['date_controle']??'',
                    $i+1, $g['essence']??'', $g['num_df10']??'', $g['code_barre']??'',
                    $g['date_abattage']??'', $g['volume']??'', $g['diam_pb']??'',
                    $g['diam_gb']??'', $g['long']??'', $g['n_lv']??'', $g['affectation']??''
                ], ';');
            }
        }
        fclose($out);
        exit;
    }
}

// Stats pour affichage
$stats = [
    'rapports'    => $db->query("SELECT COUNT(*) FROM rapports")->fetch_row()[0],
    'utilisateurs'=> $db->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'parc_foret'  => $db->query("SELECT COUNT(*) FROM fiche_parc_foret")->fetch_row()[0],
    'grumes'      => $db->query("SELECT SUM(JSON_LENGTH(grume_json)) FROM fiche_tracabilite_grumes WHERE grume_json IS NOT NULL AND grume_json != '[]'")->fetch_row()[0] ?? 0,
];

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">📥 Export Global</h1>
        <div class="breadcrumb"><a href="../dashboard_admin.php">Tableau de bord</a> › Export</div>
    </div>
</div>

<div class="alert alert-info">
    ℹ️ Les fichiers exportés sont au format <strong>CSV (séparateur point-virgule)</strong>, compatibles avec Microsoft Excel et LibreOffice Calc. L'encodage est UTF-8 avec BOM pour un affichage correct des caractères accentués.
</div>

<div class="fiches-grid">

    <div class="fiche-card fade-in">
        <span class="fiche-icon">📋</span>
        <h3>Rapports</h3>
        <p>Tous les rapports avec leurs informations générales, statuts et commentaires.</p>
        <div style="margin-top:12px;font-weight:700;color:var(--vert-moyen);"><?= $stats['rapports'] ?> rapport(s)</div>
        <a href="export_global.php?export=rapports" class="btn btn-primary btn-sm mt-2" style="margin-top:12px;">
            📥 Télécharger CSV
        </a>
    </div>

    <div class="fiche-card fade-in">
        <span class="fiche-icon">👥</span>
        <h3>Utilisateurs</h3>
        <p>Liste complète des utilisateurs inscrits (contrôleurs et administrateurs).</p>
        <div style="margin-top:12px;font-weight:700;color:var(--vert-moyen);"><?= $stats['utilisateurs'] ?> utilisateur(s)</div>
        <a href="export_global.php?export=utilisateurs" class="btn btn-primary btn-sm" style="margin-top:12px;">
            📥 Télécharger CSV
        </a>
    </div>

    <div class="fiche-card fade-in">
        <span class="fiche-icon">🌳</span>
        <h3>Fiches Parc Forêt</h3>
        <p>Données détaillées de toutes les fiches Parc Forêt avec les scores par critère.</p>
        <div style="margin-top:12px;font-weight:700;color:var(--vert-moyen);"><?= $stats['parc_foret'] ?> fiche(s)</div>
        <a href="export_global.php?export=parc_foret" class="btn btn-primary btn-sm" style="margin-top:12px;">
            📥 Télécharger CSV
        </a>
    </div>

    <div class="fiche-card fade-in">
        <span class="fiche-icon">📦</span>
        <h3>Registre Grumes</h3>
        <p>Toutes les grumes enregistrées dans les fiches de traçabilité (essence, volumes, codes).</p>
        <div style="margin-top:12px;font-weight:700;color:var(--vert-moyen);"><?= $stats['grumes'] ?> grume(s)</div>
        <a href="export_global.php?export=grumes" class="btn btn-primary btn-sm" style="margin-top:12px;">
            📥 Télécharger CSV
        </a>
    </div>

</div>

<div class="card fade-in">
    <div class="card-header"><span class="card-title">📊 Aperçu rapide — Derniers rapports soumis</span></div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Contrôleur</th><th>TF</th><th>AAC</th><th>Date</th><th>Statut</th><th>PDF</th></tr>
            </thead>
            <tbody>
            <?php
            $recent = $db->query("
                SELECT r.*,u.nom,u.prenom FROM rapports r
                JOIN users u ON r.controleur_id=u.id
                WHERE r.statut IN ('soumis','validé')
                ORDER BY r.date_soumission DESC LIMIT 15
            ")->fetch_all(MYSQLI_ASSOC);
            $b = ['soumis'=>'warning','validé'=>'success','rejeté'=>'danger','brouillon'=>'secondary'];
            foreach ($recent as $r):?>
            <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?></td>
                <td><?= htmlspecialchars($r['titre_forestier']??'-') ?></td>
                <td><?= htmlspecialchars($r['aac']??'-') ?></td>
                <td><?= date('d/m/Y', strtotime($r['date_rapport'])) ?></td>
                <td><span class="badge badge-<?= $b[$r['statut']]??'secondary' ?>"><?= ucfirst($r['statut']) ?></span></td>
                <td><a href="generer_pdf.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">📄 PDF</a></td>
            </tr>
            <?php endforeach;?>
            <?php if (empty($recent)):?>
            <tr><td colspan="7" class="text-center" style="padding:24px;color:var(--gris-texte);">Aucun rapport soumis.</td></tr>
            <?php endif;?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
