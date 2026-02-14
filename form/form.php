<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

require_once __DIR__ . '/../config/database.php';

$csrf_token = generate_csrf();

// Fetch all filieres
$sql_filier = "SELECT ID_filiere, Nom_filiere FROM filiere";
$result_filier = $conn->query($sql_filier);
$filiereArray = array();
if ($result_filier && $result_filier->num_rows > 0) {
    while ($row_filier = $result_filier->fetch_assoc()) {
        $filiereArray[$row_filier["ID_filiere"]] = $row_filier["Nom_filiere"];
    }
}

// Initialize variables
$a_scolaire = array();
$students = array();
$selected_filiere = "";
$selected_annee = "";
$selected_stage = "";
$selected_numero_d_apogee = "";
$stage_options = array();
$membres_jury = array();
$numMembers = 0;
$title = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF on submission
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        die("Session expirée. Veuillez réessayer.");
    }

    $selected_filiere = sanitize_int($_POST['filiere'] ?? 0);
    $selected_annee = sanitize_int($_POST['a_scolaire'] ?? 0);
    $selected_stage = sanitize_int($_POST['stage_type'] ?? 0);
    $selected_numero_d_apogee = sanitize_int($_POST['etudiant'] ?? 0);
    $numMembers = sanitize_int($_POST['numMembers'] ?? 0);

    // Fetch academic years based on selected filiere
    if ($selected_filiere > 0) {
        $stmt = $conn->prepare("SELECT ID_annee, Description_annee FROM annee_scolaire WHERE ID_filiere = ?");
        $stmt->bind_param("i", $selected_filiere);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $a_scolaire[$row["ID_annee"]] = $row["Description_annee"];
        }
        $stmt->close();
    }

    // Fetch stage types based on selected year
    if ($selected_annee > 0) {
        $stmt = $conn->prepare("SELECT s.ID_stage, s.Type_stage FROM stage s JOIN stage_par_annee_scolaire spa ON s.ID_stage = spa.ID_stage WHERE spa.ID_annee = ?");
        $stmt->bind_param("i", $selected_annee);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $stage_options[$row["ID_stage"]] = $row["Type_stage"];
        }
        $stmt->close();
    }

    // Fetch stage abbreviation for title
    if ($selected_stage > 0) {
        $stmt = $conn->prepare("SELECT Abbreviation_stage FROM stage WHERE ID_stage = ?");
        $stmt->bind_param("i", $selected_stage);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $title = $row["Abbreviation_stage"];
        }
        $stmt->close();
    }

    // Fetch students based on filiere and year
    if ($selected_filiere > 0 && $selected_annee > 0) {
        $stmt = $conn->prepare("SELECT Numero_D_apogee, CONCAT(Nom_etudiant, ' ', Prenom_etudiant) AS FullName FROM etudiant WHERE ID_filiere = ? AND ID_annee = ?");
        $stmt->bind_param("ii", $selected_filiere, $selected_annee);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[$row["Numero_D_apogee"]] = $row["FullName"];
        }
        $stmt->close();
    }

    // Fetch jury members based on filiere
    if ($selected_filiere > 0) {
        $stmt = $conn->prepare("SELECT ID_prof, CONCAT(Nom_prof, ' ', Prenom_prof) AS FullName FROM membre_jury WHERE ID_filiere = ?");
        $stmt->bind_param("i", $selected_filiere);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (!empty(trim($row["FullName"]))) {
                $membres_jury[$row["ID_prof"]] = $row["FullName"];
            }
        }
        $stmt->close();
    }

    // Handle form submission (insertData button clicked)
    if (isset($_POST['insertData']) && $_POST['insertData'] === '1') {
        if ($selected_filiere > 0 && $selected_annee > 0 && $selected_stage > 0 && $selected_numero_d_apogee > 0) {
            $titre = trim($_POST['titre'] ?? '');
            $encadrant = trim($_POST['encadrant'] ?? '');
            $note_rapport = sanitize_float($_POST['rapport'] ?? 0);
            $note_presentation = sanitize_float($_POST['presentation'] ?? 0);
            $note_encadrant = sanitize_float($_POST['encadrant_note'] ?? 0);

            // Fetch grade percentages
            $stmt = $conn->prepare("SELECT Pourcentage_rapport, Pourcentage_presentation_orale, Pourcentage_encadrant FROM pourcentage WHERE ID_filiere = ?");
            $stmt->bind_param("i", $selected_filiere);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $pourcentage_rapport = $row["Pourcentage_rapport"];
                $pourcentage_presentation = $row["Pourcentage_presentation_orale"];
                $pourcentage_encadrant = $row["Pourcentage_encadrant"];

                // Calculate final grade
                $note_finale = ($note_rapport * $pourcentage_rapport) +
                               ($note_presentation * $pourcentage_presentation) +
                               ($note_encadrant * $pourcentage_encadrant);

                // Check if rapport already exists
                $stmt2 = $conn->prepare("SELECT Numero_D_apogee FROM rapport WHERE Numero_D_apogee = ? AND ID_stage = ?");
                $stmt2->bind_param("ii", $selected_numero_d_apogee, $selected_stage);
                $stmt2->execute();
                $check_result = $stmt2->get_result();
                if ($check_result->num_rows == 0) {
                    // Insert rapport
                    $stmt3 = $conn->prepare("INSERT INTO rapport (Numero_D_apogee, ID_stage, Titre_Rapport, Nom_Prenom_encadrent, Note_rapport, Note_presentation_orale, Note_encadrant, Note_finale) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt3->bind_param("iissdddd", $selected_numero_d_apogee, $selected_stage, $titre, $encadrant, $note_rapport, $note_presentation, $note_encadrant, $note_finale);
                    if (!$stmt3->execute()) {
                        error_log("Rapport insert error: " . $stmt3->error);
                    }
                    $stmt3->close();
                }
                $stmt2->close();
            }
            $stmt->close();

            // Insert soutenance
            $date_soutenance = $_POST['date'] ?? '';
            $heure_soutenance = $_POST['heure'] ?? '';
            $lieu_soutenance = trim($_POST['salle'] ?? '');

            // Check if soutenance already exists
            $stmt = $conn->prepare("SELECT Numero_D_apogee FROM soutenance WHERE Numero_D_apogee = ? AND ID_stage = ?");
            $stmt->bind_param("ii", $selected_numero_d_apogee, $selected_stage);
            $stmt->execute();
            $check_result = $stmt->get_result();
            if ($check_result->num_rows == 0) {
                $stmt2 = $conn->prepare("INSERT INTO soutenance (Date_soutenance, Heure_soutenance, Lieu_soutenance, ID_stage, Numero_D_apogee) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("sssii", $date_soutenance, $heure_soutenance, $lieu_soutenance, $selected_stage, $selected_numero_d_apogee);
                if ($stmt2->execute()) {
                    $id_soutenance = $conn->insert_id;

                    // Insert jury members
                    for ($i = 1; $i <= $numMembers; $i++) {
                        $membre_key = "membre_$i";
                        $role_key = "role_$i";
                        if (isset($_POST[$membre_key]) && isset($_POST[$role_key])) {
                            $id_jury = sanitize_int($_POST[$membre_key]);
                            $role = trim($_POST[$role_key]);
                            $stmt3 = $conn->prepare("INSERT INTO jury_soutenance (ID_jury, ID_soutenance, role_du_jury) VALUES (?, ?, ?)");
                            $stmt3->bind_param("iis", $id_jury, $id_soutenance, $role);
                            if (!$stmt3->execute()) {
                                error_log("Jury insert error: " . $stmt3->error);
                            }
                            $stmt3->close();
                        }
                    }
                } else {
                    error_log("Soutenance insert error: " . $stmt2->error);
                }
                $stmt2->close();
            }
            $stmt->close();

            // Store selections in session and redirect (FIXED: only on submit)
            $_SESSION['selected_filiere'] = $selected_filiere;
            $_SESSION['selected_annee'] = $selected_annee;
            $_SESSION['selected_stage'] = $selected_stage;
            $_SESSION['selected_etudiant'] = $selected_numero_d_apogee;

            $conn->close();
            redirect("../generate_pdf/generate_pdf.php");
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'evaluation — EST Fkih Ben Salah</title>
    <link rel="stylesheet" href="form.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="topnav">
        <div class="topnav-inner">
            <a href="#" class="topnav-brand">
                <svg class="topnav-logo" width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <rect width="28" height="28" rx="8" fill="currentColor" fill-opacity="0.15"/>
                    <path d="M7 9h14M7 14h10M7 19h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>EST Fkih Ben Salah</span>
            </a>
            <div class="topnav-links">
                <a href="#section-info" class="topnav-link active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>Information</span>
                </a>
                <a href="#section-jury" class="topnav-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>Jury</span>
                </a>
            </div>
            <div class="topnav-actions">
                <span class="topnav-user">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?php echo esc($_SESSION['user_name'] ?? ''); ?>
                </span>
                <a href="../login/logout.php" class="topnav-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    <span>Deconnexion</span>
                </a>
            </div>
            <button class="topnav-burger" id="burgerBtn" aria-label="Menu">
                <svg class="burger-open" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg class="burger-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <!-- Mobile dropdown -->
        <div class="topnav-mobile" id="mobileMenu">
            <a href="#section-info" class="topnav-mobile-link">Information & Evaluation</a>
            <a href="#section-jury" class="topnav-mobile-link">Membres du jury</a>
            <div class="topnav-mobile-divider"></div>
            <span class="topnav-mobile-user">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <?php echo esc($_SESSION['user_name'] ?? ''); ?>
            </span>
            <a href="../login/logout.php" class="topnav-mobile-logout">Deconnexion</a>
        </div>
    </nav>

    <main class="page-content">
        <form id="dataForm" action="<?php echo esc($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo esc($csrf_token); ?>">
            <input type="hidden" name="insertData" id="insertData" value="0">

            <!-- SECTION 1: Student Info & Evaluation -->
            <section class="card" id="section-info">
                <div class="card-header">
                    <div class="card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                    </div>
                    <div>
                        <h2>Information de l'etudiant</h2>
                        <p>Selectionnez la filiere, l'annee et l'etudiant</p>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Student Selection Row -->
                    <div class="form-section-label">Selection de l'etudiant</div>
                    <div class="form-grid cols-3">
                        <div class="form-group">
                            <label for="filiere">Filiere</label>
                            <div class="select-wrap">
                                <select id="filiere" name="filiere" required onchange="this.form.submit()">
                                    <option value="">Selectionner une filiere</option>
                                    <?php foreach($filiereArray as $id => $filier): ?>
                                        <option value="<?php echo esc($id); ?>" <?php echo ($id == $selected_filiere) ? "selected" : ""; ?>><?php echo esc($filier); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="a_scolaire">Annee scolaire</label>
                            <div class="select-wrap">
                                <select id="a_scolaire" name="a_scolaire" required onchange="this.form.submit()">
                                    <option value="">Selectionner une annee</option>
                                    <?php if (!empty($selected_filiere)):
                                        foreach($a_scolaire as $id => $annee): ?>
                                            <option value="<?php echo esc($id); ?>" <?php echo ($id == $selected_annee) ? "selected" : ""; ?>><?php echo esc($annee); ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                                <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stage_type">Type de stage</label>
                            <div class="select-wrap">
                                <select id="stage_type" name="stage_type" required onchange="this.form.submit()">
                                    <option value="">Selectionner un type</option>
                                    <?php if (!empty($selected_annee)):
                                        foreach ($stage_options as $id => $stage): ?>
                                            <option value="<?php echo esc($id); ?>" <?php echo ($id == $selected_stage) ? "selected" : ""; ?>><?php echo esc($stage); ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                                <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid cols-4">
                        <div class="form-group span-1">
                            <label for="etudiant">Etudiant</label>
                            <div class="select-wrap">
                                <select id="etudiant" name="etudiant" required onchange="this.form.submit()">
                                    <option value="">Selectionner un etudiant</option>
                                    <?php if (!empty($students)):
                                        foreach ($students as $id => $etudiant): ?>
                                            <option value="<?php echo esc($id); ?>" <?php echo ($id == $selected_numero_d_apogee) ? "selected" : ""; ?>><?php echo esc($etudiant); ?></option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                                <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="numMembers">Membres du jury</label>
                            <input type="number" id="numMembers" name="numMembers" value="<?php echo esc($numMembers); ?>" min="1" max="6" onchange="this.form.submit()" required placeholder="0">
                        </div>

                        <div class="form-group">
                            <label for="date">Date de soutenance</label>
                            <input type="date" id="date" name="date" value="<?php echo esc($_POST['date'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="heure">Heure</label>
                            <input type="time" id="heure" name="heure" value="<?php echo esc($_POST['heure'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <!-- Details Row -->
                    <div class="form-section-label">Details du stage</div>
                    <div class="form-grid cols-3">
                        <div class="form-group">
                            <label for="titre">Titre du <?php echo esc($title); ?></label>
                            <input type="text" id="titre" name="titre" value="<?php echo esc($_POST['titre'] ?? ''); ?>" required placeholder="Titre du rapport">
                        </div>

                        <div class="form-group">
                            <label for="encadrant">Encadrant</label>
                            <input type="text" id="encadrant" name="encadrant" value="<?php echo esc($_POST['encadrant'] ?? ''); ?>" required placeholder="Nom et prenom">
                        </div>

                        <div class="form-group">
                            <label for="salle">Lieu de soutenance</label>
                            <input type="text" id="salle" name="salle" value="<?php echo esc($_POST['salle'] ?? ''); ?>" required placeholder="Ex: Salle A1">
                        </div>
                    </div>

                    <!-- Grades Row -->
                    <div class="form-section-label">Mode d'evaluation</div>
                    <div class="form-grid cols-3">
                        <div class="form-group">
                            <label for="rapport">Rapport <span class="note-max">/20</span></label>
                            <input type="number" id="rapport" name="rapport" min="0" max="20" step="0.1" value="<?php echo esc($_POST['rapport'] ?? ''); ?>" required placeholder="0.0">
                        </div>
                        <div class="form-group">
                            <label for="presentation">Presentation orale <span class="note-max">/20</span></label>
                            <input type="number" id="presentation" name="presentation" min="0" max="20" step="0.1" value="<?php echo esc($_POST['presentation'] ?? ''); ?>" required placeholder="0.0">
                        </div>
                        <div class="form-group">
                            <label for="encadrant_note">Note encadrant <span class="note-max">/20</span></label>
                            <input type="number" id="encadrant_note" name="encadrant_note" min="0" max="20" step="0.1" value="<?php echo esc($_POST['encadrant_note'] ?? ''); ?>" required placeholder="0.0">
                        </div>
                    </div>

                    <div class="card-actions">
                        <a href="#section-jury" class="btn btn-primary">
                            <span>Suivant</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: Jury Members -->
            <section class="card" id="section-jury">
                <div class="card-header">
                    <div class="card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <h2>Membres du jury</h2>
                        <p>Selectionnez les membres et attribuez leurs roles</p>
                    </div>
                </div>

                <div class="card-body">
                    <?php if ($numMembers > 0): ?>
                        <div class="jury-table-header">
                            <span class="jury-col-name">Nom et prenom</span>
                            <span class="jury-col-role">Role du membre</span>
                        </div>
                        <?php for ($i = 1; $i <= $numMembers; $i++): ?>
                            <div class="jury-row">
                                <div class="jury-number"><?php echo $i; ?></div>
                                <div class="form-group jury-member-select">
                                    <div class="select-wrap">
                                        <select id="membre_<?php echo $i; ?>" name="membre_<?php echo $i; ?>" required onchange="filterMembers()">
                                            <option value="">Selectionner un membre</option>
                                            <?php foreach ($membres_jury as $id_prof => $membre): ?>
                                                <option value="<?php echo esc($id_prof); ?>"
                                                    <?php echo (isset($_POST["membre_$i"]) && $_POST["membre_$i"] == $id_prof) ? "selected" : ""; ?>>
                                                    <?php echo esc($membre); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                    </div>
                                </div>
                                <div class="form-group jury-role-input">
                                    <input type="text" id="role_<?php echo $i; ?>" name="role_<?php echo $i; ?>"
                                        value="<?php echo esc($_POST["role_$i"] ?? ''); ?>"
                                        placeholder="Ex: President, Rapporteur..." required>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="jury-empty">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <p>Veuillez indiquer le nombre de membres du jury dans la section precedente</p>
                        </div>
                    <?php endif; ?>

                    <div class="card-actions two-actions">
                        <a href="#section-info" class="btn btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                            <span>Retour</span>
                        </a>
                        <button type="button" class="btn btn-primary" onclick="submitForm()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Soumettre l'evaluation</span>
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </main>

    <!-- Scroll to top -->
    <button class="scroll-top" id="scrollTopBtn" aria-label="Retour en haut">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
    </button>

    <script>
        function submitForm() {
            document.getElementById('insertData').value = '1';
            document.forms[0].submit();
        }
    </script>
    <script src="form.js"></script>
</body>
</html>
