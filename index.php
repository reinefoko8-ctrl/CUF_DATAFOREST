<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'dashboard_admin.php' : 'dashboard_controleur.php'));
    exit;
}

$error = '';
$success = '';
$active_tab = $_GET['tab'] ?? 'login'; // login | register_ctrl | register_admin

// ---- CONNEXION ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = sanitize($_POST['role'] ?? '');

    if ($email && $password && $role) {
        $db   = getDB();
        // Administrateurs : accepter actif ET en_attente (premier admin)
        // Contrôleurs : seulement actif
        if ($role === 'administrateur') {
            $stmt = $db->prepare("SELECT * FROM users WHERE email=? AND role='administrateur'");
            $stmt->bind_param('s', $email);
        } else {
            $stmt = $db->prepare("SELECT * FROM users WHERE email=? AND role=? AND statut='actif'");
            $stmt->bind_param('ss', $email, $role);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Activer automatiquement l'admin si encore en_attente
            if ($user['role'] === 'administrateur' && $user['statut'] === 'en_attente') {
                $db->query("UPDATE users SET statut='actif' WHERE id=" . (int)$user['id']);
                $user['statut'] = 'actif';
            }
            if ($user['statut'] !== 'actif') {
                $error = 'Votre compte est désactivé. Contactez un administrateur.';
                $active_tab = 'login';
                goto end_login;
            }
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['nom']      = $user['nom'];
            $_SESSION['prenom']   = $user['prenom'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['titre_forestier'] = $user['titre_forestier'];
            $_SESSION['aac']      = $user['aac'];

            // Update last login
            $db->query("UPDATE users SET derniere_connexion=NOW() WHERE id=" . $user['id']);

            header('Location: ' . ($user['role'] === 'administrateur' ? 'dashboard_admin.php' : 'dashboard_controleur.php'));
            exit;
        } else {
            $error = 'Email, mot de passe ou rôle incorrect. Vérifiez aussi que votre compte est activé.';
            $active_tab = 'login';
        }
        end_login:
    } else {
        $error = 'Veuillez remplir tous les champs.';
        $active_tab = 'login';
    }
}

// ---- INSCRIPTION ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $nom      = sanitize($_POST['nom'] ?? '');
    $prenom   = sanitize($_POST['prenom'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role     = sanitize($_POST['role'] ?? 'controleur');
    $titre    = sanitize($_POST['titre_forestier'] ?? '');
    $aac      = sanitize($_POST['aac'] ?? '');

    if ($nom && $prenom && $email && $password) {
        if ($password !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } else {
            $db   = getDB();
            $check = $db->prepare("SELECT id FROM users WHERE email=?");
            $check->bind_param('s', $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = 'Cet email est déjà utilisé.';
            } else {
                $hash   = password_hash($password, PASSWORD_DEFAULT);
                // Les administrateurs sont activés directement, les contrôleurs doivent être validés
                $statut = ($role === 'administrateur') ? 'actif' : 'en_attente';
                $stmt   = $db->prepare("INSERT INTO users (nom,prenom,email,password,role,titre_forestier,aac,statut) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->bind_param('ssssssss', $nom, $prenom, $email, $hash, $role, $titre, $aac, $statut);

                if ($stmt->execute()) {
                    if ($role === 'administrateur') {
                        $success = '✅ Compte administrateur créé avec succès ! Vous pouvez maintenant vous connecter.';
                    } else {
                        $success = '✅ Compte contrôleur créé ! Un administrateur doit valider votre compte avant que vous puissiez vous connecter.';
                    }
                    $active_tab = 'login';
                } else {
                    $error = 'Erreur lors de la création du compte.';
                }
            }
        }
    } else {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    }
    if ($error) $active_tab = 'register';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>CUF DataForest — Connexion</title>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="icon" type="image/png" href="images/logo.png"/>
    <style>
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .register-link { font-size:0.82rem; text-align:center; margin-top:16px; color:var(--gris-texte); }
        .register-link a { color:var(--vert-moyen); font-weight:600; text-decoration:none; }
        .register-link a:hover { text-decoration:underline; }
        .divider-text { text-align:center; position:relative; margin:16px 0; }
        .divider-text::before { content:''; position:absolute; top:50%; left:0; right:0; border-top:1px solid var(--gris-moyen); }
        .divider-text span { background:white; position:relative; padding:0 12px; font-size:0.78rem; color:var(--gris-texte); }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-container fade-in">
        <div class="login-card">

            <!-- GAUCHE -->
            <div class="login-left">
                <img src="images/logo.png" alt="CUF Logo" class="login-logo"/>
                <h1>CUF DataForest</h1>
                <p>Cameroon United Forests<br/>Système de gestion des évaluations forestières</p>
                <div class="login-tagline">🌿 Gérer la forêt, préserver l'avenir</div>
                <div style="margin-top:32px;opacity:0.7;font-size:0.78rem;line-height:1.8;">
                    <div>📋 Fiches d'évaluation numériques</div>
                    <div>📄 Génération PDF automatique</div>
                    <div>📊 Suivi et statistiques</div>
                    <div>🔒 Accès sécurisé</div>
                </div>
            </div>

            <!-- DROITE -->
            <div class="login-right">

                <?php if ($error): ?>
                    <div class="alert alert-error">⚠️ <?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <!-- TABS -->
                <div style="display:flex;gap:8px;margin-bottom:24px;border-bottom:2px solid var(--gris-moyen);padding-bottom:0;">
                    <button data-tab="login" class="role-tab <?= $active_tab==='login'?'active':'' ?>" style="border-radius:8px 8px 0 0;border-bottom:none;margin-bottom:-2px;">
                        🔐 Connexion
                    </button>
                    <button data-tab="register" class="role-tab <?= $active_tab==='register'?'active':'' ?>" style="border-radius:8px 8px 0 0;border-bottom:none;margin-bottom:-2px;">
                        📝 Créer un compte
                    </button>
                </div>

                <!-- CONNEXION -->
                <div class="tab-pane <?= $active_tab==='login'?'active':'' ?>" id="login">
                    <h2>Bon retour !</h2>
                    <p class="subtitle">Connectez-vous à votre espace de travail</p>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="login"/>
                        <div class="form-group">
                            <label>Rôle</label>
                            <div class="role-tabs">
                                <label class="role-tab" style="cursor:pointer;">
                                    <input type="radio" name="role" value="controleur" style="display:none;" checked/>
                                    <span class="icon">👷</span> Contrôleur
                                </label>
                                <label class="role-tab" style="cursor:pointer;">
                                    <input type="radio" name="role" value="administrateur" style="display:none;"/>
                                    <span class="icon">👔</span> Administrateur
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Adresse email</label>
                            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required/>
                        </div>
                        <div class="form-group">
                            <label>Mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required/>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">🔐 Se connecter</button>
                    </form>
                </div>

                <!-- INSCRIPTION -->
                <div class="tab-pane <?= $active_tab==='register'?'active':'' ?>" id="register">
                    <h2>Créer un compte</h2>
                    <p class="subtitle">Remplissez le formulaire — un admin activera votre compte</p>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="register"/>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom *</label>
                                <input type="text" name="nom" class="form-control" placeholder="Nom de famille" required/>
                            </div>
                            <div class="form-group">
                                <label>Prénom *</label>
                                <input type="text" name="prenom" class="form-control" placeholder="Prénom" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required/>
                        </div>
                        <div class="form-group">
                            <label>Rôle *</label>
                            <select name="role" class="form-control">
                                <option value="controleur">👷 Contrôleur</option>
                                <option value="administrateur">👔 Administrateur</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Titre forestier</label>
                                <input type="text" name="titre_forestier" class="form-control" placeholder="Ex: 09023"/>
                            </div>
                            <div class="form-group">
                                <label>AAC</label>
                                <input type="text" name="aac" class="form-control" placeholder="Ex: 6.1"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Mot de passe *</label>
                                <input type="password" name="password" class="form-control" placeholder="Min. 6 caractères" required/>
                            </div>
                            <div class="form-group">
                                <label>Confirmer *</label>
                                <input type="password" name="confirm" class="form-control" placeholder="Répétez" required/>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">📝 Créer mon compte</button>
                    </form>
                </div>

            </div><!-- /.login-right -->
        </div><!-- /.login-card -->
    </div>
</div>

<script src="js/app.js"></script>
<script>
// Radio role tabs style
document.querySelectorAll('.role-tab input[type=radio]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        this.closest('.role-tab').classList.add('active');
    });
    if (radio.checked) radio.closest('.role-tab').classList.add('active');
});
</script>
</body>
</html>
