<?php
require_once '../includes/config.php';
requireLogin();
$db  = getDB();
$uid = $_SESSION['user_id'];
$rid = (int)($_GET['rapport_id'] ?? 0);
if (!$rid) { header('Location: ../dashboard_controleur.php'); exit; }
$stmt = $db->prepare("SELECT * FROM rapports WHERE id=?");
$stmt->bind_param('i', $rid);
$stmt->execute();
$rapport = $stmt->get_result()->fetch_assoc();
if (!$rapport) { header('Location: ../dashboard_controleur.php'); exit; }
$existing = $db->query("SELECT * FROM fiche_abattage WHERE rapport_id=$rid")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rapport['statut'] === 'brouillon') {
    $d = $_POST;
    // Build field list dynamically
    $champs = ['nom_controleur','nom_abatteur','titre_forestier','aac','date_controle','nom_aide_abatteur','uc',
               'p1_num_code_barre','p1_num_df10','p1_num_ligne','p1_essence',
               'p1_c1_piste_fuite_direction','p1_c1_nettoyage','p1_c1_longueur_piste','p1_c1_largeur_piste',
               'p1_c2_egobelage','p1_c3_hauteur_souche',
               'p1_c4_entaille_1er_trait','p1_c4_entaille_2eme_trait','p1_c4_02_traits','p1_c4_semelle',
               'p1_c5_charniere_longue','p1_c5_largeur_charniere','p1_c5_epaulement',
               'p1_c6_coupe_abattage','p1_c7_patte_retenue','p1_c7_taille_patte',
               'p1_c8_aubiers','p1_c9_direction_chute',
               'p1_c10_tronconnage','p1_c10_etelage','p1_c10_ecuage',
               'p1_c11_marquage_souche','p1_c11_defaut_apparent','p1_total',
               'p2_num_code_barre','p2_num_df10','p2_essence','p2_total',
               'p3_num_code_barre','p3_num_df10','p3_essence','p3_total',
               'p4_num_code_barre','p4_num_df10','p4_essence','p4_total',
               'p5_num_code_barre','p5_num_df10','p5_essence','p5_total',
               'observations','appreciation'];
    // Remplir les valeurs
    $vals = [];
    foreach ($champs as $f) { $vals[$f] = sanitize($d[$f] ?? ''); }

    // Construire la requête sans fn() ni array_values direct
    $param_list = array_values($vals);

    if ($existing) {
        $sets = implode(',', array_map(function($f){ return "$f=?"; }, $champs));
        $stmt2 = $db->prepare("UPDATE fiche_abattage SET $sets WHERE rapport_id=?");
        $types = str_repeat('s', count($champs)) . 'i';
        $param_list[] = $rid;
        $stmt2->bind_param($types, ...$param_list);
    } else {
        $all = array_merge(['rapport_id'], $champs);
        $ph  = implode(',', array_fill(0, count($all), '?'));
        $stmt2 = $db->prepare("INSERT INTO fiche_abattage (" . implode(',', $all) . ") VALUES ($ph)");
        $types = 'i' . str_repeat('s', count($champs));
        array_unshift($param_list, $rid);
        $stmt2->bind_param($types, ...$param_list);
    }
    if ($stmt2->execute()) { $msg='success'; $existing=$db->query("SELECT * FROM fiche_abattage WHERE rapport_id=$rid")->fetch_assoc(); }
    else $msg='error:'.$db->error;
}

$pageTitle = 'Fiche Abattage Contrôlé';
include '../includes/header.php';

function v($e,$f,$def='') { return htmlspecialchars($e[$f] ?? $def); }
function sv($e,$f) { return $e[$f] ?? ''; }
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🪓 Fiche Abattage Contrôlé</h1>
        <div class="breadcrumb"><a href="../dashboard_controleur.php">Tableau de bord</a> › <a href="rapport_edit.php?id=<?= $rid ?>">Rapport #<?= $rid ?></a> › Abattage contrôlé</div>
    </div>
    <a href="rapport_edit.php?id=<?= $rid ?>" class="btn btn-secondary">← Retour</a>
</div>

<?php if ($msg==='success'): ?><div class="alert alert-success">✅ Fiche enregistrée !</div><?php endif; ?>
<?php if ($msg==='error'): ?><div class="alert alert-error">❌ Erreur d'enregistrement.</div><?php endif; ?>

<form method="POST" class="fiche-form-container fade-in">
    <div class="fiche-form-header">
        <img src="../images/logo.png" alt="CUF"/>
        <div>
            <h2>FICHE DE CONTRÔLE — "Opérations d'abattage contrôlé"</h2>
            <p>Version 02, du 01/04/2026 &nbsp;|&nbsp; Rapport #<?= $rid ?></p>
        </div>
    </div>
    <div class="fiche-form-body">
        <div class="form-row-3 mb-3">
            <div class="form-group"><label>Nom du contrôleur</label>
                <input type="text" name="nom_controleur" class="form-control" value="<?= v($existing,'nom_controleur',$_SESSION['prenom'].' '.$_SESSION['nom']) ?>"/></div>
            <div class="form-group"><label>Titre forestier</label>
                <input type="text" name="titre_forestier" class="form-control" value="<?= v($existing,'titre_forestier',$rapport['titre_forestier']??'') ?>"/></div>
            <div class="form-group"><label>AAC</label>
                <input type="text" name="aac" class="form-control" value="<?= v($existing,'aac',$rapport['aac']??'') ?>"/></div>
            <div class="form-group"><label>Nom de l'abatteur</label>
                <input type="text" name="nom_abatteur" class="form-control" value="<?= v($existing,'nom_abatteur') ?>"/></div>
            <div class="form-group"><label>Nom de l'aide abatteur</label>
                <input type="text" name="nom_aide_abatteur" class="form-control" value="<?= v($existing,'nom_aide_abatteur') ?>"/></div>
            <div class="form-group"><label>Date de contrôle</label>
                <input type="date" name="date_controle" class="form-control" value="<?= v($existing,'date_controle',date('Y-m-d')) ?>"/></div>
            <div class="form-group"><label>Unité de comptage (UC)</label>
                <input type="text" name="uc" class="form-control" value="<?= v($existing,'uc') ?>"/></div>
        </div>

        <hr class="divider"/>
        <h3 style="color:var(--vert-fonce);margin-bottom:6px;">Informations Pied 1</h3>
        <div class="form-row mb-2">
            <div class="form-group"><label>N° Code barre</label><input type="text" name="p1_num_code_barre" class="form-control" value="<?= v($existing,'p1_num_code_barre') ?>"/></div>
            <div class="form-group"><label>N° DF10</label><input type="text" name="p1_num_df10" class="form-control" value="<?= v($existing,'p1_num_df10') ?>"/></div>
            <div class="form-group"><label>N° Ligne</label><input type="text" name="p1_num_ligne" class="form-control" value="<?= v($existing,'p1_num_ligne') ?>"/></div>
            <div class="form-group"><label>Essence</label><input type="text" name="p1_essence" class="form-control" value="<?= v($existing,'p1_essence') ?>"/></div>
        </div>

        <?php
        $crit_p1 = [
            ['key'=>'p1_c1_piste_fuite_direction', 'pts'=>0.5,'cat'=>'1 - Piste de fuite (2 pts)','texte'=>'La direction de la piste de fuite est-elle à environ ±15° de la direction de chute ?'],
            ['key'=>'p1_c1_nettoyage',             'pts'=>0.5,'cat'=>'','texte'=>'Le nettoyage de la piste et du pourtour de l\'arbre est-il fait au plus bas possible ?'],
            ['key'=>'p1_c1_longueur_piste',        'pts'=>0.5,'cat'=>'','texte'=>'La longueur de la piste de fuite est-elle suffisante (au moins 15 m) ?'],
            ['key'=>'p1_c1_largeur_piste',         'pts'=>0.5,'cat'=>'','texte'=>'La largeur de la piste varie entre 1 m et 1,5 m et le nettoyage du pourtour est fait à un rayon d\'environ 2 m ?'],
            ['key'=>'p1_c2_egobelage',             'pts'=>1,  'cat'=>'2 - Égobelage (1 pt)','texte'=>'Si présence de contreforts, l\'égobelage a-t-il été effectué du côté de l\'entaille ?'],
            ['key'=>'p1_c3_hauteur_souche',        'pts'=>0.5,'cat'=>'3 - Hauteur de la souche (0,5 pt)','texte'=>'La souche est-elle la plus basse possible ?'],
            ['key'=>'p1_c4_entaille_1er_trait',    'pts'=>0.5,'cat'=>'4 - Entaille de direction (2 pts)','texte'=>'Le 1er trait horizontal de l\'entaille est-il assez profond (environ 1/5e du diamètre) ?'],
            ['key'=>'p1_c4_entaille_2eme_trait',   'pts'=>0.5,'cat'=>'','texte'=>'Le deuxième trait de scie forme un angle d\'environ 45° ?'],
            ['key'=>'p1_c4_02_traits',             'pts'=>0.5,'cat'=>'','texte'=>'Les 2 traits de scie (fond de l\'entaille) se rejoignent-ils en une ligne droite (charnière homogène) ?'],
            ['key'=>'p1_c4_semelle',               'pts'=>0.5,'cat'=>'','texte'=>'La semelle de l\'entaille de direction a-t-elle été amorcée (chanfrein) ?'],
            ['key'=>'p1_c5_charniere_longue',      'pts'=>1,  'cat'=>'5 - Charnière (2,5 pts)','texte'=>'La charnière est-elle bien établie, assez longue et régulière (environ 1/10e ou 10% du diamètre) ?'],
            ['key'=>'p1_c5_largeur_charniere',     'pts'=>1,  'cat'=>'','texte'=>'La largeur de la charnière est-elle d\'environ 4 doigts (en moyenne) ?'],
            ['key'=>'p1_c5_epaulement',            'pts'=>0.5,'cat'=>'','texte'=>'L\'épaulement est-il conforme (environ 6 et 10 cm pour les arbres de petits diamètres) ?'],
            ['key'=>'p1_c6_coupe_abattage',        'pts'=>1,  'cat'=>'6 - Coupe d\'abattage (1 pt)','texte'=>'La coupe d\'abattage est-elle uniforme (perçage) et sans arrache ?'],
            ['key'=>'p1_c7_patte_retenue',         'pts'=>0.5,'cat'=>'7 - Patte(s) de retenue (1,5 pt)','texte'=>'Les/la patte(s) de retenue existe(nt)-elle(s) et l\'emplacement est-il adéquat ?'],
            ['key'=>'p1_c7_taille_patte',          'pts'=>1,  'cat'=>'','texte'=>'La taille est-elle suffisante pour assurer la sécurité de l\'abatteur ?'],
            ['key'=>'p1_c8_aubiers',               'pts'=>0.5,'cat'=>'8 - Coupure des aubiers (0,5 pt)','texte'=>'Les aubiers ont-ils été coupés sur les deux côtés ?'],
            ['key'=>'p1_c9_direction_chute',       'pts'=>1,  'cat'=>'9 - Direction de chute (1 pt)','texte'=>'L\'arbre est-il tombé dans la bonne direction de chute ?'],
            ['key'=>'p1_c10_tronconnage',          'pts'=>1,  'cat'=>'10 - Tronçonnage (2 pts)','texte'=>'L\'arbre est-il tronçonné et la coupe est-elle réalisée sans défaut ?'],
            ['key'=>'p1_c10_etelage',              'pts'=>0.5,'cat'=>'','texte'=>'L\'étêtage est-il effectué à moins d\'un mètre de la première grosse branche ?'],
            ['key'=>'p1_c10_ecuage',               'pts'=>0.5,'cat'=>'','texte'=>'S\'il y a un écuage, était-il justifié (présence de contreforts, gros trou) ?'],
            ['key'=>'p1_c11_marquage_souche',      'pts'=>0.5,'cat'=>'11 - Valorisation / Marquage souche (1 pt)','texte'=>'La souche porte-t-elle le numéro d\'identification de l\'abatteur et l\'UC marqués ?'],
            ['key'=>'p1_c11_defaut_apparent',      'pts'=>0.5,'cat'=>'','texte'=>'La partie du fût de l\'arbre valorisable possède-t-elle un défaut apparent ?'],
        ];
        $current_cat = '';
        ?>
        <h3 style="color:var(--vert-fonce);margin:16px 0 12px;">Critères d'évaluation — Pied 1</h3>
        <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;"><strong>1</strong> = Conforme &nbsp;|&nbsp; <strong>0,5</strong> = Partiellement conforme &nbsp;|&nbsp; <strong>0</strong> = Non conforme &nbsp;|&nbsp; <strong>NA</strong> = Non applicable</p>

        <table class="criteres-table">
            <thead><tr><th>Catégorie</th><th>Critère</th><th style="text-align:center;width:160px;">Score</th></tr></thead>
            <tbody>
            <?php foreach ($crit_p1 as $c): ?>
            <tr data-pts="<?= $c['pts'] ?>">
                <td style="font-size:0.78rem;font-weight:600;color:var(--vert-fonce);white-space:nowrap;min-width:160px;"><?= $c['cat'] ?></td>
                <td style="font-size:0.83rem;"><?= $c['texte'] ?></td>
                <td>
                    <input type="hidden" name="<?= $c['key'] ?>" value="<?= sv($existing,$c['key']) ?>"/>
                    <div class="critere-score">
                        <button type="button" class="score-btn <?= sv($existing,$c['key'])==='1'?'selected-1':'' ?>" data-val="1">1</button>
                        <button type="button" class="score-btn <?= sv($existing,$c['key'])==='0.5'?'selected-1':'' ?>" data-val="0.5" style="font-size:0.75rem;">½</button>
                        <button type="button" class="score-btn <?= sv($existing,$c['key'])==='0'?'selected-0':'' ?>" data-val="0">0</button>
                        <button type="button" class="score-btn <?= sv($existing,$c['key'])==='NA'?'selected-na':'' ?>" data-val="NA">NA</button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-bar">
            <span class="total-label">📊 Total pied 1 :</span>
            <span class="total-value" id="total-display"><?= v($existing,'p1_total','0') ?>/15</span>
        </div>
        <input type="hidden" name="p1_total" id="total-hidden" value="<?= v($existing,'p1_total') ?>"/>

        <hr class="divider"/>
        <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Pieds 2 à 5 (références)</h3>
        <?php for ($p=2; $p<=5; $p++): ?>
        <div class="card" style="margin-bottom:12px;">
            <div class="card-header"><span class="card-title">🌲 Pied <?= $p ?></span></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group"><label>N° Code barre</label><input type="text" name="p<?=$p?>_num_code_barre" class="form-control" value="<?= v($existing,"p{$p}_num_code_barre") ?>"/></div>
                    <div class="form-group"><label>N° DF10</label><input type="text" name="p<?=$p?>_num_df10" class="form-control" value="<?= v($existing,"p{$p}_num_df10") ?>"/></div>
                    <div class="form-group"><label>Essence</label><input type="text" name="p<?=$p?>_essence" class="form-control" value="<?= v($existing,"p{$p}_essence") ?>"/></div>
                    <div class="form-group"><label>Total /15</label><input type="number" name="p<?=$p?>_total" step="0.5" class="form-control" value="<?= v($existing,"p{$p}_total") ?>" placeholder="0"/></div>
                </div>
            </div>
        </div>
        <?php endfor; ?>

        <hr class="divider"/>
        <div class="form-group">
            <label>Observations et/ou recommandations</label>
            <textarea name="observations" class="form-control" rows="4"><?= v($existing,'observations') ?></textarea>
        </div>
        <div class="form-group">
            <label>Appréciation</label>
            <textarea name="appreciation" class="form-control" rows="2"><?= v($existing,'appreciation') ?></textarea>
        </div>

        <?php if ($rapport['statut']==='brouillon'): ?>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button>
            <a href="rapport_edit.php?id=<?= $rid ?>" class="btn btn-secondary btn-lg">← Retour</a>
        </div>
        <?php else: ?>
        <div class="alert alert-info">ℹ️ Rapport en mode lecture seule (statut: <?= $rapport['statut'] ?>).</div>
        <?php endif; ?>
    </div>
</form>

<?php include '../includes/footer.php'; ?>
