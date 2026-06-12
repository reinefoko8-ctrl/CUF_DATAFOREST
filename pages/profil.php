<?php
// pages/profil.php
require_once '../includes/config.php';
requireLogin();
$db=getDB(); $uid=$_SESSION['user_id'];
$pageTitle='Mon profil';
$user=$db->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nom=sanitize($_POST['nom']??'');
    $prenom=sanitize($_POST['prenom']??'');
    $tf=sanitize($_POST['titre_forestier']??'');
    $aac=sanitize($_POST['aac']??'');
    $pw=$_POST['password']??'';
    $confirm=$_POST['confirm']??'';
    if($nom&&$prenom){
        if($pw){
            if($pw!==$confirm){$msg='error_pw';}
            elseif(strlen($pw)<6){$msg='error_short';}
            else{
                $hash=password_hash($pw,PASSWORD_DEFAULT);
                $stmt=$db->prepare("UPDATE users SET nom=?,prenom=?,titre_forestier=?,aac=?,password=? WHERE id=?");
                $stmt->bind_param('sssssi',$nom,$prenom,$tf,$aac,$hash,$uid);$stmt->execute();$msg='ok';
            }
        }else{
            $stmt=$db->prepare("UPDATE users SET nom=?,prenom=?,titre_forestier=?,aac=? WHERE id=?");
            $stmt->bind_param('ssssi',$nom,$prenom,$tf,$aac,$uid);$stmt->execute();$msg='ok';
        }
        if($msg==='ok'){$_SESSION['nom']=$nom;$_SESSION['prenom']=$prenom;$_SESSION['titre_forestier']=$tf;$_SESSION['aac']=$aac;
            $user=$db->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();}
    }
}
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">👤 Mon profil</h1><div class="breadcrumb">Modifier vos informations personnelles</div></div>
</div>
<?php if($msg==='ok'):?><div class="alert alert-success">✅ Profil mis à jour !</div><?php endif;?>
<?php if($msg==='error_pw'):?><div class="alert alert-error">❌ Les mots de passe ne correspondent pas.</div><?php endif;?>
<?php if($msg==='error_short'):?><div class="alert alert-error">❌ Mot de passe trop court (6 caractères minimum).</div><?php endif;?>

<div class="card fade-in" style="max-width:600px;">
    <div class="card-header">
        <span class="card-title">✏️ Modifier mes informations</span>
        <span class="badge badge-<?=$user['role']==='administrateur'?'info':'secondary'?>"><?=ucfirst($user['role'])?></span>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group"><label>Nom *</label><input type="text" name="nom" class="form-control" value="<?=htmlspecialchars($user['nom'])?>" required/></div>
                <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" class="form-control" value="<?=htmlspecialchars($user['prenom'])?>" required/></div>
            </div>
            <div class="form-group"><label>Email</label><input type="email" class="form-control" value="<?=htmlspecialchars($user['email'])?>" disabled style="background:#f5f5f5;"/></div>
            <div class="form-row">
                <div class="form-group"><label>Titre forestier</label><input type="text" name="titre_forestier" class="form-control" value="<?=htmlspecialchars($user['titre_forestier']??'')?>"/></div>
                <div class="form-group"><label>AAC</label><input type="text" name="aac" class="form-control" value="<?=htmlspecialchars($user['aac']??'')?>"/></div>
            </div>
            <hr class="divider"/>
            <p style="font-size:0.82rem;color:var(--gris-texte);margin-bottom:12px;">Laissez vide pour ne pas changer le mot de passe.</p>
            <div class="form-row">
                <div class="form-group"><label>Nouveau mot de passe</label><input type="password" name="password" class="form-control" placeholder="Min. 6 caractères"/></div>
                <div class="form-group"><label>Confirmer</label><input type="password" name="confirm" class="form-control" placeholder="Répétez"/></div>
            </div>
            <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
        </form>
    </div>
</div>

<div class="card fade-in" style="max-width:600px;">
    <div class="card-header"><span class="card-title">📊 Mes statistiques</span></div>
    <div class="card-body">
    <?php
    $nb_total=$db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid")->fetch_row()[0];
    $nb_val=$db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid AND statut='validé'")->fetch_row()[0];
    $nb_soumis=$db->query("SELECT COUNT(*) FROM rapports WHERE controleur_id=$uid AND statut='soumis'")->fetch_row()[0];
    ?>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
        <div style="text-align:center;background:var(--vert-pale);padding:16px;border-radius:var(--radius-sm);">
            <div style="font-size:1.8rem;font-weight:700;color:var(--vert-fonce);"><?=$nb_total?></div>
            <div style="font-size:0.8rem;color:var(--gris-texte);">Total rapports</div>
        </div>
        <div style="text-align:center;background:var(--vert-pale);padding:16px;border-radius:var(--radius-sm);">
            <div style="font-size:1.8rem;font-weight:700;color:var(--vert-moyen);"><?=$nb_val?></div>
            <div style="font-size:0.8rem;color:var(--gris-texte);">Validés</div>
        </div>
        <div style="text-align:center;background:#fff3e0;padding:16px;border-radius:var(--radius-sm);">
            <div style="font-size:1.8rem;font-weight:700;color:var(--orange-alerte);"><?=$nb_soumis?></div>
            <div style="font-size:0.8rem;color:var(--gris-texte);">En attente</div>
        </div>
    </div>
    <div style="margin-top:12px;font-size:0.82rem;color:var(--gris-texte);">
        Membre depuis le <?=date('d/m/Y',strtotime($user['date_creation']))?>.
        <?php if($user['derniere_connexion']):?>Dernière connexion : <?=date('d/m/Y à H:i',strtotime($user['derniere_connexion']))?>.<?php endif;?>
    </div>
    </div>
</div>
<?php include '../includes/footer.php';?>
