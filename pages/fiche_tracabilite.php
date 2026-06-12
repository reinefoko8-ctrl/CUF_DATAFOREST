<?php
require_once '../includes/config.php';
requireLogin();
$db=getDB(); $uid=$_SESSION['user_id'];
$rid=(int)($_GET['rapport_id']??0);
if(!$rid){header('Location:../dashboard_controleur.php');exit;}
$stmt=$db->prepare("SELECT * FROM rapports WHERE id=?");$stmt->bind_param('i',$rid);$stmt->execute();
$rapport=$stmt->get_result()->fetch_assoc();
if(!$rapport){header('Location:../dashboard_controleur.php');exit;}
$existing=$db->query("SELECT * FROM fiche_tracabilite_grumes WHERE rapport_id=$rid")->fetch_assoc();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'&&$rapport['statut']==='brouillon'){
    $grumes=$_POST['grumes']??[];
    $grume_json=json_encode(array_values($grumes));
    $nc=sanitize($_POST['nom_controleur']??'');
    $dc=sanitize($_POST['date_controle']??date('Y-m-d'));
    $obs=sanitize($_POST['observations']??'');
    if($existing){
        $stmt2=$db->prepare("UPDATE fiche_tracabilite_grumes SET nom_controleur=?,date_controle=?,grume_json=?,observations=? WHERE rapport_id=?");
        $stmt2->bind_param('ssssi',$nc,$dc,$grume_json,$obs,$rid);
    }else{
        $stmt2=$db->prepare("INSERT INTO fiche_tracabilite_grumes (rapport_id,nom_controleur,date_controle,grume_json,observations) VALUES (?,?,?,?,?)");
        $stmt2->bind_param('issss',$rid,$nc,$dc,$grume_json,$obs);
    }
    if($stmt2->execute()){$msg='success';$existing=$db->query("SELECT * FROM fiche_tracabilite_grumes WHERE rapport_id=$rid")->fetch_assoc();}
    else $msg='error';
}
$grumes=[];
if($existing&&$existing['grume_json'])$grumes=json_decode($existing['grume_json'],true)??[];
if(empty($grumes))$grumes=[['essence'=>'','num_df10'=>'','code_barre'=>'','date_abattage'=>'','num_seq'=>'','n_ligne'=>'','n_ordre'=>'','n_fiche'=>'','volume'=>'','diam_pb'=>'','diam_gb'=>'','long'=>'','n_lv'=>'','affectation'=>'']];
$pageTitle='Fiche Traçabilité Grumes';
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">📦 Traçabilité Forêt Grumes</h1>
    <div class="breadcrumb"><a href="../dashboard_controleur.php">Tableau de bord</a> › <a href="rapport_edit.php?id=<?=$rid?>">Rapport #<?=$rid?></a> › Traçabilité Grumes</div></div>
    <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary">← Retour</a>
</div>
<?php if($msg==='success'):?><div class="alert alert-success">✅ Fiche enregistrée !</div><?php endif;?>
<?php if($msg==='error'):?><div class="alert alert-error">❌ Erreur d'enregistrement.</div><?php endif;?>
<form method="POST" class="fiche-form-container fade-in">
<div class="fiche-form-header"><img src="../images/logo.png" alt="CUF"/>
    <div><h2>FICHE DE CONTRÔLE — "Traçabilité forêt grumes"</h2><p>Version 02, du 01/04/2026 &nbsp;|&nbsp; Rapport #<?=$rid?></p></div>
</div>
<div class="fiche-form-body">
    <div class="form-row mb-3">
        <div class="form-group"><label>Nom du contrôleur</label><input type="text" name="nom_controleur" class="form-control" value="<?=htmlspecialchars($existing['nom_controleur']??$_SESSION['prenom'].' '.$_SESSION['nom'])?>"/></div>
        <div class="form-group"><label>Date de contrôle</label><input type="date" name="date_controle" class="form-control" value="<?=htmlspecialchars($existing['date_controle']??date('Y-m-d'))?>"/></div>
        <div class="form-group"><label>Titre forestier</label><input type="text" class="form-control" value="<?=htmlspecialchars($rapport['titre_forestier']??'')?>" readonly style="background:#f5f5f5;"/></div>
        <div class="form-group"><label>AAC</label><input type="text" class="form-control" value="<?=htmlspecialchars($rapport['aac']??'')?>" readonly style="background:#f5f5f5;"/></div>
    </div>
    <hr class="divider"/>
    <h3 style="color:var(--vert-fonce);margin-bottom:8px;">Registre des grumes</h3>
    <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:16px;">Enregistrement obligatoire des données lues. Remplissez une ligne par grume contrôlée.</p>
    <div class="table-wrapper">
    <table class="criteres-table" id="grumes-table" style="min-width:1100px;">
        <thead>
            <tr>
                <th>N°</th>
                <th>Essence</th>
                <th>N° DF10</th>
                <th>Code barre</th>
                <th>Date abattage</th>
                <th>N° séquentiel</th>
                <th>N° Ligne</th>
                <th>N° Ordre</th>
                <th>N° Fiche</th>
                <th>Volume (m³)</th>
                <th>Ø PB (cm)</th>
                <th>Ø GB (cm)</th>
                <th>Long (m)</th>
                <th>N° LV</th>
                <th>Affectation/Qualité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="grumes-tbody">
        <?php foreach($grumes as $i=>$g):?>
        <tr>
            <td style="text-align:center;font-weight:700;"><?=$i+1?></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][essence]" value="<?=htmlspecialchars($g['essence']??'')?>" style="min-width:90px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][num_df10]" value="<?=htmlspecialchars($g['num_df10']??'')?>" style="min-width:80px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][code_barre]" value="<?=htmlspecialchars($g['code_barre']??'')?>" style="min-width:80px;"/></td>
            <td><input class="form-control" type="date" name="grumes[<?=$i?>][date_abattage]" value="<?=htmlspecialchars($g['date_abattage']??'')?>" style="min-width:120px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][num_seq]" value="<?=htmlspecialchars($g['num_seq']??'')?>" style="min-width:70px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][n_ligne]" value="<?=htmlspecialchars($g['n_ligne']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][n_ordre]" value="<?=htmlspecialchars($g['n_ordre']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][n_fiche]" value="<?=htmlspecialchars($g['n_fiche']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="number" step="0.01" name="grumes[<?=$i?>][volume]" value="<?=htmlspecialchars($g['volume']??'')?>" style="min-width:70px;"/></td>
            <td><input class="form-control" type="number" name="grumes[<?=$i?>][diam_pb]" value="<?=htmlspecialchars($g['diam_pb']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="number" name="grumes[<?=$i?>][diam_gb]" value="<?=htmlspecialchars($g['diam_gb']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="number" step="0.01" name="grumes[<?=$i?>][long]" value="<?=htmlspecialchars($g['long']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][n_lv]" value="<?=htmlspecialchars($g['n_lv']??'')?>" style="min-width:60px;"/></td>
            <td><input class="form-control" type="text" name="grumes[<?=$i?>][affectation]" value="<?=htmlspecialchars($g['affectation']??'')?>" style="min-width:100px;"/></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row" onclick="this.closest('tr').remove()">✕</button></td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    </div>
    <div style="margin-top:12px;display:flex;gap:10px;align-items:center;">
        <button type="button" id="add-grume-row" class="btn btn-secondary btn-sm">➕ Ajouter une grume</button>
        <span style="font-size:0.8rem;color:var(--gris-texte);">Total grumes : <strong id="grume-count"><?=count($grumes)?></strong></span>
    </div>
    <div class="form-group mt-3"><label>Observations</label><textarea name="observations" class="form-control" rows="3"><?=htmlspecialchars($existing['observations']??'')?></textarea></div>
    <?php if($rapport['statut']==='brouillon'):?>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button>
        <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-secondary btn-lg">← Retour au rapport</a>
    </div>
    <?php else:?>
    <div class="alert alert-info">ℹ️ Rapport en lecture seule (statut: <?=$rapport['statut']?>).</div>
    <?php endif;?>
</div></form>
<script>
// Update grume count on add
document.getElementById('add-grume-row')?.addEventListener('click', function() {
    const tbody = document.getElementById('grumes-tbody');
    const count = tbody.querySelectorAll('tr').length;
    const idx = count;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td style="text-align:center;font-weight:700;">${count+1}</td>
        <td><input class="form-control" type="text" name="grumes[${idx}][essence]" style="min-width:90px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][num_df10]" style="min-width:80px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][code_barre]" style="min-width:80px;"/></td>
        <td><input class="form-control" type="date" name="grumes[${idx}][date_abattage]" style="min-width:120px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][num_seq]" style="min-width:70px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][n_ligne]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][n_ordre]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][n_fiche]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="number" step="0.01" name="grumes[${idx}][volume]" style="min-width:70px;"/></td>
        <td><input class="form-control" type="number" name="grumes[${idx}][diam_pb]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="number" name="grumes[${idx}][diam_gb]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="number" step="0.01" name="grumes[${idx}][long]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][n_lv]" style="min-width:60px;"/></td>
        <td><input class="form-control" type="text" name="grumes[${idx}][affectation]" style="min-width:100px;"/></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row" onclick="this.closest('tr').remove()">✕</button></td>`;
    tbody.appendChild(tr);
    document.getElementById('grume-count').textContent = tbody.querySelectorAll('tr').length;
});
</script>
<?php include '../includes/footer.php';?>
