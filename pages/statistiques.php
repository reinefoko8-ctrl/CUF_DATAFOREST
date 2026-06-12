<?php
require_once '../includes/config.php';
requireAdmin();
$db=getDB();
$pageTitle='Statistiques';

// Stats générales
$total_rapports   =$db->query("SELECT COUNT(*) FROM rapports")->fetch_row()[0];
$rapports_valides =$db->query("SELECT COUNT(*) FROM rapports WHERE statut='validé'")->fetch_row()[0];
$rapports_soumis  =$db->query("SELECT COUNT(*) FROM rapports WHERE statut='soumis'")->fetch_row()[0];
$total_ctrl       =$db->query("SELECT COUNT(*) FROM users WHERE role='controleur'")->fetch_row()[0];
$total_grumes     =$db->query("SELECT COUNT(*) FROM fiche_tracabilite_grumes")->fetch_row()[0];

// Rapports par mois (6 derniers)
$par_mois=$db->query("
    SELECT DATE_FORMAT(date_rapport,'%Y-%m') as mois, COUNT(*) as nb
    FROM rapports GROUP BY mois ORDER BY mois DESC LIMIT 6
")->fetch_all(MYSQLI_ASSOC);
$par_mois=array_reverse($par_mois);

// Rapports par contrôleur
$par_ctrl=$db->query("
    SELECT u.nom, u.prenom, COUNT(r.id) as nb, SUM(r.statut='validé') as valides
    FROM users u LEFT JOIN rapports r ON r.controleur_id=u.id
    WHERE u.role='controleur'
    GROUP BY u.id ORDER BY nb DESC LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Fiches remplies par type
$fiches_stats=[
    ['Parc Forêt','fiche_parc_foret'],
    ['Abattage','fiche_abattage'],
    ['Routes','fiche_routes_forestieres'],
    ['Traçabilité','fiche_tracabilite_grumes'],
    ['Sécurité TC','fiche_securite_tronconneuses'],
    ['Sortie pieds','fiche_sortie_pieds'],
    ['Débardage','fiche_debardage'],
    ['Pont','fiche_pont_forestier'],
    ['Post exploitation','fiche_post_exploitation'],
    ['Déchets','fiche_dechets_foret'],
    ['Base méc.','fiche_base_mecanique'],
];
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">📊 Statistiques</h1>
    <div class="breadcrumb"><a href="../dashboard_admin.php">Tableau de bord</a> › Statistiques</div></div>
</div>

<!-- KPIs -->
<div class="stats-grid fade-in">
    <div class="stat-card" data-icon="📋"><div class="stat-number"><?=$total_rapports?></div><div class="stat-label">Total rapports</div></div>
    <div class="stat-card" data-icon="✅"><div class="stat-number"><?=$rapports_valides?></div><div class="stat-label">Validés</div></div>
    <div class="stat-card orange" data-icon="📨"><div class="stat-number"><?=$rapports_soumis?></div><div class="stat-label">En attente validation</div></div>
    <div class="stat-card bleu" data-icon="👷"><div class="stat-number"><?=$total_ctrl?></div><div class="stat-label">Contrôleurs inscrits</div></div>
    <div class="stat-card" data-icon="📦"><div class="stat-number"><?=$total_grumes?></div><div class="stat-label">Fiches traçabilité</div></div>
    <div class="stat-card" data-icon="📈">
        <div class="stat-number"><?=$total_rapports>0?round($rapports_valides/$total_rapports*100):0?>%</div>
        <div class="stat-label">Taux de validation</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

<!-- Rapports par mois -->
<div class="card fade-in">
    <div class="card-header"><span class="card-title">📅 Rapports par mois</span></div>
    <div class="card-body">
    <?php if($par_mois):$max_m=max(array_column($par_mois,'nb'));foreach($par_mois as $m):$pct=$max_m>0?round($m['nb']/$max_m*100):0;?>
    <div style="margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;font-size:0.83rem;margin-bottom:4px;">
            <span><?=date('M Y',strtotime($m['mois'].'-01'))?></span>
            <strong style="color:var(--vert-moyen);"><?=$m['nb']?> rapport(s)</strong>
        </div>
        <div style="height:8px;background:var(--gris-moyen);border-radius:4px;">
            <div style="height:100%;width:<?=$pct?>%;background:linear-gradient(90deg,var(--vert-fonce),var(--vert-clair));border-radius:4px;"></div>
        </div>
    </div>
    <?php endforeach;else:?><p style="color:var(--gris-texte);">Aucune donnée.</p><?php endif;?>
    </div>
</div>

<!-- Par contrôleur -->
<div class="card fade-in">
    <div class="card-header"><span class="card-title">👷 Activité par contrôleur</span></div>
    <div class="card-body" style="padding:0;">
    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
        <thead><tr style="background:var(--vert-pale);">
            <th style="padding:10px 16px;text-align:left;">Contrôleur</th>
            <th style="padding:10px;text-align:center;">Total</th>
            <th style="padding:10px;text-align:center;">Validés</th>
        </tr></thead>
        <tbody>
        <?php foreach($par_ctrl as $c):?>
        <tr style="border-bottom:1px solid var(--gris-moyen);">
            <td style="padding:10px 16px;"><?=htmlspecialchars($c['prenom'].' '.$c['nom'])?></td>
            <td style="padding:10px;text-align:center;"><strong><?=$c['nb']?></strong></td>
            <td style="padding:10px;text-align:center;"><span class="badge badge-success"><?=$c['valides']?></span></td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    </div>
</div>

</div>

<!-- Fiches par type -->
<div class="card fade-in">
    <div class="card-header"><span class="card-title">📝 Fiches remplies par type</span></div>
    <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
    <?php foreach($fiches_stats as[$nom,$table]):
        $nb=$db->query("SELECT COUNT(*) FROM $table")->fetch_row()[0];
        $pct=$total_rapports>0?round($nb/$total_rapports*100):0;
    ?>
    <div style="background:var(--vert-pale);border-radius:var(--radius-sm);padding:14px;text-align:center;">
        <div style="font-size:1.4rem;font-weight:700;color:var(--vert-fonce);"><?=$nb?></div>
        <div style="font-size:0.78rem;color:var(--gris-texte);margin-bottom:6px;"><?=$nom?></div>
        <div style="height:5px;background:var(--gris-moyen);border-radius:3px;">
            <div style="height:100%;width:<?=$pct?>%;background:var(--vert-clair);border-radius:3px;"></div>
        </div>
        <div style="font-size:0.72rem;color:var(--vert-moyen);margin-top:4px;"><?=$pct?>% des rapports</div>
    </div>
    <?php endforeach;?>
    </div>
    </div>
</div>

<?php include '../includes/footer.php';?>
