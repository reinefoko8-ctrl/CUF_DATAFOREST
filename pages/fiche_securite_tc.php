<?php
require_once '../includes/config.php';
requireLogin();
$db=getDB();$uid=$_SESSION['user_id'];
$rid=(int)($_GET['rapport_id']??0);
if(!$rid){header('Location:../dashboard_controleur.php');exit;}
$stmt=$db->prepare("SELECT * FROM rapports WHERE id=?");$stmt->bind_param('i',$rid);$stmt->execute();
$rapport=$stmt->get_result()->fetch_assoc();
$existing=$db->query("SELECT * FROM fiche_securite_tronconneuses WHERE rapport_id=$rid")->fetch_assoc();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'&&$rapport['statut']==='brouillon'){
    $tc_data=$_POST['tc']??[];
    $tc_json=json_encode($tc_data);
    $tf=sanitize($_POST['titre_forestier']??'');
    $aac=sanitize($_POST['aac']??'');
    $nc=sanitize($_POST['nom_controleur']??'');
    $dc=sanitize($_POST['date_controle']??date('Y-m-d'));
    if($existing){
        $stmt2=$db->prepare("UPDATE fiche_securite_tronconneuses SET titre_forestier=?,aac=?,nom_controleur=?,date_controle=?,tc_json=? WHERE rapport_id=?");
        $stmt2->bind_param('sssssi',$tf,$aac,$nc,$dc,$tc_json,$rid);
    }else{
        $stmt2=$db->prepare("INSERT INTO fiche_securite_tronconneuses (rapport_id,titre_forestier,aac,nom_controleur,date_controle,tc_json) VALUES (?,?,?,?,?,?)");
        $stmt2->bind_param('isssss',$rid,$tf,$aac,$nc,$dc,$tc_json);
    }
    if($stmt2->execute()){$msg='success';$existing=$db->query("SELECT * FROM fiche_securite_tronconneuses WHERE rapport_id=$rid")->fetch_assoc();}
    else $msg='error';
}
$tc_rows=[];
if($existing&&$existing['tc_json'])$tc_rows=json_decode($existing['tc_json'],true)??[];
if(empty($tc_rows))$tc_rows=[['num_serie'=>'','e1'=>'','e2'=>'','e3'=>'','e4'=>'','e5'=>'','e6'=>'','e7'=>'','e8'=>'']];
$pageTitle='Fiche Sécurité Tronçonneuses';
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">⚙️ Sécurité des Tronçonneuses</h1>
    <div class="breadcrumb"><a href="../dashboard_controleur.php">Tableau de bord</a> › <a href="rapport_edit.php?id=<?=$rid?>">Rapport #<?=$rid?></a> › Sécurité TC</div></div>
    <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary">← Retour</a>
</div>
<?php if($msg==='success'):?><div class="alert alert-success">✅ Fiche enregistrée !</div><?php endif;?>
<form method="POST" class="fiche-form-container fade-in">
<div class="fiche-form-header"><img src="../images/logo.png" alt="CUF"/>
    <div><h2>FICHE DE CONTRÔLE — "Éléments de sécurité des tronçonneuses"</h2><p>Version 01, du 01/04/2026 &nbsp;|&nbsp; Rapport #<?=$rid?></p></div></div>
<div class="fiche-form-body">
    <div class="form-row mb-3">
        <div class="form-group"><label>Titre forestier</label><input type="text" name="titre_forestier" class="form-control" value="<?=htmlspecialchars($existing['titre_forestier']??$rapport['titre_forestier']??'')?>"/></div>
        <div class="form-group"><label>AAC</label><input type="text" name="aac" class="form-control" value="<?=htmlspecialchars($existing['aac']??$rapport['aac']??'')?>"/></div>
        <div class="form-group"><label>Nom du contrôleur</label><input type="text" name="nom_controleur" class="form-control" value="<?=htmlspecialchars($existing['nom_controleur']??$_SESSION['prenom'].' '.$_SESSION['nom'])?>"/></div>
        <div class="form-group"><label>Date de contrôle</label><input type="date" name="date_controle" class="form-control" value="<?=htmlspecialchars($existing['date_controle']??date('Y-m-d'))?>"/></div>
    </div>
    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:12px;">Éléments de sécurité par tronçonneuse</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:16px;">✓ = Présent/fonctionnel &nbsp;|&nbsp; ✗ = Absent/défaillant</p>
    <div class="table-wrapper">
    <table class="criteres-table" id="tc-table">
        <thead>
            <tr>
                <th>N° de la scie</th>
                <th>1. Protège main avant</th>
                <th>2. Frein de chaîne</th>
                <th>3. Ergot anti-fouet</th>
                <th>4. Double gâchette</th>
                <th>5. Silentbloc</th>
                <th>6. Poignée antidérapant</th>
                <th>7. Bouton marche arrêt</th>
                <th>8. Autres observations sécurité</th>
            </tr>
        </thead>
        <tbody id="tc-tbody">
        <?php foreach($tc_rows as $i=>$row):?>
        <tr>
            <td><input class="form-control" type="text" name="tc[<?=$i?>][num_serie]" value="<?=htmlspecialchars($row['num_serie']??'')?>" placeholder="N° série"/></td>
            <?php foreach(['e1','e2','e3','e4','e5','e6','e7','e8'] as $e):?>
            <td class="text-center">
                <select class="form-control" name="tc[<?=$i?>][<?=$e?>]" style="min-width:70px;">
                    <option value="">-</option>
                    <option value="1" <?=($row[$e]??'')==='1'?'selected':''?>>✓</option>
                    <option value="0" <?=($row[$e]??'')==='0'?'selected':''?>>✗</option>
                </select>
            </td>
            <?php endforeach;?>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    </div>
    <div style="margin-top:12px;display:flex;gap:10px;align-items:center;">
        <button type="button" id="add-tc-row" class="btn btn-secondary btn-sm">➕ Ajouter une tronçonneuse</button>
        <span style="font-size:0.8rem;color:var(--gris-texte);">Ajoutez autant de lignes que nécessaire</span>
    </div>
    <div class="form-group mt-3"><label>Présence de fuite de carburant ?</label>
        <select name="tc[0][presence_carburant]" class="form-control" style="max-width:200px;">
            <option value="">-</option>
            <option value="Oui">Oui</option>
            <option value="Non">Non</option>
        </select>
    </div>
    <div class="form-group"><label>Observations</label><textarea name="observations" class="form-control" rows="3"><?=htmlspecialchars($existing['observations']??'')?></textarea></div>
    <?php if($rapport['statut']==='brouillon'):?>
    <div style="display:flex;gap:12px;"><button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button><a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary btn-lg">← Retour</a></div>
    <?php endif;?>
</div></form>
<?php include '../includes/footer.php';?>
