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

$existing = $db->query("SELECT * FROM fiche_sortie_pieds WHERE rapport_id=$rid")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rapport['statut'] === 'brouillon') {
    $tf   = sanitize($_POST['titre_forestier'] ?? '');
    $aac  = sanitize($_POST['aac'] ?? '');
    $nc   = sanitize($_POST['nom_controleur'] ?? '');
    $dc   = sanitize($_POST['date_controle'] ?? date('Y-m-d'));
    $uc   = sanitize($_POST['uc'] ?? '');
    $pf   = sanitize($_POST['parc_foret_planifie'] ?? '');
    $ntm  = sanitize($_POST['nb_tiges_avenir_materialisees'] ?? '');
    $ntnm = sanitize($_POST['nb_tiges_avenir_non_materialisees'] ?? '');
    $tp1  = sanitize($_POST['trace_principal_nb_pistes'] ?? '');
    $tp2  = sanitize($_POST['trace_principal_nb_prise_mesure'] ?? '');
    $tp3  = sanitize($_POST['trace_principal_largeur_moyenne'] ?? '');
    $ts1  = sanitize($_POST['trace_secondaire_nb_pistes'] ?? '');
    $ts2  = sanitize($_POST['trace_secondaire_nb_prise_mesure'] ?? '');
    $ts3  = sanitize($_POST['trace_secondaire_largeur_moyenne'] ?? '');
    $c1   = sanitize($_POST['c1'] ?? '');
    $c2   = sanitize($_POST['c2'] ?? '');
    $c3   = sanitize($_POST['c3'] ?? '');
    $c4   = sanitize($_POST['c4'] ?? '');
    $c5   = sanitize($_POST['c5'] ?? '');
    $c6   = sanitize($_POST['c6'] ?? '');
    $c7   = sanitize($_POST['c7'] ?? '');
    $c8   = sanitize($_POST['c8'] ?? '');
    $c9   = sanitize($_POST['c9'] ?? '');
    $c10  = sanitize($_POST['c10'] ?? '');
    $tot  = sanitize($_POST['total_points'] ?? '0');
    $obs  = sanitize($_POST['observations'] ?? '');
    $app  = sanitize($_POST['appreciation'] ?? '');

    if ($existing) {
        $stmt2 = $db->prepare("UPDATE fiche_sortie_pieds SET
            titre_forestier=?, aac=?, nom_controleur=?, date_controle=?, uc=?,
            parc_foret_planifie=?, nb_tiges_avenir_materialisees=?,
            nb_tiges_avenir_non_materialisees=?,
            trace_principal_nb_pistes=?, trace_principal_nb_prise_mesure=?,
            trace_principal_largeur_moyenne=?,
            trace_secondaire_nb_pistes=?, trace_secondaire_nb_prise_mesure=?,
            trace_secondaire_largeur_moyenne=?,
            c1=?,c2=?,c3=?,c4=?,c5=?,c6=?,c7=?,c8=?,c9=?,c10=?,
            total_points=?, observations=?, appreciation=?
            WHERE rapport_id=?");
        $stmt2->bind_param('sssssssssssssssssssssssssssi',
            $tf,$aac,$nc,$dc,$uc,$pf,$ntm,$ntnm,
            $tp1,$tp2,$tp3,$ts1,$ts2,$ts3,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tot,$obs,$app,$rid);
    } else {
        $stmt2 = $db->prepare("INSERT INTO fiche_sortie_pieds
            (rapport_id, titre_forestier, aac, nom_controleur, date_controle, uc,
             parc_foret_planifie, nb_tiges_avenir_materialisees,
             nb_tiges_avenir_non_materialisees,
             trace_principal_nb_pistes, trace_principal_nb_prise_mesure,
             trace_principal_largeur_moyenne,
             trace_secondaire_nb_pistes, trace_secondaire_nb_prise_mesure,
             trace_secondaire_largeur_moyenne,
             c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,
             total_points, observations, appreciation)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt2->bind_param('isssssssssssssssssssssssssss',
            $rid,$tf,$aac,$nc,$dc,$uc,$pf,$ntm,$ntnm,
            $tp1,$tp2,$tp3,$ts1,$ts2,$ts3,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tot,$obs,$app);
    }

    if ($stmt2->execute()) {
        $msg = 'success';
        $existing = $db->query("SELECT * FROM fiche_sortie_pieds WHERE rapport_id=$rid")->fetch_assoc();
    } else {
        $msg = 'error:' . $db->error;
    }
}

$pageTitle = 'Fiche Sortie Pieds';
include '../includes/header.php';
function vs($e,$f,$def=''){return htmlspecialchars($e[$f]??$def);}
function svs($e,$f){return $e[$f]??'';}
?>
<div class="page-header">
    <div><h1 class="page-title">👣 Sortie Pieds</h1>
    <div class="breadcrumb">
        <a href="../dashboard_controleur.php">Tableau de bord</a> ›
        <a href="rapport_edit.php?id=<?=$rid?>">Rapport #<?=$rid?></a> › Sortie pieds
    </div></div>
    <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary">← Retour</a>
</div>
<?php if($msg==='success'):?><div class="alert alert-success">✅ Fiche enregistrée avec succès !</div><?php endif;?>
<?php if(strpos($msg,'error')===0):?><div class="alert alert-error">❌ Erreur : <?=htmlspecialchars(substr($msg,6))?></div><?php endif;?>

<form method="POST" class="fiche-form-container fade-in">
<div class="fiche-form-header">
    <img src="../images/logo.png" alt="CUF"/>
    <div><h2>FICHE DE CONTRÔLE — "Sortie pieds"</h2>
    <p>Version 01, du 23/09/2025 &nbsp;|&nbsp; Rapport #<?=$rid?></p></div>
</div>
<div class="fiche-form-body">
    <div class="form-row-3 mb-3">
        <div class="form-group"><label>Titre forestier</label>
            <input type="text" name="titre_forestier" class="form-control" value="<?=vs($existing,'titre_forestier',$rapport['titre_forestier']??'')?>"/></div>
        <div class="form-group"><label>AAC</label>
            <input type="text" name="aac" class="form-control" value="<?=vs($existing,'aac',$rapport['aac']??'')?>"/></div>
        <div class="form-group"><label>Nom du contrôleur</label>
            <input type="text" name="nom_controleur" class="form-control" value="<?=vs($existing,'nom_controleur',$_SESSION['prenom'].' '.$_SESSION['nom'])?>"/></div>
        <div class="form-group"><label>Date de contrôle</label>
            <input type="date" name="date_controle" class="form-control" value="<?=vs($existing,'date_controle',date('Y-m-d'))?>"/></div>
        <div class="form-group"><label>Unité de comptage (UC)</label>
            <input type="text" name="uc" class="form-control" value="<?=vs($existing,'uc')?>"/></div>
        <div class="form-group"><label>Parc forêt planifié n°</label>
            <input type="text" name="parc_foret_planifie" class="form-control" value="<?=vs($existing,'parc_foret_planifie')?>"/></div>
        <div class="form-group"><label>Nb tiges d'avenir matérialisées</label>
            <input type="number" name="nb_tiges_avenir_materialisees" class="form-control" value="<?=vs($existing,'nb_tiges_avenir_materialisees')?>"/></div>
        <div class="form-group"><label>Nb tiges d'avenir non matérialisées</label>
            <input type="number" name="nb_tiges_avenir_non_materialisees" class="form-control" value="<?=vs($existing,'nb_tiges_avenir_non_materialisees')?>"/></div>
    </div>

    <div class="card mb-3"><div class="card-header"><span class="card-title">📏 Tracés</span></div>
    <div class="card-body">
        <h4 style="color:var(--vert-fonce);font-size:0.9rem;margin-bottom:10px;">Tracé principal</h4>
        <div class="form-row mb-2">
            <div class="form-group"><label>Nb pistes parcourues</label>
                <input type="number" name="trace_principal_nb_pistes" class="form-control" value="<?=vs($existing,'trace_principal_nb_pistes')?>"/></div>
            <div class="form-group"><label>Nb prises de mesure</label>
                <input type="number" name="trace_principal_nb_prise_mesure" class="form-control" value="<?=vs($existing,'trace_principal_nb_prise_mesure')?>"/></div>
            <div class="form-group"><label>Largeur moyenne (m)</label>
                <input type="number" step="0.01" name="trace_principal_largeur_moyenne" class="form-control" value="<?=vs($existing,'trace_principal_largeur_moyenne')?>"/></div>
        </div>
        <h4 style="color:var(--vert-fonce);font-size:0.9rem;margin-bottom:10px;">Tracé secondaire</h4>
        <div class="form-row">
            <div class="form-group"><label>Nb pistes parcourues</label>
                <input type="number" name="trace_secondaire_nb_pistes" class="form-control" value="<?=vs($existing,'trace_secondaire_nb_pistes')?>"/></div>
            <div class="form-group"><label>Nb prises de mesure</label>
                <input type="number" name="trace_secondaire_nb_prise_mesure" class="form-control" value="<?=vs($existing,'trace_secondaire_nb_prise_mesure')?>"/></div>
            <div class="form-group"><label>Largeur moyenne (m)</label>
                <input type="number" step="0.01" name="trace_secondaire_largeur_moyenne" class="form-control" value="<?=vs($existing,'trace_secondaire_largeur_moyenne')?>"/></div>
        </div>
    </div></div>

    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Critères d'évaluation</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;">
        <strong>1</strong> = Conforme &nbsp;|&nbsp; <strong>0</strong> = Non conforme &nbsp;|&nbsp; <strong>NA</strong> = Non applicable
    </p>
    <?php $criteres=[
        ['c1', "Les arbres triés et pistés sont-ils réellement exploitables et situés dans une zone autorisée ?"],
        ['c2', "Pour chaque arbre abandonné, le motif d'abandon est-il justifié ?"],
        ['c3', "La carte de sortie pieds intègre-t-elle le projet route et est mise à jour quotidiennement ?"],
        ['c4', "La matérialisation des pistes de sortie pieds est-elle effective et conforme (largeur > 1 m, hauteur > 2 m) ?"],
        ['c5', "Le tracé des pistes secondaires est-il en 'arêtes de poisson' sur la piste principale ?"],
        ['c6', "Le tracé des pistes sur le terrain respecte-t-il le projet initial ?"],
        ['c7', "Les jalons portent-ils la mention exacte du nombre de pieds et sont-ils orientés vers le chemin d'accès ?"],
        ['c8', "La matérialisation des tiges d'avenir, semenciers et arbres interdits est-elle effective ?"],
        ['c9', "La matérialisation des arbres patrimoniaux et autres sites d'intérêt est-elle effective ?"],
        ['c10',"Le projet de création d'un parc est-il matérialisé à l'aide d'un jalon ?"],
    ];?>
    <table class="criteres-table">
        <thead><tr><th style="width:40px;">N°</th><th>Critère</th><th style="width:160px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php foreach($criteres as $i=>[$key,$texte]):?>
        <tr data-pts="1">
            <td class="critere-num"><?=$i+1?></td>
            <td style="font-size:0.84rem;"><?=$texte?></td>
            <td>
                <input type="hidden" name="<?=$key?>" value="<?=svs($existing,$key)?>"/>
                <div class="critere-score">
                    <button type="button" class="score-btn <?=svs($existing,$key)==='1'?'selected-1':''?>" data-val="1">1</button>
                    <button type="button" class="score-btn <?=svs($existing,$key)==='0'?'selected-0':''?>" data-val="0">0</button>
                    <button type="button" class="score-btn <?=svs($existing,$key)==='NA'?'selected-na':''?>" data-val="NA">NA</button>
                </div>
            </td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <div class="total-bar">
        <span class="total-label">📊 Total :</span>
        <span class="total-value" id="total-display"><?=vs($existing,'total_points','0')?>/10</span>
    </div>
    <input type="hidden" name="total_points" id="total-hidden" value="<?=vs($existing,'total_points','0')?>"/>
    <div class="form-group mt-2"><label>Observations</label>
        <textarea name="observations" class="form-control" rows="4"><?=vs($existing,'observations')?></textarea></div>
    <div class="form-group"><label>Appréciation</label>
        <textarea name="appreciation" class="form-control" rows="2"><?=vs($existing,'appreciation')?></textarea></div>
    <?php if($rapport['statut']==='brouillon'):?>
    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button>
        <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary btn-lg">← Retour</a>
    </div>
    <?php else:?><div class="alert alert-info">ℹ️ Lecture seule.</div><?php endif;?>
</div>
</form>
<?php include '../includes/footer.php';?>
