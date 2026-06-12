<?php
// pages/notifications.php
require_once '../includes/config.php';
requireLogin();
$db=getDB(); $uid=$_SESSION['user_id'];
$pageTitle='Notifications';
// Marquer tout lu
if(isset($_GET['mark_all'])){$db->query("UPDATE notifications SET lu=1 WHERE user_id=$uid");header('Location:notifications.php');exit;}
$notifs=$db->query("SELECT * FROM notifications WHERE user_id=$uid ORDER BY date_creation DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
// Marquer lu
if(isset($_GET['read'])){$nid=(int)$_GET['read'];$db->query("UPDATE notifications SET lu=1 WHERE id=$nid AND user_id=$uid");}
include '../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">🔔 Notifications</h1><div class="breadcrumb">Toutes vos notifications</div></div>
    <a href="notifications.php?mark_all=1" class="btn btn-secondary btn-sm">✅ Tout marquer comme lu</a>
</div>
<div class="card fade-in">
    <div class="card-body" style="padding:0;">
    <?php foreach($notifs as $n):?>
    <div style="padding:16px 24px;border-bottom:1px solid var(--gris-moyen);display:flex;gap:12px;align-items:flex-start;background:<?=$n['lu']?'white':'#f0fff0'?>;">
        <div style="font-size:1.5rem;"><?=$n['lu']?'🔔':'🆕'?></div>
        <div style="flex:1;">
            <div style="font-size:0.87rem;"><?=htmlspecialchars($n['message'])?></div>
            <div style="font-size:0.77rem;color:var(--gris-texte);margin-top:4px;"><?=date('d/m/Y à H:i',strtotime($n['date_creation']))?></div>
        </div>
        <div style="display:flex;gap:6px;">
            <?php if($n['rapport_id']):?>
            <a href="rapport_detail.php?id=<?=$n['rapport_id']?>" class="btn btn-sm btn-primary">👁 Voir rapport</a>
            <?php endif;?>
            <?php if(!$n['lu']):?>
            <a href="notifications.php?read=<?=$n['id']?>" class="btn btn-sm btn-secondary">✅ Lu</a>
            <?php endif;?>
        </div>
    </div>
    <?php endforeach;?>
    <?php if(empty($notifs)):?>
    <div style="padding:40px;text-align:center;color:var(--gris-texte);">🔔 Aucune notification pour le moment.</div>
    <?php endif;?>
    </div>
</div>
<?php include '../includes/footer.php';?>
