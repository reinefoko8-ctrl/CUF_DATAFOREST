<?php
// pages/mes_rapports.php
require_once '../includes/config.php';
requireLogin();
$db=getDB(); $uid=$_SESSION['user_id'];
$pageTitle='Mes rapports';
$rapports=$db->query("SELECT * FROM rapports WHERE controleur_id=$uid ORDER BY date_creation DESC")->fetch_all(MYSQLI_ASSOC);

// Supprimer un brouillon
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['delete_id'])){
    $did=(int)$_POST['delete_id'];
    $db->query("DELETE FROM rapports WHERE id=$did AND controleur_id=$uid AND statut='brouillon'");
    header('Location: mes_rapports.php?deleted=1');exit;
}
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">📋 Mes rapports</h1>
    <div class="breadcrumb"><a href="../dashboard_controleur.php">Tableau de bord</a> › Mes rapports</div></div>
    <a href="nouveau_rapport.php" class="btn btn-primary">➕ Nouveau rapport</a>
</div>
<?php if(isset($_GET['deleted'])):?><div class="alert alert-success">🗑️ Brouillon supprimé.</div><?php endif;?>
<div class="card fade-in">
    <div class="card-header"><span class="card-title">📋 <?=count($rapports)?> rapport(s)</span>
        <input type="text" id="table-search" class="form-control" style="width:200px;" placeholder="🔍 Rechercher..."/>
    </div>
    <div class="card-body" style="padding:0;">
    <div class="table-wrapper"><table>
        <thead><tr><th>#</th><th>Titre</th><th>TF</th><th>AAC</th><th>Date rapport</th><th>Créé le</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($rapports as $r):$b=['brouillon'=>'secondary','soumis'=>'warning','validé'=>'success','rejeté'=>'danger'];?>
        <tr>
            <td><strong>#<?=$r['id']?></strong></td>
            <td><?=htmlspecialchars($r['titre']??'Rapport #'.$r['id'])?></td>
            <td><?=htmlspecialchars($r['titre_forestier']??'-')?></td>
            <td><?=htmlspecialchars($r['aac']??'-')?></td>
            <td><?=date('d/m/Y',strtotime($r['date_rapport']))?></td>
            <td><?=date('d/m/Y',strtotime($r['date_creation']))?></td>
            <td><span class="badge badge-<?=$b[$r['statut']]??'secondary'?>"><?=ucfirst($r['statut'])?></span></td>
            <td style="display:flex;gap:5px;flex-wrap:wrap;">
                <a href="rapport_detail.php?id=<?=$r['id']?>" class="btn btn-sm btn-primary">👁 Voir</a>
                <?php if($r['statut']==='brouillon'):?>
                <a href="rapport_edit.php?id=<?=$r['id']?>" class="btn btn-sm btn-secondary">✏️ Modifier</a>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?=$r['id']?>"/>
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce brouillon ?')">🗑️</button>
                </form>
                <?php endif;?>
                <a href="generer_pdf.php?id=<?=$r['id']?>" class="btn btn-sm btn-info" target="_blank">📄 PDF</a>
            </td>
        </tr>
        <?php endforeach;?>
        <?php if(empty($rapports)):?><tr><td colspan="8" class="text-center" style="padding:32px;">Aucun rapport. <a href="nouveau_rapport.php" style="color:var(--vert-moyen);font-weight:600;">Créer votre premier rapport →</a></td></tr><?php endif;?>
        </tbody>
    </table></div>
    </div>
</div>
<?php include '../includes/footer.php';?>
