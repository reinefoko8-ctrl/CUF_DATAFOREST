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

$existing = $db->query("SELECT * FROM fiche_routes_forestieres WHERE rapport_id=$rid")->fetch_assoc();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rapport['statut'] === 'brouillon') {
    $tf  = sanitize($_POST['titre_forestier'] ?? '');
    $aac = sanitize($_POST['aac'] ?? '');
    $nc  = sanitize($_POST['nom_controleur'] ?? '');
    $dc  = sanitize($_POST['date_controle'] ?? date('Y-m-d'));
    $car = sanitize($_POST['caracteristiques_troncon'] ?? '');
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

    if ($existing) {
        $stmt2 = $db->prepare("UPDATE fiche_routes_forestieres SET
            titre_forestier=?, aac=?, nom_controleur=?, date_controle=?,
            caracteristiques_troncon=?,
            c1=?, c2=?, c3=?, c4=?, c5=?, c6=?, c7=?, c8=?, c9=?, c10=?,
            total_points=?, observations=?, appreciation=?
            WHERE rapport_id=?");
        $stmt2->bind_param('ssssssssssssssssssi',
            $tf,$aac,$nc,$dc,$car,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tp,$obs,$app,$rid);
    } else {
        $stmt2 = $db->prepare("INSERT INTO fiche_routes_forestieres
            (rapport_id, titre_forestier, aac, nom_controleur, date_controle,
             caracteristiques_troncon,
             c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,
             total_points, observations, appreciation)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt2->bind_param('issssssssssssssssss',
            $rid,$tf,$aac,$nc,$dc,$car,
            $c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,
            $tp,$obs,$app);
    }

    if ($stmt2->execute()) {
        $msg = 'success';
        $existing = $db->query("SELECT * FROM fiche_routes_forestieres WHERE rapport_id=$rid")->fetch_assoc();
    } else {
        $msg = 'error:' . $db->error;
    }
}

$pageTitle = 'Fiche Routes Forestières';
include '../includes/header.php';
function vr($e,$f,$def=''){return htmlspecialchars($e[$f]??$def);}
function svr($e,$f){return $e[$f]??'';}
?>
<div class="page-header">
    <div><h1 class="page-title">🛤️ Routes Forestières</h1>
    <div class="breadcrumb">
        <a href="../dashboard_controleur.php">Tableau de bord</a> ›
        <a href="rapport_edit.php?id=<?=$rid?>">Rapport #<?=$rid?></a> › Routes forestières
    </div></div>
    <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary">← Retour</a>
</div>
<?php if($msg==='success'):?><div class="alert alert-success">✅ Fiche enregistrée avec succès !</div><?php endif;?>
<?php if(strpos($msg,'error')===0):?><div class="alert alert-error">❌ Erreur : <?=htmlspecialchars(substr($msg,6))?></div><?php endif;?>

<form method="POST" class="fiche-form-container fade-in">
<div class="fiche-form-header">
    <img src="../images/logo.png" alt="CUF"/>
    <div><h2>FICHE DE CONTRÔLE — "Routes Forestières"</h2>
    <p>Rapport #<?=$rid?></p></div>
</div>
<div class="fiche-form-body">
    <div class="form-row-3 mb-3">
        <div class="form-group"><label>Titre forestier</label>
            <input type="text" name="titre_forestier" class="form-control"
            value="<?=vr($existing,'titre_forestier',$rapport['titre_forestier']??'')?>"/></div>
        <div class="form-group"><label>AAC</label>
            <input type="text" name="aac" class="form-control"
            value="<?=vr($existing,'aac',$rapport['aac']??'')?>"/></div>
        <div class="form-group"><label>Nom du contrôleur</label>
            <input type="text" name="nom_controleur" class="form-control"
            value="<?=vr($existing,'nom_controleur',$_SESSION['prenom'].' '.$_SESSION['nom'])?>"/></div>
        <div class="form-group"><label>Date de contrôle</label>
            <input type="date" name="date_controle" class="form-control"
            value="<?=vr($existing,'date_controle',date('Y-m-d'))?>"/></div>
        <div class="form-group" style="grid-column:span 2"><label>Caractéristiques du tronçon</label>
            <input type="text" name="caracteristiques_troncon" class="form-control"
            value="<?=vr($existing,'caracteristiques_troncon')?>"/></div>
    </div>
    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Critères d'évaluation</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;">
        <strong>1</strong> = Conforme &nbsp;|&nbsp; <strong>0</strong> = Non conforme &nbsp;|&nbsp; <strong>NA</strong> = Non applicable
    </p>
    <?php $criteres=[
        ['c1', "La route respecte-t-elle le tracé initial prévu dans le plan de gestion ?"],
        ['c2', "La largeur de la route est-elle conforme aux normes (entre 7 et 9 m) ?"],
        ['c3', "Les fossés de drainage sont-ils fonctionnels et bien entretenus ?"],
        ['c4', "Les ouvrages de franchissement (ponceaux) sont-ils en bon état ?"],
        ['c5', "Les zones riveraines (30 m de chaque côté) sont-elles protégées ?"],
        ['c6', "Les pentes sont-elles traitées pour éviter l'érosion ?"],
        ['c7', "La signalisation routière est-elle présente et lisible ?"],
        ['c8', "Les accès aux zones interdites sont-ils fermés ?"],
        ['c9', "Les routes abandonnées sont-elles correctement fermées ?"],
        ['c10',"L'entretien régulier de la route est-il effectif ?"],
    ];?>
    <table class="criteres-table">
        <thead><tr><th style="width:40px;">N°</th><th>Critère</th><th style="width:160px;text-align:center;">Score</th></tr></thead>
        <tbody>
        <?php foreach($criteres as $i=>[$key,$texte]):?>
        <tr data-pts="1">
            <td class="critere-num"><?=$i+1?></td>
            <td style="font-size:0.84rem;"><?=$texte?></td>
            <td>
                <input type="hidden" name="<?=$key?>" value="<?=svr($existing,$key)?>"/>
                <div class="critere-score">
                    <button type="button" class="score-btn <?=svr($existing,$key)==='1'?'selected-1':''?>" data-val="1">1</button>
                    <button type="button" class="score-btn <?=svr($existing,$key)==='0'?'selected-0':''?>" data-val="0">0</button>
                    <button type="button" class="score-btn <?=svr($existing,$key)==='NA'?'selected-na':''?>" data-val="NA">NA</button>
                </div>
            </td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <div class="total-bar">
        <span class="total-label">📊 Total :</span>
        <span class="total-value" id="total-display"><?=vr($existing,'total_points','0')?>/10</span>
    </div>
    <input type="hidden" name="total_points" id="total-hidden" value="<?=vr($existing,'total_points','0')?>"/>
    <div class="form-group mt-2"><label>Observations</label>
        <textarea name="observations" class="form-control" rows="4"><?=vr($existing,'observations')?></textarea></div>
    <div class="form-group"><label>Appréciation</label>
        <textarea name="appreciation" class="form-control" rows="2"><?=vr($existing,'appreciation')?></textarea></div>
    <?php if($rapport['statut']==='brouillon'):?>
    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button>
        <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary btn-lg">← Retour</a>
    </div>
    <?php else:?>
    <div class="alert alert-info">ℹ️ Lecture seule (statut : <?=$rapport['statut']?>).</div>
    <?php endif;?>
</div>
</form>
<?php include '../includes/footer.php';?>
