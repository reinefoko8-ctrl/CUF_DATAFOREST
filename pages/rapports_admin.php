<?php
require_once '../includes/config.php';
requireAdmin();
$db=getDB();
$pageTitle='Gestion des rapports';

// Filtres
$filter_statut=sanitize($_GET['statut']??'');
$filter_search=sanitize($_GET['q']??'');

$where='WHERE 1=1';
$params=[];$types='';
if($filter_statut){$where.=" AND r.statut=?";$params[]=$filter_statut;$types.='s';}
if($filter_search){$where.=" AND (u.nom LIKE ? OR u.prenom LIKE ? OR r.titre LIKE ? OR r.titre_forestier LIKE ?)";
    $s="%$filter_search%";$params=array_merge($params,[$s,$s,$s,$s]);$types.='ssss';}

$stmt=$db->prepare("SELECT r.*,u.nom,u.prenom,u.titre_forestier as tf FROM rapports r JOIN users u ON r.controleur_id=u.id $where ORDER BY r.date_creation DESC");
if($types)$stmt->bind_param($types,...$params);
$stmt->execute();
$rapports=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Validation/rejet
if($_SERVER['REQUEST_METHOD']==='POST'){
    $rid=(int)($_POST['rapport_id']??0);
    $action=sanitize($_POST['action_rapport']??'');
    $comment=sanitize($_POST['commentaire']??'');
    if($rid&&in_array($action,['valider','rejeter'])){
        $statut=$action==='valider'?'validé':'rejeté';
        $stmt2=$db->prepare("UPDATE rapports SET statut=?,commentaire_admin=?,admin_id=?,date_validation=NOW() WHERE id=?");
        $ai=$_SESSION['user_id'];
        $stmt2->bind_param('ssii',$statut,$comment,$ai,$rid);
        $stmt2->execute();
        // Notifier le controleur (requete preparee pour eviter erreurs apostrophe)
        $r = $db->query("SELECT controleur_id FROM rapports WHERE id=$rid")->fetch_assoc();
        $ci = (int)$r['controleur_id'];
        $nm = "Votre rapport #" . $rid . " a ete " . $statut . " par l administrateur.";
        $type_notif = "rapport_" . $statut;
        $stmt3 = $db->prepare("INSERT INTO notifications (user_id, type, message, rapport_id) VALUES (?, ?, ?, ?)");
        $stmt3->bind_param('issi', $ci, $type_notif, $nm, $rid);
        $stmt3->execute();
        header('Location: rapports_admin.php?updated=1'); exit;
    }
}
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">📋 Tous les rapports</h1>
    <div class="breadcrumb"><a href="../dashboard_admin.php">Tableau de bord</a> › Rapports</div></div>
</div>
<?php if(isset($_GET['updated'])):?><div class="alert alert-success">✅ Rapport mis à jour.</div><?php endif;?>

<!-- Filtres -->
<div class="card fade-in mb-3">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group mb-0">
                <label>Statut</label>
                <select name="statut" class="form-control" style="width:160px;">
                    <option value="">Tous</option>
                    <?php foreach(['brouillon','soumis','validé','rejeté'] as $s):?>
                    <option value="<?=$s?>" <?=$filter_statut===$s?'selected':''?>><?=ucfirst($s)?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group mb-0">
                <label>Recherche</label>
                <input type="text" name="q" class="form-control" style="width:220px;" placeholder="Nom, titre, TF..." value="<?=$filter_search?>"/>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
            <a href="rapports_admin.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <span class="card-title">📋 <?=count($rapports)?> rapport(s) trouvé(s)</span>
        <input type="text" id="table-search" class="form-control" style="width:200px;" placeholder="🔍 Rechercher..."/>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
        <table>
            <thead><tr>
                <th>#</th><th>Contrôleur</th><th>Titre</th><th>Titre forestier</th>
                <th>AAC</th><th>Date rapport</th><th>Soumission</th><th>Statut</th><th>Actions</th>
            </tr></thead>
            <tbody>
            <?php foreach($rapports as $r):
                $b=['brouillon'=>'secondary','soumis'=>'warning','validé'=>'success','rejeté'=>'danger'];
            ?>
            <tr>
                <td><strong>#<?=$r['id']?></strong></td>
                <td><?=htmlspecialchars($r['prenom'].' '.$r['nom'])?></td>
                <td><?=htmlspecialchars($r['titre']??'Rapport #'.$r['id'])?></td>
                <td><?=htmlspecialchars($r['titre_forestier']??'-')?></td>
                <td><?=htmlspecialchars($r['aac']??'-')?></td>
                <td><?=date('d/m/Y',strtotime($r['date_rapport']))?></td>
                <td><?=$r['date_soumission']?date('d/m/Y',strtotime($r['date_soumission'])):'-'?></td>
                <td><span class="badge badge-<?=$b[$r['statut']]??'secondary'?>"><?=ucfirst($r['statut'])?></span></td>
                <td style="display:flex;gap:5px;flex-wrap:wrap;">
                    <a href="rapport_detail.php?id=<?=$r['id']?>" class="btn btn-sm btn-primary">👁 Voir</a>
                    <a href="generer_pdf.php?id=<?=$r['id']?>" class="btn btn-sm btn-secondary" target="_blank">📄 PDF</a>
                    <?php if($r['statut']==='soumis'):?>
                    <button type="button" class="btn btn-sm btn-info" onclick="openValidModal(<?=$r['id']?>,'valider')">✅ Valider</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="openValidModal(<?=$r['id']?>,'rejeter')">❌ Rejeter</button>
                    <?php endif;?>
                </td>
            </tr>
            <?php endforeach;?>
            <?php if(empty($rapports)):?><tr><td colspan="9" class="text-center" style="padding:28px;color:var(--gris-texte);">Aucun rapport trouvé.</td></tr><?php endif;?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Modal validation -->
<div id="valid-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:var(--radius);padding:32px;max-width:480px;width:90%;box-shadow:var(--ombre-forte);">
        <h3 id="modal-title" style="color:var(--vert-fonce);margin-bottom:16px;"></h3>
        <form method="POST">
            <input type="hidden" name="rapport_id" id="modal-rid"/>
            <input type="hidden" name="action_rapport" id="modal-action"/>
            <div class="form-group">
                <label>Commentaire (optionnel)</label>
                <textarea name="commentaire" class="form-control" rows="4" placeholder="Votre commentaire à l'intention du contrôleur..."></textarea>
            </div>
            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('valid-modal').style.display='none'">Annuler</button>
                <button type="submit" id="modal-btn" class="btn btn-primary">Confirmer</button>
            </div>
        </form>
    </div>
</div>
<script>
function openValidModal(rid, action) {
    document.getElementById('modal-rid').value = rid;
    document.getElementById('modal-action').value = action;
    document.getElementById('modal-title').textContent = action === 'valider' ? '✅ Valider le rapport #' + rid : '❌ Rejeter le rapport #' + rid;
    document.getElementById('modal-btn').className = 'btn ' + (action === 'valider' ? 'btn-primary' : 'btn-danger');
    document.getElementById('modal-btn').textContent = action === 'valider' ? '✅ Confirmer la validation' : '❌ Confirmer le rejet';
    document.getElementById('valid-modal').style.display = 'flex';
}
</script>
<?php include '../includes/footer.php';?>
