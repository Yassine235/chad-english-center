<?php
/**
 * CONTACT.PHP - Formulaire de contact du CHAD ENGLISH CENTER
 * 
 * MODIFICATION IMPORTANTE :
 * Ligne 19 : Remplacez 'contact@chadenglishcenter.td' par votre email réel
 * 
 * Fonctionnalités :
 * - Validation et sanitisation des données
 * - Protection contre les injections d'en-têtes
 * - Envoi d'email via mail()
 * - Logging des soumissions
 * - Protection CSRF avec token de session
 */

// Configuration - MODIFIEZ CET EMAIL
$destinataire = 'contact@chadenglishcenter.td'; // ← CHANGEZ ICI

// Démarrer la session pour le token CSRF
session_start();

// Générer un token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Variables pour les messages
$success = false;
$error = '';
$formData = [];

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        
        // Fonction de sanitisation
        function sanitize_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            return $data;
        }
        
        // Fonction de validation d'email
        function validate_email($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }
        
        // Fonction pour détecter les injections d'en-têtes
        function has_header_injection($str) {
            return preg_match("/[\r\n]/", $str);
        }
        
        // Récupérer et sanitiser les données
        $nom = sanitize_input($_POST['nom'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $telephone = sanitize_input($_POST['telephone'] ?? '');
        $sujet = sanitize_input($_POST['sujet'] ?? '');
        $niveau = sanitize_input($_POST['niveau'] ?? '');
        $message = sanitize_input($_POST['message'] ?? '');
        $consent = isset($_POST['consent']) ? true : false;
        
        // Sauvegarder les données pour réaffichage en cas d'erreur
        $formData = [
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'sujet' => $sujet,
            'niveau' => $niveau,
            'message' => $message,
            'consent' => $consent
        ];
        
        // Validation
        if (empty($nom)) {
            $error = 'Le nom est requis.';
        } elseif (empty($email)) {
            $error = 'L\'email est requis.';
        } elseif (!validate_email($email)) {
            $error = 'L\'email n\'est pas valide.';
        } elseif (has_header_injection($email) || has_header_injection($nom)) {
            $error = 'Caractères invalides détectés.';
        } elseif (empty($telephone)) {
            $error = 'Le téléphone est requis.';
        } elseif (empty($sujet)) {
            $error = 'Le sujet est requis.';
        } elseif (empty($message)) {
            $error = 'Le message est requis.';
        } elseif (!$consent) {
            $error = 'Vous devez accepter la politique de confidentialité.';
        } else {
            
            // Préparer l'email
            $email_subject = "Contact CHAD ENGLISH CENTER : " . $sujet;
            
            // Corps du message
            $email_body = "Nouveau message de contact du site CHAD ENGLISH CENTER\n\n";
            $email_body .= "Nom : " . $nom . "\n";
            $email_body .= "Email : " . $email . "\n";
            $email_body .= "Téléphone : " . $telephone . "\n";
            $email_body .= "Sujet : " . $sujet . "\n";
            $email_body .= "Niveau d'intérêt : " . ($niveau ?: 'Non spécifié') . "\n\n";
            $email_body .= "Message :\n" . $message . "\n\n";
            $email_body .= "---\n";
            $email_body .= "Date : " . date('d/m/Y H:i:s') . "\n";
            $email_body .= "IP : " . $_SERVER['REMOTE_ADDR'] . "\n";
            
            // En-têtes de l'email
            $headers = "From: " . $nom . " <" . $email . ">\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Tentative d'envoi de l'email
            if (mail($destinataire, $email_subject, $email_body, $headers)) {
                
                // Logger la soumission
                $log_dir = __DIR__ . '/contacts';
                if (!is_dir($log_dir)) {
                    mkdir($log_dir, 0755, true);
                }
                
                $log_file = $log_dir . '/logs.txt';
                $log_entry = sprintf(
                    "[%s] %s <%s> - %s - IP: %s\n",
                    date('Y-m-d H:i:s'),
                    $nom,
                    $email,
                    $sujet,
                    $_SERVER['REMOTE_ADDR']
                );
                file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
                
                $success = true;
                
                // Réinitialiser les données du formulaire
                $formData = [];
                
                // Redirection (optionnelle)
                // header('Location: contact.php?success=1');
                // exit;
                
            } else {
                $error = 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer ou nous contacter directement.';
            }
        }
    }
}

// Vérifier le paramètre de succès dans l'URL
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - CHAD ENGLISH CENTER</title>
    <meta name="description" content="Contactez le CHAD ENGLISH CENTER pour toute information ou inscription. Nous sommes à votre écoute.">
    
    <!-- Open Graph -->
    <meta property="og:title" content="Contact - CHAD ENGLISH CENTER">
    <meta property="og:description" content="Contactez-nous pour plus d'informations sur nos formations en anglais.">
    <meta property="og:image" content="assets/images/hero.jpg">
    <meta property="og:type" content="website">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    
    <!-- Added favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/logo.jpg">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header / Navigation -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="nav-brand">
                    <!-- Added logo image -->
                    <img src="assets/images/logo.png" alt="CHAD ENGLISH CENTER Logo" class="logo-img">
                    <div>
                        <h1 class="logo"><span style="color: blue;">CHAD</span> <span style="color: yellow;">ENGLISH</span> <span style="color: red;">CENTER</span></h1>
                        <p class="tagline">Enter to learn, leave to serve</p>
                    </div>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.html" class="nav-link">Accueil</a></li>
                    <li><a href="formations.html" class="nav-link">Formations</a></li>
                    <li><a href="apropos.html" class="nav-link">À propos</a></li>
                    <li><a href="contact.php" class="nav-link active">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h2 class="page-title">Contactez-nous</h2>
            <p class="page-subtitle">Nous sommes là pour répondre à vos questions</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <div class="container">
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <h3>Merci pour votre message !</h3>
                    <p>Nous avons bien reçu votre demande et nous vous répondrons dans les plus brefs délais.</p>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <h3>Erreur</h3>
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="contact-grid">
                
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h3 class="form-title">Envoyez-nous un message</h3>
                    
                    <form method="POST" action="contact.php" class="contact-form" id="contactForm">
                        
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="form-group">
                            <label for="nom" class="form-label">Nom complet <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="nom" 
                                name="nom" 
                                class="form-input" 
                                required
                                value="<?php echo htmlspecialchars($formData['nom'] ?? ''); ?>"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="required">*</span></label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                required
                                value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone" class="form-label">Téléphone <span class="required">*</span></label>
                            <input 
                                type="tel" 
                                id="telephone" 
                                name="telephone" 
                                class="form-input" 
                                required
                                value="<?php echo htmlspecialchars($formData['telephone'] ?? ''); ?>"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="sujet" class="form-label">Sujet <span class="required">*</span></label>
                            <select id="sujet" name="sujet" class="form-select" required>
                                <option value="">-- Choisissez un sujet --</option>
                                <option value="Demande d'information" <?php echo (($formData['sujet'] ?? '') === 'Demande d\'information') ? 'selected' : ''; ?>>Demande d'information</option>
                                <option value="Inscription" <?php echo (($formData['sujet'] ?? '') === 'Inscription') ? 'selected' : ''; ?>>Inscription</option>
                                <option value="Horaires et tarifs" <?php echo (($formData['sujet'] ?? '') === 'Horaires et tarifs') ? 'selected' : ''; ?>>Horaires et tarifs</option>
                                <option value="Certifications" <?php echo (($formData['sujet'] ?? '') === 'Certifications') ? 'selected' : ''; ?>>Certifications</option>
                                <option value="Autre" <?php echo (($formData['sujet'] ?? '') === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="niveau" class="form-label">Niveau d'intérêt (optionnel)</label>
                            <select id="niveau" name="niveau" class="form-select">
                                <option value="">-- Sélectionnez un niveau --</option>
                                <option value="Niveau 0" <?php echo (($formData['niveau'] ?? '') === 'Niveau 0') ? 'selected' : ''; ?>>Niveau 0 - Débutant absolu</option>
                                <option value="Niveau 1" <?php echo (($formData['niveau'] ?? '') === 'Niveau 1') ? 'selected' : ''; ?>>Niveau 1 - Élémentaire 1</option>
                                <option value="Niveau 2" <?php echo (($formData['niveau'] ?? '') === 'Niveau 2') ? 'selected' : ''; ?>>Niveau 2 - Élémentaire 2</option>
                                <option value="Niveau 3" <?php echo (($formData['niveau'] ?? '') === 'Niveau 3') ? 'selected' : ''; ?>>Niveau 3 - Intermédiaire 1</option>
                                <option value="Niveau 4" <?php echo (($formData['niveau'] ?? '') === 'Niveau 4') ? 'selected' : ''; ?>>Niveau 4 - Intermédiaire 2</option>
                                <option value="Niveau 5" <?php echo (($formData['niveau'] ?? '') === 'Niveau 5') ? 'selected' : ''; ?>>Niveau 5 - Avancé</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Message <span class="required">*</span></label>
                            <textarea 
                                id="message" 
                                name="message" 
                                class="form-textarea" 
                                rows="6" 
                                required
                            ><?php echo htmlspecialchars($formData['message'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-checkbox">
                                <input 
                                    type="checkbox" 
                                    name="consent" 
                                    required
                                    <?php echo ($formData['consent'] ?? false) ? 'checked' : ''; ?>
                                >
                                <span>J'accepte que mes données soient utilisées pour répondre à ma demande. <span class="required">*</span></span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">Envoyer le message</button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div class="contact-info">
                    <h3 class="info-title">Informations de contact</h3>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Adresse</h4>
                            <p>N'Djamena, Tchad</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Téléphone</h4>
                            <p>+235 62 82 20 80</p>
                        </div>
                    </div>
                    
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="info-content">
                            <h4>Horaires d'ouverture</h4>
                            <p>Lundi - Vendredi : 8h - 19h</p>
                            <p>Samedi : 8h - 13h</p>
                        </div>
                    </div>
                    
                    <div class="social-section">
                        <h4>Suivez-nous</h4>
                        <div class="social-links">
                            <a href="#" class="social-link" aria-label="Facebook">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link" aria-label="Twitter">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="#" class="social-link" aria-label="WhatsApp">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3 class="footer-title"><span style="color:blue">CHAD</span> <span style="color:yellow">ENGLISH</span> <span style="color:red">CENTER</span></h3>
                    <p class="footer-tagline">Enter to learn, leave to serve</p>
                    <p class="footer-text">Votre partenaire pour la maîtrise de l'anglais au Tchad.</p>
                </div>

                <div class="footer-column">
                    <h4 class="footer-heading">Navigation</h4>
                    <ul class="footer-links">
                        <li><a href="index.html">Accueil</a></li>
                        <li><a href="formations.html">Formations</a></li>
                        <li><a href="apropos.html">À propos</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-heading">Contact</h4>
                    <ul class="footer-contact">
                        <li>N'Djamena, Tchad</li>
                        <li>Tél : +235 62 82 20 80</li>
                        <li>Email : contact@chadenglishcenter.td</li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-heading">Suivez-nous</h4>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="social-link" aria-label="WhatsApp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 CHAD ENGLISH CENTER. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        // Validation supplémentaire côté client
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailPattern.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide.');
                return false;
            }
        });
    </script>
</body>
</html>
