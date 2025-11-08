# CHAD ENGLISH CENTER - Site Web

Site web officiel du CHAD ENGLISH CENTER, centre de formation en anglais au Tchad.

## 📋 Contenu du projet

- `index.html` - Page d'accueil
- `formations.html` - Page des formations et cours
- `apropos.html` - Page à propos
- `contact.php` - Page de contact avec formulaire
- `assets/css/style.css` - Feuille de styles
- `assets/js/main.js` - Scripts JavaScript
- `README.md` - Ce fichier

## 🚀 Installation et déploiement

### Prérequis

- Un hébergement web avec support PHP 7.4+ et fonction `mail()` activée
- Accès FTP ou panneau de contrôle (cPanel, Plesk, etc.)

### Étapes d'installation

1. **Télécharger les fichiers**
   - Téléchargez tous les fichiers du projet

2. **Uploader sur votre hébergement**
   - Connectez-vous à votre hébergement via FTP (FileZilla, etc.)
   - Uploadez tous les fichiers dans le dossier `public_html` ou `www`
   - Conservez la structure des dossiers

3. **Configurer les permissions**
   - Le dossier `contacts/` doit avoir les permissions 755
   - Le fichier `contact.php` doit avoir les permissions 644

4. **Configurer l'email de contact**
   - Ouvrez le fichier `contact.php`
   - À la ligne 19, remplacez `contact@chadenglishcenter.td` par votre email réel :
   \`\`\`php
   $destinataire = 'votre-email@votredomaine.com';
   \`\`\`

5. **Tester le site**
   - Visitez votre site : `http://votredomaine.com`
   - Testez le formulaire de contact

## ⚙️ Configuration avancée

### Changer l'email de destination

Éditez `contact.php` ligne 19 :
\`\`\`php
$destinataire = 'nouveau-email@exemple.com';
\`\`\`

### Utiliser SMTP au lieu de mail()

Si la fonction `mail()` ne fonctionne pas sur votre hébergement, vous pouvez utiliser PHPMailer avec SMTP :

1. Téléchargez PHPMailer : https://github.com/PHPMailer/PHPMailer
2. Installez-le dans un dossier `vendor/`
3. Modifiez `contact.php` pour utiliser PHPMailer :

\`\`\`php
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.votrehebergeur.com';
$mail->SMTPAuth = true;
$mail->Username = 'votre-email@exemple.com';
$mail->Password = 'votre-mot-de-passe';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
// ... configuration du message
\`\`\`

### Modifier les textes

Tous les textes sont directement dans les fichiers HTML. Ouvrez les fichiers avec un éditeur de texte et modifiez le contenu entre les balises.

### Changer les couleurs

Éditez `assets/css/style.css` et modifiez les variables CSS au début du fichier :

\`\`\`css
:root {
    --primary: #2563eb;        /* Couleur principale */
    --primary-600: #1e40af;    /* Couleur principale foncée */
    --muted: #6b7280;          /* Gris */
    /* ... autres couleurs */
}
\`\`\`

### Ajouter des images

1. Placez vos images dans le dossier `assets/images/`
2. Remplacez les références dans les fichiers HTML :
   - `hero.jpg` - Image de la section hero (1920x800px recommandé)
   - `classroom.jpg` - Image de classe (1200x800px)
   - `team.jpg` - Image d'équipe (1200x800px)

## 📧 Gestion des contacts

Les messages du formulaire sont :
- Envoyés par email à l'adresse configurée
- Enregistrés dans `contacts/logs.txt` avec date, nom, email et IP

**Important** : Supprimez régulièrement le fichier `contacts/logs.txt` pour des raisons de confidentialité et d'espace disque.

## 🔒 Sécurité

Le formulaire de contact inclut :
- Protection CSRF avec token de session
- Validation et sanitisation des données
- Protection contre les injections d'en-têtes email
- Échappement HTML pour toutes les sorties

**Recommandations** :
- Gardez PHP à jour
- Utilisez HTTPS (certificat SSL)
- Sauvegardez régulièrement votre site

## 🌐 Compatibilité

- Navigateurs modernes (Chrome, Firefox, Safari, Edge)
- Responsive : mobile, tablette, desktop
- PHP 7.4+ requis

## 📝 Personnalisation

### Ajouter une page

1. Créez un nouveau fichier HTML (ex: `services.html`)
2. Copiez la structure d'une page existante
3. Ajoutez le lien dans la navigation de tous les fichiers :

\`\`\`html
<li><a href="services.html" class="nav-link">Services</a></li>
\`\`\`

### Modifier le footer

Le footer est identique sur toutes les pages. Modifiez-le dans chaque fichier ou créez un système d'inclusion PHP.

## 🆘 Dépannage

### Le formulaire ne fonctionne pas

1. Vérifiez que PHP est activé sur votre hébergement
2. Vérifiez que la fonction `mail()` est disponible
3. Consultez les logs d'erreur PHP de votre hébergeur
4. Testez avec un email différent
5. Contactez votre hébergeur pour activer l'envoi d'emails

### Les styles ne s'appliquent pas

1. Vérifiez que le fichier `assets/css/style.css` est bien uploadé
2. Videz le cache de votre navigateur (Ctrl+F5)
3. Vérifiez les chemins dans les balises `<link>`

### Le menu mobile ne fonctionne pas

1. Vérifiez que `assets/js/main.js` est bien uploadé
2. Ouvrez la console du navigateur (F12) pour voir les erreurs JavaScript

## 📞 Support

Pour toute question technique, consultez la documentation de votre hébergeur ou contactez leur support technique.

## 📄 Licence

Ce site est la propriété du CHAD ENGLISH CENTER. Tous droits réservés.

---

**Version** : 1.0  
**Date** : 2025  
**Développé pour** : CHAD ENGLISH CENTER
