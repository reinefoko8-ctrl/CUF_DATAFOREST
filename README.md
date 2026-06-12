# 🌿 CUF DataForest — Guide d'installation
**Cameroon United Forests — Système de gestion des évaluations forestières**

---

## 📋 Prérequis

- **XAMPP** (Apache + MySQL + PHP 8.0+)
- Navigateur web moderne (Chrome, Firefox, Edge)
- PHP extensions : `mysqli`, `json`, `mbstring`

---

## 🚀 Installation étape par étape

### 1. Copier le projet dans XAMPP

```
Copiez le dossier `cuf_dataforest/` dans :
C:\xampp\htdocs\cuf_dataforest\
```

### 2. Démarrer XAMPP

- Lancez **XAMPP Control Panel**
- Démarrez **Apache** et **MySQL**

### 3. Créer la base de données

1. Ouvrez votre navigateur → `http://localhost/phpmyadmin`
2. Cliquez sur **Nouvelle base de données**
3. Nom : `cuf_dataforest`, Encodage : `utf8mb4_unicode_ci`
4. Cliquez sur **Créer**
5. Allez dans l'onglet **SQL**
6. Copiez-collez le contenu du fichier `database.sql`
7. Cliquez sur **Exécuter**

### 4. Configurer la connexion (si nécessaire)

Ouvrez le fichier `includes/config.php` et modifiez si besoin :

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Votre utilisateur MySQL
define('DB_PASS', '');            // Votre mot de passe MySQL (vide par défaut dans XAMPP)
define('DB_NAME', 'cuf_dataforest');
define('SITE_URL', 'http://localhost/cuf_dataforest');
```

### 5. Ajouter le logo

Copiez le fichier logo CUF (PNG) dans :
```
cuf_dataforest/images/logo.png
```

### 6. Accéder au site

Ouvrez votre navigateur → `http://localhost/cuf_dataforest`

---

## 🔑 Compte administrateur par défaut

| Champ | Valeur |
|-------|--------|
| Email | `admin@cuf-dataforest.com` |
| Mot de passe | `password` |
| Rôle | Administrateur |

> ⚠️ **Changez ce mot de passe immédiatement** après la première connexion !

---

## 📁 Structure du projet

```
cuf_dataforest/
├── index.php                    ← Page de connexion / inscription
├── logout.php                   ← Déconnexion
├── dashboard_admin.php          ← Tableau de bord administrateur
├── dashboard_controleur.php     ← Tableau de bord contrôleur
├── database.sql                 ← Script SQL de création de la BDD
│
├── includes/
│   ├── config.php               ← Configuration BDD + fonctions
│   ├── header.php               ← En-tête + navbar + sidebar
│   └── footer.php               ← Pied de page
│
├── css/
│   └── style.css                ← Styles (thème vert forêt)
│
├── js/
│   └── app.js                   ← JavaScript interactif
│
├── images/
│   └── logo.png                 ← Logo CUF (à ajouter)
│
└── pages/
    ├── nouveau_rapport.php          ← Créer un nouveau rapport
    ├── rapport_edit.php             ← Hub de remplissage des fiches
    ├── rapport_detail.php           ← Détail d'un rapport
    ├── mes_rapports.php             ← Liste rapports du contrôleur
    ├── rapports_admin.php           ← Gestion rapports (admin)
    ├── generer_pdf.php              ← Génération PDF imprimable
    ├── utilisateurs.php             ← Gestion utilisateurs (admin)
    ├── statistiques.php             ← Statistiques (admin)
    ├── export_global.php            ← Export CSV (admin)
    ├── notifications.php            ← Notifications
    ├── profil.php                   ← Profil utilisateur
    │
    ├── fiche_parc_foret.php         ← Fiche 1 : Parc Forêt
    ├── fiche_abattage.php           ← Fiche 2 : Abattage contrôlé
    ├── fiche_routes.php             ← Fiche 3 : Routes forestières
    ├── fiche_tracabilite.php        ← Fiche 4 : Traçabilité grumes
    ├── fiche_securite_tc.php        ← Fiche 5 : Sécurité tronçonneuses
    ├── fiche_sortie_pieds.php       ← Fiche 6 : Sortie pieds
    ├── fiche_debardage.php          ← Fiche 7 : Débardage
    ├── fiche_pont.php               ← Fiche 8 : Pont forestier
    ├── fiche_post_exploitation.php  ← Fiche 9 : Post exploitation
    ├── fiche_dechets.php            ← Fiche 10 : Déchets en forêt
    └── fiche_base_mecanique.php     ← Fiche 11 : Base mécanique
```

---

## 👤 Rôles utilisateurs

### 🔧 Contrôleur
- Créer un compte (en attente d'activation par l'admin)
- Créer des rapports avec toutes les fiches d'évaluation
- Remplir les 11 fiches d'évaluation
- Ajouter un avis global et soumettre le rapport à l'administrateur
- Générer un PDF complet du rapport
- Consulter ses rapports et les retours de l'admin

### 👔 Administrateur
- Activer / désactiver les comptes utilisateurs
- Consulter tous les rapports soumis
- Valider ou rejeter les rapports avec commentaires
- Voir les statistiques globales
- Exporter les données en CSV
- Gérer les notifications

---

## 📝 Fiches d'évaluation disponibles

| N° | Fiche | Critères | Score max |
|----|-------|----------|-----------|
| 1 | Parc Forêt | 10 critères | /10 |
| 2 | Opérations d'abattage contrôlé | 11 catégories, 5 pieds | /15 par pied |
| 3 | Routes forestières | 10 critères | /10 |
| 4 | Traçabilité forêt grumes | Registre tabulaire | - |
| 5 | Éléments de sécurité tronçonneuses | 8 éléments par scie | - |
| 6 | Sortie pieds | 10 critères | /10 |
| 7 | Débardage | 10 critères | /10 |
| 8 | Construction pont forestier | 10 critères | /10 |
| 9 | Opérations post exploitation | 6 sections Oui/Non | - |
| 10 | Gestion des déchets en forêt | 13 points | /20 |
| 11 | Base mécanique forêt | 12 sections détaillées | - |

---

## 🎨 Personnalisation

**Couleurs** : Modifiez les variables CSS dans `css/style.css` (section `:root`)

**Logo** : Remplacez `images/logo.png` par votre logo CUF

**URL** : Modifiez `SITE_URL` dans `includes/config.php`

---

## 🔒 Sécurité

- Mots de passe hashés avec `password_hash()` (bcrypt)
- Sessions PHP sécurisées
- Validation et échappement de toutes les entrées
- Contrôle d'accès par rôle (admin/contrôleur)
- Protection contre les injections SQL (requêtes préparées)

---

## 📞 Support

Site : **CUF DataForest**
Entreprise : **Cameroon United Forests**
Version : **1.0** — Mai 2026
