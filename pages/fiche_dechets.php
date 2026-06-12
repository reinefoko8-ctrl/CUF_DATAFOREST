<?php
// pages/fiche_dechets.php
require_once '../includes/config.php';
requireLogin();
$db=getDB();$uid=$_SESSION['user_id'];
$rid=(int)($_GET['rapport_id']??0);
if(!$rid){header('Location:../dashboard_controleur.php');exit;}
$stmt=$db->prepare("SELECT * FROM rapports WHERE id=?");$stmt->bind_param('i',$rid);$stmt->execute();
$rapport=$stmt->get_result()->fetch_assoc();
if(!$rapport){header('Location:../dashboard_controleur.php');exit;}
$existing=$db->query("SELECT * FROM fiche_dechets_foret WHERE rapport_id=$rid")->fetch_assoc();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'&&$rapport['statut']==='brouillon'){
    $d=$_POST;
    $tf  = sanitize($d['titre_forestier'] ?? '');
    $aac = sanitize($d['aac'] ?? '');
    $nc  = sanitize($d['nom_controleur'] ?? '');
    $dc  = sanitize($d['date_controle'] ?? date('Y-m-d'));
    $bn  = sanitize($d['bac_nettoyage'] ?? '');
    $dec = sanitize($d['decanteur'] ?? '');
    $pnb = sanitize($d['poubelle_non_biodeg'] ?? '');
    $hu  = sanitize($d['huiles_usees'] ?? '');
    $fil = sanitize($d['filtres'] ?? '');
    $bat = sanitize($d['batteries'] ?? '');
    $cdb = sanitize($d['cables_debardage'] ?? '');
    $ah  = sanitize($d['absence_huiles'] ?? '');
    $ap  = sanitize($d['absence_plastiques'] ?? '');
    $td  = sanitize($d['transfert_dechets'] ?? '');
    $tc  = sanitize($d['transfert_contenants'] ?? '');
    $sen = sanitize($d['sensibilisation'] ?? '');
    $con = sanitize($d['consignes_respectees'] ?? '');
    $tot = sanitize($d['total_points'] ?? '0');
    $obs = sanitize($d['observations'] ?? '');
    if($existing){
        $stmt2=$db->prepare("UPDATE fiche_dechets_foret SET
            titre_forestier=?,aac=?,nom_controleur=?,date_controle=?,
            bac_nettoyage=?,decanteur=?,poubelle_non_biodeg=?,huiles_usees=?,filtres=?,
            batteries=?,cables_debardage=?,absence_huiles=?,absence_plastiques=?,
            transfert_dechets=?,transfert_contenants=?,sensibilisation=?,consignes_respectees=?,
            total_points=?,observations=?
            WHERE rapport_id=?");
        $stmt2->bind_param('sssssssssssssssssssi',
            $tf,$aac,$nc,$dc,$bn,$dec,$pnb,$hu,$fil,
            $bat,$cdb,$ah,$ap,$td,$tc,$sen,$con,$tot,$obs,$rid);
    }else{
        $stmt2=$db->prepare("INSERT INTO fiche_dechets_foret
            (rapport_id,titre_forestier,aac,nom_controleur,date_controle,
             bac_nettoyage,decanteur,poubelle_non_biodeg,huiles_usees,filtres,
             batteries,cables_debardage,absence_huiles,absence_plastiques,
             transfert_dechets,transfert_contenants,sensibilisation,consignes_respectees,
             total_points,observations)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt2->bind_param('isssssssssssssssssss',
            $rid,$tf,$aac,$nc,$dc,$bn,$dec,$pnb,$hu,$fil,
            $bat,$cdb,$ah,$ap,$td,$tc,$sen,$con,$tot,$obs);
    }
    if($stmt2->execute()){$msg='success';$existing=$db->query("SELECT * FROM fiche_dechets_foret WHERE rapport_id=$rid")->fetch_assoc();}else $msg='error';
}
$pageTitle='Fiche Déchets en Forêt';
include '../includes/header.php';
function vd($e,$f,$def=''){return htmlspecialchars($e[$f]??$def);}
function svd($e,$f){return $e[$f]??'';}
?>
<div class="page-header">
    <div><h1 class="page-title">♻️ Gestion des Déchets en Forêt</h1>
    <div class="breadcrumb"><a href="../dashboard_controleur.php">Tableau de bord</a> › <a href="rapport_edit.php?id=<?=$rid?>">Rapport #<?=$rid?></a> › Déchets</div></div>
    <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary">← Retour</a>
</div>
<?php if($msg==='success'):?><div class="alert alert-success">✅ Fiche enregistrée !</div><?php endif;?>
<form method="POST" class="fiche-form-container fade-in">
<div class="fiche-form-header"><img src="../images/logo.png" alt="CUF"/>
    <div><h2>FICHE DE CONTRÔLE — "Gestion des déchets en forêt"</h2><p>Version 02, du 23/09/2025 &nbsp;|&nbsp; Rapport #<?=$rid?></p></div></div>
<div class="fiche-form-body">
    <div class="form-row mb-3">
        <div class="form-group"><label>Titre forestier</label><input type="text" name="titre_forestier" class="form-control" value="<?=vd($existing,'titre_forestier',$rapport['titre_forestier']??'')?>"/></div>
        <div class="form-group"><label>AAC / Site</label><input type="text" name="aac" class="form-control" value="<?=vd($existing,'aac',$rapport['aac']??'')?>"/></div>
        <div class="form-group"><label>Date de contrôle</label><input type="date" name="date_controle" class="form-control" value="<?=vd($existing,'date_controle',date('Y-m-d'))?>"/></div>
        <div class="form-group"><label>Nom du contrôleur</label><input type="text" name="nom_controleur" class="form-control" value="<?=vd($existing,'nom_controleur',$_SESSION['prenom'].' '.$_SESSION['nom'])?>"/></div>
    </div>
    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Points de contrôle</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;"><strong>1</strong> = Conforme &nbsp;|&nbsp; <strong>0</strong> = Non conforme &nbsp;|&nbsp; <strong>2</strong> = (pour critères à 2 pts) &nbsp;|&nbsp; <strong>3</strong> = (pour critères à 3 pts)</p>
    <?php
    $sections=[
        'Outils de gestion des déchets en forêt'=>[
            ['bac_nettoyage','Bac de nettoyage des pièces détachées disponible',1],
            ['decanteur','Décanteur pour filtres usagers disponible',1],
            ['poubelle_non_biodeg','Poubelle pour déchets non-biodégradables disponibles au parc en activité',1],
        ],
        'De la collecte au stockage des déchets dangereux'=>[
            ['huiles_usees','Huiles usées bien stockées (si disponible)',2],
            ['filtres','Filtres usés bien stockés',1],
            ['batteries','Batteries et piles usées bien stockées',1],
            ['cables_debardage','Câbles de débardage usés collectés et stockés',2],
            ['absence_huiles','Absence de traces d\'huiles dans les zones d\'intervention mécaniques',1],
            ['absence_plastiques','Absence de déchets plastiques, câbles de débardage abandonnés dans la forêt',2],
        ],
        'Transfert des déchets dangereux'=>[
            ['transfert_dechets','Transfert des déchets dangereux',1],
            ['transfert_contenants','Les déchets dangereux liquides sont transférés vers la base mécanique dans des contenants étanches',1],
        ],
        'Sensibilisation du personnel'=>[
            ['sensibilisation','Les travailleurs sont sensibilisés au respect des consignes de gestion des déchets en forêt',3],
            ['consignes_respectees','Les consignes de gestion des déchets en forêt sont respectées par le personnel',3],
        ],
    ];
    foreach($sections as $section=>$items):?>
    <div style="margin-bottom:16px;">
        <div style="background:var(--vert-fonce);color:white;padding:8px 16px;font-weight:600;font-size:0.85rem;border-radius:var(--radius-sm) var(--radius-sm) 0 0;"><?=$section?></div>
        <table class="criteres-table" style="margin-bottom:0;">
            <tbody>
            <?php foreach($items as[$key,$texte,$pts]):?>
            <tr data-pts="<?=$pts?>">
                <td style="font-size:0.84rem;"><?=$texte?></td>
                <td style="width:180px;">
                    <input type="hidden" name="<?=$key?>" value="<?=svd($existing,$key)?>"/>
                    <div class="critere-score">
                        <?php for($v=$pts;$v>=0;$v--):?>
                        <button type="button" class="score-btn <?=svd($existing,$key)==(string)$v?'selected-'.($v>0?'1':'0'):''?>" data-val="<?=$v?>"><?=$v?></button>
                        <?php endfor;?>
                        <button type="button" class="score-btn <?=svd($existing,$key)==='NA'?'selected-na':''?>" data-val="NA">NA</button>
                    </div>
                </td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php endforeach;?>
    <div class="total-bar"><span class="total-label">📊 Total :</span><span class="total-value" id="total-display"><?=vd($existing,'total_points','0')?>/20</span></div>
    <input type="hidden" name="total_points" id="total-hidden" value="<?=vd($existing,'total_points','0')?>"/>
    <div class="form-group mt-2"><label>Observations et recommandations</label><textarea name="observations" class="form-control" rows="4"><?=vd($existing,'observations')?></textarea></div>
    <?php if($rapport['statut']==='brouillon'):?>
    <div style="display:flex;gap:12px;"><button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button><a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary btn-lg">← Retour</a></div>
    <?php endif;?>
</div></form>
<?php include '../includes/footer.php';?>
