<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . '/../includes/auth.php';
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $error_message = "Session expirée. Veuillez réessayer.";
    } else {
        $email = $_POST['email'] ?? '';
        $password_cin = sanitize_int($_POST['password'] ?? 0);

        require_once __DIR__ . '/../config/database.php';

        $stmt = $conn->prepare("SELECT ID_prof, Nom_prof, Prenom_prof FROM membre_jury WHERE Email_prof = ? AND CIN_prof = ?");
        $stmt->bind_param("si", $email, $password_cin);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $row['ID_prof'];
            $_SESSION['user_name'] = $row['Nom_prof'] . ' ' . $row['Prenom_prof'];
            $stmt->close();
            $conn->close();
            redirect("../form/form.php");
        } else {
            $error_message = "Email ou mot de passe incorrect";
        }

        $stmt->close();
        $conn->close();
    }
}

require_once __DIR__ . '/../includes/auth.php';
$csrf_token = generate_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — EST Fkih Ben Salah</title>
    <link rel="stylesheet" href="login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-page">
        <!-- Left panel: branding -->
        <div class="brand-panel">
            <div class="brand-content">
                <div class="brand-badge">EST FBS</div>
                <h1>Ecole Supérieure<br>de Technologie</h1>
                <p class="brand-location">Fkih Ben Salah</p>
                <div class="brand-divider"></div>
                <p class="brand-desc">Plateforme d'évaluation des stages et projets de fin d'études</p>
                <div class="brand-footer">
                    <p>Université Sultan Moulay Slimane</p>
                </div>
            </div>
            <div class="brand-pattern"></div>
        </div>

        <!-- Right panel: login form -->
        <div class="form-panel">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Connexion</h2>
                    <p>Accédez à votre espace d'évaluation</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M8 4.5V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="8" cy="11.5" r="0.75" fill="currentColor"/></svg>
                        <span><?php echo esc($error_message); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo esc($csrf_token); ?>">
                    
                    <div class="field">
                        <label for="email">Adresse email</label>
                        <div class="input-wrap">
                            <svg class="field-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <input type="email" id="email" name="email" placeholder="nom@exemple.ma" required autocomplete="email">
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Mot de passe (CIN)</label>
                        <div class="input-wrap">
                            <svg class="field-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" id="password" name="password" placeholder="Votre numéro CIN" required autocomplete="current-password">
                            <button type="button" class="toggle-pass" id="togglePassword" aria-label="Afficher le mot de passe">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Se connecter</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </form>

                <div class="form-footer">
                    <p>Royaume du Maroc — Ministère de l'Enseignement Supérieur</p>
                </div>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>
