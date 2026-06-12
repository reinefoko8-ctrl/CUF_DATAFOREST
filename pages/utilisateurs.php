<?php
require_once '../includes/config.php';
requireAdmin();
$db=getDB();
$pageTitle='Gestion des utilisateurs';
$filter=sanitize($_GET['filter']??'');

// Actions
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=sanitize($_POST['action']??'');
    $user_id=(int)($_POST['user_id']??0);
    if($user_id&&in_array($action,['activer','desactiver','supprimer'])){
        if($action==='activer')$db->query("UPDATE users SET statut='actif' WHERE id=$user_id");
        elseif($action==='desactiver')$db->query("UPDATE users SET statut='inactif' WHERE id=$user_id");
        elseif($action==='supprimer')$db->query("DELETE FROM users WHERE id=$user_id AND role!='administrateur'");
        header('Location: utilisateurs.php?done=1');exit;
    }
}

$where=$filter?("WHERE statut='".$db->real_escape_string($filter)."'"):'';
$users=$db->query("SELECT *,(SELECT COUNT(*) FROM rapports WHERE controleur_id=users.id) as nb_rapports FROM users $where ORDER BY role,date_creation DESC")->fetch_all(MYSQLI_ASSOC);
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">👥 Gestion des utilisateurs</h1>
    <div class="breadcrumb"><a href="../dashboard_admin.php">Tableau de bord</a> › Utilisateurs</div></div>
</div>
<?php if(isset($_GET['done'])):?><div class="alert alert-success">✅ Action effectuée.</div><?php endif;?>

<!-- Filtres rapides -->
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
    <a href="utilisateurs.php" class="btn <?=!$filter?'btn-primary':'btn-secondary'?> btn-sm">Tous</a>
    <a href="utilisateurs.php?filter=actif" class="btn <?=$filter==='actif'?'btn-primary':'btn-secondary'?> btn-sm">✅ Actifs</a>
    <a href="utilisateurs.php?filter=en_attente" class="btn <?=$filter==='en_attente'?'btn-primary':'btn-secondary'?> btn-sm">⏳ En attente</a>
    <a href="utilisateurs.php?filter=inactif" class="btn <?=$filter==='inactif'?'btn-primary':'btn-secondary'?> btn-sm">❌ Inactifs</a>
</div>

<div class="card fade-in">
    <div class="card-header"><span class="card-title">👥 <?=count($users)?> utilisateur(s)</span>
        <input type="text" id="table-search" class="form-control" style="width:200px;" placeholder="🔍 Rechercher..."/>
    </div>
    <div class="card-body" style="padding:0;">
    <div class="table-wrapper"><table>
        <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th>TF / AAC</th><th>Rapports</th><th>Statut</th><th>Inscription</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($users as $u):
            $bs=['actif'=>'success','inactif'=>'danger','en_attente'=>'warning'];
        ?>
        <tr>
            <td><?=$u['id']?></td>
            <td><strong><?=htmlspecialchars($u['prenom'].' '.$u['nom'])?></strong></td>
            <td><?=htmlspecialchars($u['email'])?></td>
            <td><span class="badge badge-<?=$u['role']==='administrateur'?'info':'secondary'?>">
                <?=$u['role']==='administrateur'?'👔 Admin':'👷 Contrôleur'?></span></td>
            <td><?=htmlspecialchars($u['titre_forestier']??'-')?> / <?=htmlspecialchars($u['aac']??'-')?></td>
            <td><?=$u['nb_rapports']?></td>
            <td><span class="badge badge-<?=$bs[$u['statut']]??'secondary'?>"><?=ucfirst(str_replace('_',' ',$u['statut']))?></span></td>
            <td><?=date('d/m/Y',strtotime($u['date_creation']))?></td>
            <td>
                <form method="POST" style="display:flex;gap:4px;flex-wrap:wrap;">
                    <input type="hidden" name="user_id" value="<?=$u['id']?>"/>
                    <?php if($u['statut']==='en_attente'||$u['statut']==='inactif'):?>
                    <button type="submit" name="action" value="activer" class="btn btn-sm btn-primary">✅ Activer</button>
                    <?php endif;?>
                    <?php if($u['statut']==='actif'):?>
                    <button type="submit" name="action" value="desactiver" class="btn btn-sm btn-warning">⏸️ Désactiver</button>
                    <?php endif;?>
                    <?php if($u['id']!==$_SESSION['user_id']&&$u['role']!=='administrateur'):?>
                    <button type="submit" name="action" value="supprimer" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️</button>
                    <?php endif;?>
                </form>
            </td>
        </tr>
        <?php endforeach;?>
        <?php if(empty($users)):?><tr><td colspan="9" class="text-center" style="padding:28px;">Aucun utilisateur trouvé.</td></tr><?php endif;?>
        </tbody>
    </table></div>
    </div>
</div>
<?php include '../includes/footer.php';?>
