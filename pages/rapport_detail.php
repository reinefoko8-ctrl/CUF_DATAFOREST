<?php
// pages/rapport_detail.php
require_once '../includes/config.php';
requireLogin();
$db=getDB(); $uid=$_SESSION['user_id'];
$rid=(int)($_GET['id']??0);
if(!$rid){header('Location:../dashboard_controleur.php');exit;}
$stmt=$db->prepare("SELECT r.*,u.nom,u.prenom,u.email FROM rapports r JOIN users u ON r.controleur_id=u.id WHERE r.id=?");
$stmt->bind_param('i',$rid);$stmt->execute();
$rapport=$stmt->get_result()->fetch_assoc();
if(!$rapport){header('Location:../dashboard_controleur.php');exit;}
if(!isAdmin()&&$rapport['controleur_id']!=$uid){header('Location:../dashboard_controleur.php');exit;}

// Charger totaux fiches
function getTotal($db,$table,$rid,$col='total_points'){
    $r=$db->query("SELECT $col FROM $table WHERE rapport_id=$rid")->fetch_assoc();
    return $r[$col]??null;
}
$totaux=[
    'Parc Forêt'        =>['score'=>getTotal($db,'fiche_parc_foret',$rid),'max'=>10],
    'Abattage contrôlé' =>['score'=>getTotal($db,'fiche_abattage',$rid,'p1_total'),'max'=>15],
    'Routes forestières'=>['score'=>getTotal($db,'fiche_routes_forestieres',$rid),'max'=>10],
    'Sortie pieds'      =>['score'=>getTotal($db,'fiche_sortie_pieds',$rid),'max'=>10],
    'Débardage'         =>['score'=>getTotal($db,'fiche_debardage',$rid),'max'=>10],
    'Pont forestier'    =>['score'=>getTotal($db,'fiche_pont_forestier',$rid),'max'=>10],
    'Déchets forêt'     =>['score'=>getTotal($db,'fiche_dechets_foret',$rid),'max'=>20],
];
$pageTitle='Rapport #'.$rid;
include '../includes/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title">📋 <?=htmlspecialchars($rapport['titre']??'Rapport #'.$rid)?></h1>
        <div class="breadcrumb">
            <?php if(isAdmin()):?><a href="../dashboard_admin.php">Admin</a> › <a href="rapports_admin.php">Rapports</a>
            <?php else:?><a href="../dashboard_controleur.php">Tableau de bord</a> › <a href="mes_rapports.php">Mes rapports</a><?php endif;?>
            › Rapport #<?=$rid?>
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="generer_pdf.php?id=<?=$rid?>" class="btn btn-secondary" target="_blank">📄 Générer PDF</a>
        <?php if(!isAdmin()&&$rapport['statut']==='brouillon'):?>
        <a href="rapport_edit.php?id=<?=$rid?>" class="btn btn-primary">✏️ Modifier</a>
        <?php endif;?>
    </div>
</div>
<?php if(isset($_GET['submitted'])):?><div class="alert alert-success">✅ Rapport soumis avec succès à l'administrateur !</div><?php endif;?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
<!-- Informations -->
<div class="card fade-in">
    <div class="card-header"><span class="card-title">ℹ️ Informations générales</span></div>
    <div class="card-body">
        <table style="width:100%;font-size:0.87rem;">
            <tr><td style="color:var(--gris-texte);padding:6px 0;width:140px;">Contrôleur</td><td><strong><?=htmlspecialchars($rapport['prenom'].' '.$rapport['nom'])?></strong></td></tr>
            <tr><td style="color:var(--gris-texte);padding:6px 0;">Titre forestier</td><td><?=htmlspecialchars($rapport['titre_forestier']??'-')?></td></tr>
            <tr><td style="color:var(--gris-texte);padding:6px 0;">AAC</td><td><?=htmlspecialchars($rapport['aac']??'-')?></td></tr>
            <tr><td style="color:var(--gris-texte);padding:6px 0;">Date rapport</td><td><?=date('d/m/Y',strtotime($rapport['date_rapport']))?></td></tr>
            <tr><td style="color:var(--gris-texte);padding:6px 0;">Soumission</td><td><?=$rapport['date_soumission']?date('d/m/Y H:i',strtotime($rapport['date_soumission'])):'-'?></td></tr>
            <tr><td style="color:var(--gris-texte);padding:6px 0;">Statut</td>
                <td><?php $b=['brouillon'=>'secondary','soumis'=>'warning','validé'=>'success','rejeté'=>'danger'];?>
                <span class="badge badge-<?=$b[$rapport['statut']]??'secondary'?>"><?=ucfirst($rapport['statut'])?></span></td></tr>
        </table>
        <?php if($rapport['avis_global']):?>
        <hr class="divider"/><strong style="font-size:0.85rem;">Avis du contrôleur :</strong>
        <p style="font-size:0.84rem;color:var(--gris-texte);margin-top:6px;"><?=nl2br(htmlspecialchars($rapport['avis_global']))?></p>
        <?php endif;?>
        <?php if($rapport['commentaire_admin']):?>
        <hr class="divider"/>
        <strong style="font-size:0.85rem;color:var(--bleu-info);">Commentaire de l'administrateur :</strong>
        <p style="font-size:0.84rem;color:var(--gris-texte);margin-top:6px;"><?=nl2br(htmlspecialchars($rapport['commentaire_admin']))?></p>
        <?php endif;?>
    </div>
</div>
<!-- Scores -->
<div class="card fade-in">
    <div class="card-header"><span class="card-title">📊 Scores des fiches</span></div>
    <div class="card-body">
        <?php foreach($totaux as $nom=>$t):
            $pct=$t['max']>0&&$t['score']!==null?round($t['score']/$t['max']*100):null;
            $color=$pct===null?'#ccc':($pct>=70?'var(--vert-clair)':($pct>=40?'var(--orange-alerte)':'var(--rouge))'));
        ?>
        <div style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;font-size:0.83rem;margin-bottom:4px;">
                <span><?=$nom?></span>
                <span style="font-weight:700;color:<?=$color?>"><?=$t['score']!==null?$t['score'].'/'.$t['max']:'Non remplie'?></span>
            </div>
            <div style="height:7px;background:var(--gris-moyen);border-radius:4px;">
                <div style="height:100%;width:<?=$pct??0?>%;background:<?=$color?>;border-radius:4px;transition:width 0.5s;"></div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>
</div>

<!-- Fiches remplies -->
<div class="card fade-in">
    <div class="card-header"><span class="card-title">📝 Accès aux fiches</span></div>
    <div class="card-body">
        <div class="fiches-grid">
        <?php
        $flist=[
            ['🌳','Parc Forêt','fiche_parc_foret','fiche_parc_foret.php'],
            ['🪓','Abattage','fiche_abattage','fiche_abattage.php'],
            ['🛤️','Routes','fiche_routes_forestieres','fiche_routes.php'],
            ['📦','Traçabilité','fiche_tracabilite_grumes','fiche_tracabilite.php'],
            ['⚙️','Sécurité TC','fiche_securite_tronconneuses','fiche_securite_tc.php'],
            ['👣','Sortie pieds','fiche_sortie_pieds','fiche_sortie_pieds.php'],
            ['🚜','Débardage','fiche_debardage','fiche_debardage.php'],
            ['🌉','Pont','fiche_pont_forestier','fiche_pont.php'],
            ['🔄','Post exploitation','fiche_post_exploitation','fiche_post_exploitation.php'],
            ['♻️','Déchets','fiche_dechets_foret','fiche_dechets.php'],
            ['🔧','Base mécanique','fiche_base_mecanique','fiche_base_mecanique.php'],
        ];
        foreach($flist as[$ico,$nom,$table,$url]):
            $filled=$db->query("SELECT COUNT(*) FROM $table WHERE rapport_id=$rid")->fetch_row()[0]>0;
        ?>
        <a href="<?=$url?>?rapport_id=<?=$rid?>" class="fiche-card <?=$filled?'remplie':''?>" style="min-height:90px;">
            <span class="fiche-icon" style="font-size:1.5rem;"><?=$ico?></span>
            <h3 style="font-size:0.85rem;"><?=$nom?></h3>
            <div class="fiche-status <?=$filled?'ok':'pending'?>" style="font-size:0.75rem;">
                <?=$filled?'✅ Remplie':'⊙ Non remplie'?>
            </div>
        </a>
        <?php endforeach;?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php';?>
