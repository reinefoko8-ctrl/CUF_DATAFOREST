<?php
require_once '../includes/config.php';
requireLogin();
$db  = getDB();
$uid = $_SESSION['user_id'];
$rid = (int)($_GET['id'] ?? 0);
if (!$rid) { header('Location:../dashboard_controleur.php'); exit; }

$stmt = $db->prepare("SELECT r.*,u.nom,u.prenom,u.email FROM rapports r JOIN users u ON r.controleur_id=u.id WHERE r.id=?");
$stmt->bind_param('i', $rid); $stmt->execute();
$rapport = $stmt->get_result()->fetch_assoc();
if (!$rapport) { header('Location:../dashboard_controleur.php'); exit; }
if (!isAdmin() && $rapport['controleur_id'] != $uid) { header('Location:../dashboard_controleur.php'); exit; }

// Charger toutes les fiches
$parc        = $db->query("SELECT * FROM fiche_parc_foret WHERE rapport_id=$rid")->fetch_assoc();
$abattage    = $db->query("SELECT * FROM fiche_abattage WHERE rapport_id=$rid")->fetch_assoc();
$routes      = $db->query("SELECT * FROM fiche_routes_forestieres WHERE rapport_id=$rid")->fetch_assoc();
$tracabilite = $db->query("SELECT * FROM fiche_tracabilite_grumes WHERE rapport_id=$rid")->fetch_assoc();
$sec_tc      = $db->query("SELECT * FROM fiche_securite_tronconneuses WHERE rapport_id=$rid")->fetch_assoc();
$sortie      = $db->query("SELECT * FROM fiche_sortie_pieds WHERE rapport_id=$rid")->fetch_assoc();
$debardage   = $db->query("SELECT * FROM fiche_debardage WHERE rapport_id=$rid")->fetch_assoc();
$pont        = $db->query("SELECT * FROM fiche_pont_forestier WHERE rapport_id=$rid")->fetch_assoc();
$post_exp    = $db->query("SELECT * FROM fiche_post_exploitation WHERE rapport_id=$rid")->fetch_assoc();
$dechets     = $db->query("SELECT * FROM fiche_dechets_foret WHERE rapport_id=$rid")->fetch_assoc();
$base_mec    = $db->query("SELECT * FROM fiche_base_mecanique WHERE rapport_id=$rid")->fetch_assoc();

// ── Calcul dynamique des totaux depuis les critères individuels ──────────────
function calcTotal(array $fiche = null, array $cols, int $max): array {
    if (!$fiche) return ['score' => null, 'max' => $max, 'pct' => null, 'detail' => []];
    $score = 0; $applicable = 0; $detail = [];
    foreach ($cols as $col) {
        $val = $fiche[$col] ?? '';
        $detail[$col] = $val;
        if ($val === 'NA' || $val === '') continue;
        $applicable++;
        if (is_numeric($val)) $score += (float)$val;
    }
    $pct = $applicable > 0 ? round($score / $max * 100) : 0;
    return ['score' => $score, 'max' => $max, 'pct' => $pct, 'detail' => $detail];
}

$parc_total = calcTotal($parc,
    ['c1_installation','c2_panneau_matricule','c3_pente_douce','c4_distance_nappe',
     'c5_tiges_avenir','c6_marquage_grumes','c7_couche_debris','c8_culées_coin',
     'c9_coursons','c10_marques_ab'], 10);

$routes_total = calcTotal($routes,
    ['c1','c2','c3','c4','c5','c6','c7','c8','c9','c10'], 10);

$sortie_total = calcTotal($sortie,
    ['c1','c2','c3','c4','c5','c6','c7','c8','c9','c10'], 10);

$debardage_total = calcTotal($debardage,
    ['c1','c2','c3','c4','c5','c6','c7','c8','c9','c10'], 10);

$pont_total = calcTotal($pont,
    ['c1','c2','c3','c4','c5','c6','c7','c8','c9','c10'], 10);

$dechets_total = calcTotal($dechets,
    ['bac_nettoyage','decanteur','poubelle_non_biodeg','huiles_usees','filtres',
     'batteries','cables_debardage','absence_huiles','absence_plastiques',
     'transfert_dechets','transfert_contenants','sensibilisation','consignes_respectees'], 20);

// Abattage : calcul depuis critères pied 1
$abattage_total = calcTotal($abattage,
    ['p1_c1_piste_fuite_direction','p1_c1_nettoyage','p1_c1_longueur_piste','p1_c1_largeur_piste',
     'p1_c2_egobelage','p1_c3_hauteur_souche',
     'p1_c4_entaille_1er_trait','p1_c4_entaille_2eme_trait','p1_c4_02_traits','p1_c4_semelle',
     'p1_c5_charniere_longue','p1_c5_largeur_charniere','p1_c5_epaulement',
     'p1_c6_coupe_abattage','p1_c7_patte_retenue','p1_c7_taille_patte',
     'p1_c8_aubiers','p1_c9_direction_chute',
     'p1_c10_tronconnage','p1_c10_etelage','p1_c10_ecuage',
     'p1_c11_marquage_souche','p1_c11_defaut_apparent'], 15);

// Helper fonctions
function scoreLabel($val) {
    if ($val === '1') return '<span style="color:#2e7d32;font-weight:700;">✓ Conforme</span>';
    if ($val === '0') return '<span style="color:#c62828;font-weight:700;">✗ Non conforme</span>';
    if ($val === '0.5') return '<span style="color:#e65100;font-weight:700;">½ Partiel</span>';
    if ($val === 'NA') return '<span style="color:#777;">NA</span>';
    return '<span style="color:#aaa;">—</span>';
}
function scoreBadge($val) {
    if ($val === '1')   return '<span style="background:#e8f5e9;color:#2e7d32;padding:1px 6px;border-radius:8px;font-weight:700;">1</span>';
    if ($val === '0.5') return '<span style="background:#fff3e0;color:#e65100;padding:1px 6px;border-radius:8px;font-weight:700;">0.5</span>';
    if ($val === '0')   return '<span style="background:#ffebee;color:#c62828;padding:1px 6px;border-radius:8px;font-weight:700;">0</span>';
    if ($val === 'NA')  return '<span style="color:#999;font-size:9px;">NA</span>';
    return '<span style="color:#ccc;">—</span>';
}
function pctColor($pct) {
    if ($pct === null) return '#aaa';
    if ($pct >= 70) return '#2e7d32';
    if ($pct >= 40) return '#e65100';
    return '#c62828';
}
function formatDate($d) {
    if (!$d) return '—';
    return date('d/m/Y', strtotime($d));
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<title>Rapport CUF DataForest #<?= $rid ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Arial',sans-serif; font-size:11px; color:#1a1a1a; background:#f0f0f0; }
.page {
    background:white; width:210mm; margin:8mm auto; padding:14mm 16mm;
    box-shadow:0 2px 12px rgba(0,0,0,0.15); page-break-after:always;
}
.page:last-child { page-break-after:auto; }
.header { display:flex; align-items:center; gap:14px; border-bottom:3px solid #1a4a1a; padding-bottom:10px; margin-bottom:14px; }
.header img { width:50px; height:50px; object-fit:contain; }
.site-name { font-size:16px; font-weight:700; color:#1a4a1a; }
.fiche-name { font-size:11px; color:#2d7a2d; font-weight:600; margin-top:2px; }
.version { font-size:8px; color:#888; margin-top:2px; }
.info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:12px; }
.info-box { border:1px solid #ddd; padding:5px 8px; border-radius:4px; }
.info-label { font-size:7px; font-weight:700; color:#2d7a2d; text-transform:uppercase; letter-spacing:0.5px; }
.info-val { font-size:10px; font-weight:600; }
.section-title {
    background:#1a4a1a; color:white; padding:5px 10px;
    font-size:10px; font-weight:700; border-radius:3px 3px 0 0; margin-top:12px;
}
table.eval { width:100%; border-collapse:collapse; font-size:9px; }
table.eval th { background:#2d7a2d; color:white; padding:5px 8px; text-align:left; }
table.eval td { padding:5px 8px; border-bottom:1px solid #eee; vertical-align:middle; }
table.eval tr:nth-child(even) td { background:#fafafa; }
.total-row td { background:#e8f5e9 !important; font-weight:700; font-size:10px; }
.obs-box { border:1px solid #ddd; padding:8px; margin-top:8px; border-radius:4px; min-height:35px; font-size:9px; color:#333; }
.obs-label { font-size:8px; font-weight:700; color:#2d7a2d; text-transform:uppercase; margin-top:8px; margin-bottom:3px; }
.sig-box { display:flex; justify-content:space-between; margin-top:18px; padding-top:10px; border-top:1px solid #ddd; }
.sig-area { text-align:center; font-size:8px; color:#777; }
.sig-line { border-bottom:1px solid #aaa; width:110px; margin:25px auto 3px; }
.recap-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:10px; }
.recap-item { border:1px solid #ddd; padding:8px 10px; border-radius:4px; }
.recap-nom { font-size:8px; color:#555; margin-bottom:3px; }
.recap-score { font-size:18px; font-weight:700; line-height:1; }
.recap-pct { font-size:8px; font-weight:600; margin-top:2px; }
.recap-vide { font-size:8px; color:#aaa; font-style:italic; }
.progress { height:5px; background:#eee; border-radius:3px; margin-top:4px; overflow:hidden; }
.progress-bar { height:100%; border-radius:3px; }
.print-btn {
    position:fixed; bottom:20px; right:20px;
    background:#1a4a1a; color:white; border:none;
    padding:12px 24px; border-radius:50px;
    font-size:13px; font-weight:700; cursor:pointer;
    box-shadow:0 4px 16px rgba(0,0,0,0.3); z-index:999;
}
@media print {
    body { background:white; }
    .page { box-shadow:none; margin:0; }
    .print-btn { display:none !important; }
}
</style>
</head>
<body>
<button class="print-btn" onclick="window.print()">🖨️ Imprimer / PDF</button>

<!-- ════════════════════════════════════════════
     PAGE DE GARDE
════════════════════════════════════════════ -->
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">RAPPORT D'ÉVALUATION DES ACTIVITÉS FORESTIÈRES</div>
            <div class="version">Cameroon United Forests — Généré le <?= date('d/m/Y à H:i') ?></div>
        </div>
    </div>

    <!-- Titre rapport -->
    <div style="background:#e8f5e9;border:2px solid #2d7a2d;border-radius:6px;padding:16px;text-align:center;margin-bottom:14px;">
        <div style="font-size:20px;font-weight:700;color:#1a4a1a;">Rapport #<?= $rid ?></div>
        <div style="font-size:12px;color:#2d7a2d;margin-top:3px;"><?= htmlspecialchars($rapport['titre'] ?? 'Rapport d\'évaluation') ?></div>
        <?php
        $bcolors = ['brouillon'=>'#ff9800','soumis'=>'#1976d2','validé'=>'#2e7d32','rejeté'=>'#c62828'];
        $bc = $bcolors[$rapport['statut']] ?? '#777';
        ?>
        <div style="margin-top:8px;display:inline-block;padding:3px 14px;border-radius:20px;background:<?= $bc ?>;color:white;font-size:9px;font-weight:700;">
            <?= strtoupper($rapport['statut']) ?>
        </div>
    </div>

    <!-- Infos générales -->
    <div class="info-grid">
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($rapport['prenom'].' '.$rapport['nom']) ?></div></div>
        <div class="info-box"><div class="info-label">Titre forestier</div><div class="info-val"><?= htmlspecialchars($rapport['titre_forestier'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($rapport['aac'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Date du rapport</div><div class="info-val"><?= formatDate($rapport['date_rapport']) ?></div></div>
        <div class="info-box"><div class="info-label">Date de soumission</div><div class="info-val"><?= formatDate($rapport['date_soumission']) ?></div></div>
        <div class="info-box"><div class="info-label">Statut</div><div class="info-val"><?= ucfirst($rapport['statut']) ?></div></div>
    </div>

    <!-- Récapitulatif des scores -->
    <div class="section-title">📊 RÉCAPITULATIF DES SCORES</div>
    <div class="recap-grid">
    <?php
    $recap_fiches = [
        ['Parc Forêt',          $parc_total,      $parc],
        ['Abattage contrôlé',   $abattage_total,  $abattage],
        ['Routes forestières',  $routes_total,    $routes],
        ['Sortie pieds',        $sortie_total,    $sortie],
        ['Débardage',           $debardage_total, $debardage],
        ['Pont forestier',      $pont_total,      $pont],
        ['Déchets en forêt',    $dechets_total,   $dechets],
        ['Traçabilité grumes',  null,             $tracabilite],
        ['Sécurité tronçonneuses', null,          $sec_tc],
        ['Post exploitation',   null,             $post_exp],
        ['Base mécanique',      null,             $base_mec],
    ];
    foreach ($recap_fiches as [$nom, $tot, $fiche]):
        $color = $tot ? pctColor($tot['pct']) : '#aaa';
    ?>
    <div class="recap-item">
        <div class="recap-nom"><?= $nom ?></div>
        <?php if ($fiche): ?>
            <?php if ($tot): ?>
                <div class="recap-score" style="color:<?= $color ?>"><?= number_format($tot['score'],1) ?>/<?= $tot['max'] ?></div>
                <div class="recap-pct" style="color:<?= $color ?>"><?= $tot['pct'] ?>%</div>
                <div class="progress"><div class="progress-bar" style="width:<?= $tot['pct'] ?>%;background:<?= $color ?>;"></div></div>
            <?php else: ?>
                <div class="recap-score" style="color:#2e7d32;font-size:12px;">✅ Remplie</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="recap-vide">Non remplie</div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>

    <?php if ($rapport['avis_global']): ?>
    <div class="obs-label">Avis global du contrôleur</div>
    <div class="obs-box"><?= nl2br(htmlspecialchars($rapport['avis_global'])) ?></div>
    <?php endif; ?>
    <?php if ($rapport['commentaire_admin']): ?>
    <div class="obs-label" style="color:#1976d2;">Commentaire de l'administrateur</div>
    <div class="obs-box" style="border-color:#1976d2;"><?= nl2br(htmlspecialchars($rapport['commentaire_admin'])) ?></div>
    <?php endif; ?>

    <div class="sig-box">
        <div class="sig-area"><div class="sig-line"></div>Signature du contrôleur<br/><?= htmlspecialchars($rapport['prenom'].' '.$rapport['nom']) ?></div>
        <div class="sig-area"><div class="sig-line"></div>Cachet &amp; Signature<br/>Administrateur CUF</div>
    </div>
</div>

<!-- ════════════════════════════════════════════
     FICHE 1 : PARC FORÊT
════════════════════════════════════════════ -->
<?php if ($parc): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Parc Forêt"</div>
            <div class="version">Version 03 du 01/04/2026</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">Référence TF</div><div class="info-val"><?= htmlspecialchars($parc['reference_parc_foret'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($parc['nom_controleur'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Date contrôle</div><div class="info-val"><?= formatDate($parc['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">Longitude</div><div class="info-val"><?= htmlspecialchars($parc['longitude'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Latitude</div><div class="info-val"><?= htmlspecialchars($parc['latitude'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Superficie (m²)</div><div class="info-val"><?= htmlspecialchars($parc['superficie_parc'] ?? '—') ?></div></div>
    </div>
    <div class="section-title">Critères d'évaluation</div>
    <table class="eval">
        <thead><tr><th>N°</th><th>Critère</th><th style="width:100px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php $parc_crit = [
            ['c1_installation',     'Installation du parc respecte l\'emplacement initial'],
            ['c2_panneau_matricule','Panneau d\'immatriculation conforme'],
            ['c3_pente_douce',      'Parc à plus de 30 m d\'un plan d\'eau'],
            ['c4_distance_nappe',   'Pente douce permettant le drainage des eaux'],
            ['c5_tiges_avenir',     'Distance entre 2 parcs ≥ 250 m, superficie < 2 000 m²'],
            ['c6_marquage_grumes',  'Tiges d\'avenir identifiées et maîtrisées'],
            ['c7_couche_debris',    'Marquage des grumes lisible et correct'],
            ['c8_culées_coin',      'Débris entreposés dans un coin pour réutilisation'],
            ['c9_coursons',         'Culées dans un coin pour réutilisation'],
            ['c10_marques_ab',      'Coursons abandonnés marqués AB avec N°DF10'],
        ];
        foreach ($parc_crit as $i => [$col, $lbl]):
            $v = $parc[$col] ?? '';
        ?>
        <tr>
            <td style="font-weight:700;color:#2d7a2d;"><?= $i+1 ?></td>
            <td><?= $lbl ?></td>
            <td style="text-align:center;"><?= scoreBadge($v) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td style="text-align:center;color:<?= pctColor($parc_total['pct']) ?>">
                <?= number_format($parc_total['score'],1) ?>/10 (<?= $parc_total['pct'] ?>%)
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($parc['observations'])): ?>
    <div class="obs-label">Observations</div>
    <div class="obs-box"><?= nl2br(htmlspecialchars($parc['observations'])) ?></div>
    <?php endif; ?>
    <?php if (!empty($parc['appreciation'])): ?>
    <div class="obs-label">Appréciation</div>
    <div class="obs-box"><?= nl2br(htmlspecialchars($parc['appreciation'])) ?></div>
    <?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 2 : ABATTAGE CONTRÔLÉ
════════════════════════════════════════════ -->
<?php if ($abattage): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Opérations d'abattage contrôlé"</div>
            <div class="version">Version 02 du 01/04/2026</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($abattage['nom_controleur'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Abatteur</div><div class="info-val"><?= htmlspecialchars($abattage['nom_abatteur'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($abattage['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($abattage['titre_forestier'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($abattage['aac'] ?? '—') ?></div></div>
        <div class="info-box"><div class="info-label">UC</div><div class="info-val"><?= htmlspecialchars($abattage['uc'] ?? '—') ?></div></div>
    </div>

    <!-- Pieds contrôlés -->
    <div class="section-title">Pieds contrôlés</div>
    <table class="eval">
        <thead><tr><th>Pied</th><th>N° Code barre</th><th>N° DF10</th><th>Essence</th><th>Score /15</th></tr></thead>
        <tbody>
        <?php for ($p=1; $p<=5; $p++):
            $cb = $abattage["p{$p}_num_code_barre"] ?? '';
            $df = $abattage["p{$p}_num_df10"] ?? '';
            $es = $abattage["p{$p}_essence"] ?? '';
            $tt = $p === 1 ? number_format($abattage_total['score'],1) : ($abattage["p{$p}_total"] ?? '');
            if (!$cb && !$df && !$es) continue;
        ?>
        <tr>
            <td style="font-weight:700;">Pied <?= $p ?></td>
            <td><?= htmlspecialchars($cb) ?></td>
            <td><?= htmlspecialchars($df) ?></td>
            <td><?= htmlspecialchars($es) ?></td>
            <td style="font-weight:700;color:<?= pctColor($p===1?$abattage_total['pct']:null) ?>">
                <?= $tt ? $tt.'/15' : '—' ?>
            </td>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <!-- Critères pied 1 -->
    <div class="section-title" style="margin-top:8px;">Détail critères — Pied 1</div>
    <table class="eval">
        <thead><tr><th>Catégorie</th><th>Critère</th><th style="width:60px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php $ab_crit = [
            ['1 - Piste de fuite', 'p1_c1_piste_fuite_direction', 'Direction piste de fuite ±15°'],
            ['', 'p1_c1_nettoyage', 'Nettoyage piste et pourtour de l\'arbre'],
            ['', 'p1_c1_longueur_piste', 'Longueur piste ≥ 15 m'],
            ['', 'p1_c1_largeur_piste', 'Largeur piste 1 à 1,5 m, rayon 2 m'],
            ['2 - Égobelage', 'p1_c2_egobelage', 'Égobelage du côté de l\'entaille'],
            ['3 - Hauteur souche', 'p1_c3_hauteur_souche', 'Souche la plus basse possible'],
            ['4 - Entaille', 'p1_c4_entaille_1er_trait', '1er trait horizontal profond (1/5e diamètre)'],
            ['', 'p1_c4_entaille_2eme_trait', '2ème trait à 45°'],
            ['', 'p1_c4_02_traits', '2 traits se rejoignent en ligne droite'],
            ['', 'p1_c4_semelle', 'Semelle amorcée (chanfrein)'],
            ['5 - Charnière', 'p1_c5_charniere_longue', 'Charnière longue et régulière (1/10e diamètre)'],
            ['', 'p1_c5_largeur_charniere', 'Largeur ≈ 4 doigts'],
            ['', 'p1_c5_epaulement', 'Épaulement conforme (6-10 cm)'],
            ['6 - Coupe', 'p1_c6_coupe_abattage', 'Coupe uniforme sans arrache'],
            ['7 - Pattes retenue', 'p1_c7_patte_retenue', 'Patte(s) de retenue présente(s)'],
            ['', 'p1_c7_taille_patte', 'Taille suffisante pour la sécurité'],
            ['8 - Aubiers', 'p1_c8_aubiers', 'Aubiers coupés des deux côtés'],
            ['9 - Direction', 'p1_c9_direction_chute', 'Arbre tombé dans bonne direction'],
            ['10 - Tronçonnage', 'p1_c10_tronconnage', 'Tronçonnage sans défaut'],
            ['', 'p1_c10_etelage', 'Étêtage à moins de 1 m de la 1ère branche'],
            ['', 'p1_c10_ecuage', 'Écuage justifié'],
            ['11 - Valorisation', 'p1_c11_marquage_souche', 'Numéro abatteur et UC marqués sur souche'],
            ['', 'p1_c11_defaut_apparent', 'Pas de défaut apparent sur le fût'],
        ];
        foreach ($ab_crit as [$cat, $col, $lbl]):
            $v = $abattage[$col] ?? '';
        ?>
        <tr>
            <td style="font-size:8px;font-weight:600;color:#2d7a2d;white-space:nowrap;"><?= $cat ?></td>
            <td style="font-size:8.5px;"><?= $lbl ?></td>
            <td style="text-align:center;"><?= scoreBadge($v) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL PIED 1</td>
            <td style="text-align:center;color:<?= pctColor($abattage_total['pct']) ?>">
                <?= number_format($abattage_total['score'],1) ?>/15
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($abattage['observations'])): ?>
    <div class="obs-label">Observations</div>
    <div class="obs-box"><?= nl2br(htmlspecialchars($abattage['observations'])) ?></div>
    <?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 3 : ROUTES FORESTIÈRES
════════════════════════════════════════════ -->
<?php if ($routes): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Routes Forestières"</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($routes['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($routes['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($routes['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($routes['date_controle']) ?></div></div>
        <div class="info-box" style="grid-column:span 2"><div class="info-label">Caractéristiques tronçon</div><div class="info-val"><?= htmlspecialchars($routes['caracteristiques_troncon']??'—') ?></div></div>
    </div>
    <div class="section-title">Critères d'évaluation</div>
    <table class="eval">
        <thead><tr><th>N°</th><th>Critère</th><th style="width:80px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php $routes_crit = [
            ['c1','Tracé initial respecté selon le plan de gestion'],
            ['c2','Largeur conforme (7-9 m)'],
            ['c3','Fossés de drainage fonctionnels et entretenus'],
            ['c4','Ouvrages de franchissement (ponceaux) en bon état'],
            ['c5','Zones riveraines (30 m) protégées'],
            ['c6','Pentes traitées contre l\'érosion'],
            ['c7','Signalisation présente et lisible'],
            ['c8','Accès zones interdites fermés'],
            ['c9','Routes abandonnées correctement fermées'],
            ['c10','Entretien régulier effectif'],
        ];
        foreach ($routes_crit as $i => [$col, $lbl]):
            $v = $routes[$col] ?? '';
        ?>
        <tr>
            <td style="font-weight:700;color:#2d7a2d;"><?= $i+1 ?></td>
            <td><?= $lbl ?></td>
            <td style="text-align:center;"><?= scoreBadge($v) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td style="text-align:center;color:<?= pctColor($routes_total['pct']) ?>">
                <?= number_format($routes_total['score'],1) ?>/10 (<?= $routes_total['pct'] ?>%)
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($routes['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($routes['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 4 : TRAÇABILITÉ GRUMES
════════════════════════════════════════════ -->
<?php if ($tracabilite): $grumes = json_decode($tracabilite['grume_json'] ?? '[]', true) ?? []; ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Traçabilité forêt grumes"</div>
            <div class="version">Version 02 du 01/04/2026</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($tracabilite['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($tracabilite['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">Nb grumes</div><div class="info-val"><?= count($grumes) ?></div></div>
    </div>
    <div class="section-title">Registre des grumes</div>
    <table class="eval">
        <thead><tr><th>N°</th><th>Essence</th><th>N° DF10</th><th>Code barre</th><th>Date abattage</th><th>Volume (m³)</th><th>Ø PB</th><th>Ø GB</th><th>Long</th><th>Affectation</th></tr></thead>
        <tbody>
        <?php foreach ($grumes as $i => $g): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($g['essence']??'') ?></td>
            <td><?= htmlspecialchars($g['num_df10']??'') ?></td>
            <td><?= htmlspecialchars($g['code_barre']??'') ?></td>
            <td><?= htmlspecialchars($g['date_abattage']??'') ?></td>
            <td><?= htmlspecialchars($g['volume']??'') ?></td>
            <td><?= htmlspecialchars($g['diam_pb']??'') ?></td>
            <td><?= htmlspecialchars($g['diam_gb']??'') ?></td>
            <td><?= htmlspecialchars($g['long']??'') ?></td>
            <td><?= htmlspecialchars($g['affectation']??'') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($grumes)): ?><tr><td colspan="10" style="text-align:center;color:#aaa;">Aucune grume enregistrée</td></tr><?php endif; ?>
        </tbody>
    </table>
    <?php if (!empty($tracabilite['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($tracabilite['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 5 : SÉCURITÉ TRONÇONNEUSES
════════════════════════════════════════════ -->
<?php if ($sec_tc): $tc_rows = json_decode($sec_tc['tc_json'] ?? '[]', true) ?? []; ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Éléments de sécurité des tronçonneuses"</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($sec_tc['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($sec_tc['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($sec_tc['date_controle']) ?></div></div>
    </div>
    <div class="section-title">Éléments de sécurité par tronçonneuse</div>
    <table class="eval">
        <thead><tr><th>N° Scie</th><th>Protège main</th><th>Frein chaîne</th><th>Ergot fouet</th><th>Double gâchette</th><th>Silentbloc</th><th>Poignée</th><th>Bouton arrêt</th><th>Autres</th></tr></thead>
        <tbody>
        <?php foreach ($tc_rows as $tc): ?>
        <tr>
            <td style="font-weight:700;"><?= htmlspecialchars($tc['num_serie']??'—') ?></td>
            <?php foreach (['e1','e2','e3','e4','e5','e6','e7','e8'] as $e): ?>
            <td style="text-align:center;"><?= ($tc[$e]??'')==='1' ? '✓' : (($tc[$e]??'')==='0' ? '✗' : '—') ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 6 : SORTIE PIEDS
════════════════════════════════════════════ -->
<?php if ($sortie): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Sortie pieds"</div>
            <div class="version">Version 01, du 23/09/2025</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($sortie['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($sortie['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($sortie['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($sortie['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">Parc forêt planifié n°</div><div class="info-val"><?= htmlspecialchars($sortie['parc_foret_planifie']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Tiges avenir mat. / non mat.</div><div class="info-val"><?= htmlspecialchars($sortie['nb_tiges_avenir_materialisees']??'—') ?> / <?= htmlspecialchars($sortie['nb_tiges_avenir_non_materialisees']??'—') ?></div></div>
    </div>
    <div class="section-title">Critères d'évaluation</div>
    <table class="eval">
        <thead><tr><th>N°</th><th>Critère</th><th style="width:80px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php $sortie_crit = [
            ['c1','Arbres triés et pistés exploitables dans zone autorisée'],
            ['c2','Motif d\'abandon justifié pour chaque arbre abandonné'],
            ['c3','Carte de sortie pieds intègre le projet route, mise à jour quotidiennement'],
            ['c4','Matérialisation des pistes conforme (largeur > 1 m, hauteur > 2 m)'],
            ['c5','Tracé secondaire en arêtes de poisson sur piste principale'],
            ['c6','Tracé pistes sur terrain respecte projet initial'],
            ['c7','Jalons portent mention exacte du nombre de pieds'],
            ['c8','Tiges d\'avenir, semenciers, arbres interdits matérialisés'],
            ['c9','Arbres patrimoniaux et sites d\'intérêt matérialisés'],
            ['c10','Projet de parc matérialisé avec jalon et nombre de pieds'],
        ];
        foreach ($sortie_crit as $i => [$col, $lbl]):
            $v = $sortie[$col] ?? '';
        ?>
        <tr>
            <td style="font-weight:700;color:#2d7a2d;"><?= $i+1 ?></td>
            <td><?= $lbl ?></td>
            <td style="text-align:center;"><?= scoreBadge($v) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td style="text-align:center;color:<?= pctColor($sortie_total['pct']) ?>">
                <?= number_format($sortie_total['score'],1) ?>/10 (<?= $sortie_total['pct'] ?>%)
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($sortie['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($sortie['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 7 : DÉBARDAGE
════════════════════════════════════════════ -->
<?php if ($debardage): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Débardage"</div>
            <div class="version">Version 02, du 23/09/2025</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($debardage['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($debardage['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($debardage['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($debardage['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">UC</div><div class="info-val"><?= htmlspecialchars($debardage['uc']??'—') ?></div></div>
    </div>
    <div class="section-title">Critères d'évaluation</div>
    <table class="eval">
        <thead><tr><th>N°</th><th>Critère</th><th style="width:80px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php $deb_crit = [
            ['c1','Piste de débardage respecte le tracé initial (layons machette)'],
            ['c2','Pistes secondaires desservent un pied abattu valorisable'],
            ['c3','Jonction pistes principale/secondaire forme angle obtu (arête de poisson)'],
            ['c4','Cours d\'eau et ravines éloignés d\'au moins 30 m'],
            ['c5','Largeur max 5 m (principale) et 4 m (secondaire) respectée'],
            ['c6','Aucune double piste pour un même pied abattu'],
            ['c7','Tiges d\'avenir identifiées et protégées'],
            ['c8','Tronçonnage forêt effectif et bien réalisé'],
            ['c9','Purges réalisées sur les billes abandonnées en forêt'],
            ['c10','Culées et houppiers portent N°DF10 + ligne + date'],
        ];
        foreach ($deb_crit as $i => [$col, $lbl]):
            $v = $debardage[$col] ?? '';
        ?>
        <tr>
            <td style="font-weight:700;color:#2d7a2d;"><?= $i+1 ?></td>
            <td><?= $lbl ?></td>
            <td style="text-align:center;"><?= scoreBadge($v) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td style="text-align:center;color:<?= pctColor($debardage_total['pct']) ?>">
                <?= number_format($debardage_total['score'],1) ?>/10 (<?= $debardage_total['pct'] ?>%)
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($debardage['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($debardage['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 8 : PONT FORESTIER
════════════════════════════════════════════ -->
<?php if ($pont): $pont_info = json_decode($pont['ponts_json'] ?? '{}', true) ?? []; ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Construction d'un pont forestier"</div>
            <div class="version">Version 02, du 23/09/2025</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($pont['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($pont['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($pont['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($pont['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">Pont n°</div><div class="info-val"><?= htmlspecialchars($pont_info['pont_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Dimensions (L×l)</div><div class="info-val"><?= htmlspecialchars(($pont_info['longueur_pont']??'—').' × '.($pont_info['largeur_pont']??'—').' m') ?></div></div>
    </div>
    <div class="section-title">Critères d'évaluation</div>
    <table class="eval">
        <thead><tr><th>N°</th><th>Critère</th><th style="width:80px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php $pont_crit = [
            ['c1','Construction réduit largeur lit du cours d\'eau de moins de 20%'],
            ['c2','Extrémité du ponceau dépasse la base du remblai'],
            ['c3','Longrines de stabilisation sur terre ferme'],
            ['c4','Stabilisation lit cours d\'eau sans obstruer passage des poissons'],
            ['c5','Zones en bordure (20 m) protégées de tout terrassement'],
            ['c6','Billes de rétention de la terre prévues'],
            ['c7','Tablier pont ≥ 1,5 m au-dessus des hautes eaux (si navigable)'],
            ['c8','Pont positionné sur une ligne droite'],
            ['c9','Lit du cours d\'eau dégagé de tout obstacle'],
            ['c10','Essences commerciales utilisées martelées et déclarées sur DF10'],
        ];
        foreach ($pont_crit as $i => [$col, $lbl]):
            $v = $pont[$col] ?? '';
        ?>
        <tr>
            <td style="font-weight:700;color:#2d7a2d;"><?= $i+1 ?></td>
            <td><?= $lbl ?></td>
            <td style="text-align:center;"><?= scoreBadge($v) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td style="text-align:center;color:<?= pctColor($pont_total['pct']) ?>">
                <?= number_format($pont_total['score'],1) ?>/10 (<?= $pont_total['pct'] ?>%)
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($pont['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($pont['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 9 : POST EXPLOITATION
════════════════════════════════════════════ -->
<?php if ($post_exp): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Opérations post exploitation"</div>
            <div class="version">Version 03, du 01/04/2026</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($post_exp['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($post_exp['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($post_exp['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($post_exp['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">Cotation</div><div class="info-val"><?= htmlspecialchars($post_exp['cotation']??'—') ?></div></div>
    </div>
    <?php
    $pe_sections = [
        '1 - Base mécanique' => [
            ['base_mec_demontee','Base démantèlée et nettoyée'],
            ['base_mec_entretenue','Base opérationnelle et entretenue'],
        ],
        '2 - Martelage N° DF10' => [
            ['mart_souches_100pct','100% des souches retrouvées sur le terrain'],
            ['mart_souches_carte','100% des souches repérées sur carte'],
            ['mart_houppiers','100% des houppiers retrouvés sur le terrain'],
        ],
        '3 - Empiètements des limites' => [
            ['empiete_ufa','UFA'],['empiete_aac','AAC'],
            ['empiete_zones_prot','Zones de protection'],
            ['empiete_zones_interet','Zones d\'intérêt particulier'],
        ],
        '4 - Parcs forêt' => [
            ['parcs_superficie','Superficie parcs conforme (< 2 000 m²)'],
            ['dechets_geres','Déchets gérés conformément'],
            ['culees_non_marteles','Culées non martelées (absence)'],
            ['grumes_abandonnees','Grumes commercialisables abandonnées (absence)'],
            ['restauration_parcs','Restauration/reboisement des parcs effectif'],
            ['parcs_cartographies','Parcs cartographiés et matérialisés'],
        ],
        '5 - Ouvrages franchissement' => [
            ['remise_etat_lit','Remise en état du lit du cours d\'eau'],
            ['ouvrages_demantelés','Ouvrages non réutilisables démantèlés'],
            ['ouvrages_cartographies','Ouvrages cartographiés'],
        ],
        '6 - Fermeture des pistes' => [
            ['fermeture_secondaire','Pistes secondaires fermées/bloquées'],
            ['fermeture_principale','Pistes principales fermées/bloquées'],
            ['barriere','Accès par barrière'],
            ['pistes_conformes','Pistes conformes aux procédures'],
            ['routes_cartographiees','Routes cartographiées'],
        ],
    ];
    foreach ($pe_sections as $titre => $items):
    ?>
    <div class="section-title" style="margin-top:8px;"><?= $titre ?></div>
    <table class="eval">
        <thead><tr><th>Critère</th><th style="width:80px;text-align:center;">Résultat</th></tr></thead>
        <tbody>
        <?php foreach ($items as [$col, $lbl]): $v = $post_exp[$col] ?? ''; ?>
        <tr>
            <td><?= $lbl ?></td>
            <td style="text-align:center;font-weight:700;color:<?= $v==='Oui'?'#2e7d32':($v==='Non'?'#c62828':'#aaa') ?>"><?= $v ?: '—' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>
    <?php if (!empty($post_exp['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($post_exp['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 10 : DÉCHETS EN FORÊT
════════════════════════════════════════════ -->
<?php if ($dechets): ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Gestion des déchets en forêt"</div>
            <div class="version">Version 02 du 23/09/2025</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($dechets['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($dechets['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($dechets['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($dechets['date_controle']) ?></div></div>
    </div>
    <div class="section-title">Points de contrôle</div>
    <table class="eval">
        <thead><tr><th>Catégorie</th><th>Point de contrôle</th><th style="width:60px;text-align:center;">Cot.</th><th style="width:40px;text-align:center;">Max</th></tr></thead>
        <tbody>
        <?php $dec_pts = [
            ['Outils gestion','bac_nettoyage','Bac de nettoyage des pièces détachées disponible',1],
            ['','decanteur','Décanteur pour filtres usagers disponible',1],
            ['','poubelle_non_biodeg','Poubelle pour déchets non-biodégradables disponible',1],
            ['Collecte/stockage','huiles_usees','Huiles usées bien stockées',2],
            ['','filtres','Filtres usés bien stockés',1],
            ['','batteries','Batteries et piles usées bien stockées',1],
            ['','cables_debardage','Câbles de débardage usés collectés et stockés',2],
            ['','absence_huiles','Absence de traces d\'huiles dans zones mécaniques',1],
            ['','absence_plastiques','Absence de déchets plastiques/câbles abandonnés',2],
            ['Transfert','transfert_dechets','Transfert des déchets dangereux effectué',1],
            ['','transfert_contenants','Transfert dans contenants étanches',1],
            ['Sensibilisation','sensibilisation','Travailleurs sensibilisés aux consignes',3],
            ['','consignes_respectees','Consignes respectées par le personnel',3],
        ];
        foreach ($dec_pts as [$cat, $col, $lbl, $max]):
            $v = $dechets[$col] ?? '';
            $numv = is_numeric($v) ? (float)$v : 0;
        ?>
        <tr>
            <td style="font-size:8px;font-weight:600;color:#2d7a2d;"><?= $cat ?></td>
            <td><?= $lbl ?></td>
            <td style="text-align:center;font-weight:700;color:<?= $numv>0?'#2e7d32':($v===''?'#aaa':'#c62828') ?>"><?= $v !== '' ? $v : '—' ?></td>
            <td style="text-align:center;color:#aaa;"><?= $max ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td style="text-align:center;color:<?= pctColor($dechets_total['pct']) ?>"><?= number_format($dechets_total['score'],1) ?></td>
            <td style="text-align:center;">20</td>
        </tr>
        </tbody>
    </table>
    <?php if (!empty($dechets['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($dechets['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════
     FICHE 11 : BASE MÉCANIQUE
════════════════════════════════════════════ -->
<?php if ($base_mec): $details = json_decode($base_mec['details_json'] ?? '{}', true) ?? []; ?>
<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/images/logo.png" alt="CUF"/>
        <div>
            <div class="site-name">CUF DataForest</div>
            <div class="fiche-name">FICHE DE CONTRÔLE — "Base mécanique forêt"</div>
            <div class="version">Version 02, du 01/04/2026</div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-box"><div class="info-label">TF</div><div class="info-val"><?= htmlspecialchars($base_mec['titre_forestier']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">AAC</div><div class="info-val"><?= htmlspecialchars($base_mec['aac']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Contrôleur</div><div class="info-val"><?= htmlspecialchars($base_mec['nom_controleur']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Date</div><div class="info-val"><?= formatDate($base_mec['date_controle']) ?></div></div>
        <div class="info-box"><div class="info-label">Longitude</div><div class="info-val"><?= htmlspecialchars($base_mec['longitude']??'—') ?></div></div>
        <div class="info-box"><div class="info-label">Latitude</div><div class="info-val"><?= htmlspecialchars($base_mec['latitude']??'—') ?></div></div>
    </div>
    <?php
    $bm_sections = [
        '1 - Caractéristiques de la base' => [
            ['point_retention_fonctionnel','Point de rétention fonctionnel'],
        ],
        '2 - Mesures contre déversements accidentels' => [
            ['equip_securite_disponible','Équipement de sécurité disponible'],
            ['equip_conforme','Conforme et fonctionnel'],
            ['equip_signale','Signalé/accessible'],
            ['equip_accessible','Disponible'],
            ['equip_visite','Visité/entretenu'],
            ['consignes_securite','Consignes de sécurité affichées'],
        ],
    ];
    foreach ($bm_sections as $titre => $items):
    ?>
    <div class="section-title" style="margin-top:8px;"><?= $titre ?></div>
    <table class="eval">
        <thead><tr><th>Critère</th><th style="width:80px;text-align:center;">Résultat</th></tr></thead>
        <tbody>
        <?php foreach ($items as [$col, $lbl]): $v = $base_mec[$col] ?? ''; ?>
        <tr>
            <td><?= $lbl ?></td>
            <td style="text-align:center;font-weight:700;color:<?= $v==='Oui'?'#2e7d32':($v==='Non'?'#c62828':'#aaa') ?>"><?= $v ?: '—' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>
    <!-- Cuve hydrocarbures -->
    <div class="section-title" style="margin-top:8px;">Cuve d'hydrocarbures</div>
    <table class="eval">
        <tbody>
        <tr><td>Volume</td><td><?= htmlspecialchars($base_mec['cuve_volume']??'—') ?></td><td>Contenu</td><td><?= htmlspecialchars($base_mec['cuve_contenu']??'—') ?></td></tr>
        <tr><td>Fabricant</td><td><?= htmlspecialchars($base_mec['cuve_nom_fabricant']??'—') ?></td><td>Homologuée</td><td style="font-weight:700;color:<?= ($base_mec['cuve_homologuee']??'')==='Oui'?'#2e7d32':'#c62828' ?>"><?= htmlspecialchars($base_mec['cuve_homologuee']??'—') ?></td></tr>
        </tbody>
    </table>
    <?php if (!empty($base_mec['vigiles_json'])): ?>
    <div class="obs-label">Noms des vigiles présents</div>
    <div class="obs-box"><?= nl2br(htmlspecialchars($base_mec['vigiles_json'])) ?></div>
    <?php endif; ?>
    <?php if (!empty($base_mec['observations'])): ?><div class="obs-label">Observations</div><div class="obs-box"><?= nl2br(htmlspecialchars($base_mec['observations'])) ?></div><?php endif; ?>
    <div class="sig-box"><div class="sig-area"><div class="sig-line"></div>Signature du contrôleur</div></div>
</div>
<?php endif; ?>

</body>
</html>
