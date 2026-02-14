<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

// Validate session data exists
if (!isset($_SESSION['selected_filiere']) || !isset($_SESSION['selected_annee']) || 
    !isset($_SESSION['selected_stage']) || !isset($_SESSION['selected_etudiant'])) {
    redirect("../form/form.php");
}

$selected_filiere = sanitize_int($_SESSION['selected_filiere']);
$selected_annee = sanitize_int($_SESSION['selected_annee']);
$selected_stage = sanitize_int($_SESSION['selected_stage']);
$selected_etudiant = sanitize_int($_SESSION['selected_etudiant']);

require_once __DIR__ . '/../config/database.php';

// Initialize all variables with defaults
$Nom_filiere = '';
$Abbreviation_filiere = '';
$Nom__stage = '';
$Abbreviation__stage = '';
$Nom_year = '';
$Nom_etudiant = '';
$Titre_Rapport = '';
$Nom_Prenom_encadrent = '';
$Note_rapport = 0;
$Note_presentation_orale = 0;
$Note_encadrant = 0;
$Note_finale = 0;
$Percent_rapport = 0;
$Percent_presentation_orale = 0;
$Percent_encadrant = 0;
$Numero_soutenance = 0;
$Date_soutenance = '';
$Heure_soutenance = '';
$Lieu_soutenance = '';
$jury_num = 0;
$jury = array();
$jury_details = array();

// Fetch filiere info
$stmt = $conn->prepare("SELECT Nom_filiere, Abbreviation_filiere FROM filiere WHERE ID_filiere = ?");
$stmt->bind_param("i", $selected_filiere);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Nom_filiere = $row["Nom_filiere"];
    $Abbreviation_filiere = $row["Abbreviation_filiere"];
}
$stmt->close();

// Fetch stage info
$stmt = $conn->prepare("SELECT Type_stage, Abbreviation_stage FROM stage WHERE ID_stage = ?");
$stmt->bind_param("i", $selected_stage);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Nom__stage = $row["Type_stage"];
    $Abbreviation__stage = $row["Abbreviation_stage"];
}
$stmt->close();

// Fetch year description
$stmt = $conn->prepare("SELECT Description_annee FROM annee_scolaire WHERE ID_annee = ?");
$stmt->bind_param("i", $selected_annee);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Nom_year = $row["Description_annee"];
}
$stmt->close();

// Fetch student name
$stmt = $conn->prepare("SELECT CONCAT(Nom_etudiant, ' ', Prenom_etudiant) AS FullName FROM etudiant WHERE Numero_D_apogee = ?");
$stmt->bind_param("i", $selected_etudiant);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Nom_etudiant = $row["FullName"];
}
$stmt->close();

// Fetch rapport details
$stmt = $conn->prepare("SELECT Titre_Rapport, Nom_Prenom_encadrent, Note_rapport, Note_presentation_orale, Note_encadrant, Note_finale FROM rapport WHERE Numero_D_apogee = ? AND ID_stage = ?");
$stmt->bind_param("ii", $selected_etudiant, $selected_stage);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Titre_Rapport = $row["Titre_Rapport"];
    $Nom_Prenom_encadrent = $row["Nom_Prenom_encadrent"];
    $Note_rapport = $row["Note_rapport"];
    $Note_presentation_orale = $row["Note_presentation_orale"];
    $Note_encadrant = $row["Note_encadrant"];
    $Note_finale = $row["Note_finale"];
}
$stmt->close();

// Fetch grade percentages
$stmt = $conn->prepare("SELECT Pourcentage_rapport, Pourcentage_presentation_orale, Pourcentage_encadrant FROM pourcentage WHERE ID_filiere = ?");
$stmt->bind_param("i", $selected_filiere);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Percent_rapport = $row["Pourcentage_rapport"];
    $Percent_presentation_orale = $row["Pourcentage_presentation_orale"];
    $Percent_encadrant = $row["Pourcentage_encadrant"];
}
$stmt->close();

// Fetch soutenance details
$stmt = $conn->prepare("SELECT Numero_soutenance, Date_soutenance, Heure_soutenance, Lieu_soutenance FROM soutenance WHERE ID_stage = ? AND Numero_D_apogee = ?");
$stmt->bind_param("ii", $selected_stage, $selected_etudiant);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $Numero_soutenance = $row["Numero_soutenance"];
    $Date_soutenance = $row["Date_soutenance"];
    $Heure_soutenance = $row["Heure_soutenance"];
    $Lieu_soutenance = $row["Lieu_soutenance"];
}
$stmt->close();

// Fetch jury count and details (only if soutenance exists)
if ($Numero_soutenance > 0) {
    $stmt = $conn->prepare("SELECT COUNT(ID_jury) AS jury_num FROM jury_soutenance WHERE ID_soutenance = ?");
    $stmt->bind_param("i", $Numero_soutenance);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jury_num = $row["jury_num"];
    }
    $stmt->close();

    // Fetch jury members with roles
    $stmt = $conn->prepare("SELECT js.ID_jury, js.role_du_jury, mj.Nom_prof, mj.Prenom_prof FROM jury_soutenance js JOIN membre_jury mj ON js.ID_jury = mj.ID_prof WHERE js.ID_soutenance = ?");
    $stmt->bind_param("i", $Numero_soutenance);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $jury_details[] = array(
            'NomPrenom' => $row['Nom_prof'] . ' ' . $row['Prenom_prof'],
            'Role' => $row['role_du_jury']
        );
    }
    $stmt->close();
}

$conn->close();

// DUT or LP determination
$is_dut = (strpos($Nom_year, "DUT") !== false) ? "DUT " : "LP ";

// Build display strings (FIXED: semicolon + operator precedence)
$fiche = $Nom__stage . " (" . $Abbreviation__stage . ")";
$currentYear = date("Y");
$academicYear = $is_dut . $Abbreviation_filiere . " (Promotion " . $currentYear . "/" . ($currentYear + 1) . ")";
$filiere = $is_dut . $Nom_filiere . " (" . $Abbreviation_filiere . ")";
$fil = $is_dut . $Abbreviation_filiere;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche d'evaluation — <?php echo esc($fil); ?></title>
    <link rel="stylesheet" href="generate_pdf.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Action buttons (outside A4 sheet for screen) -->
    <div class="action-bar no-print">
        <a href="../form/form.php" class="action-btn action-btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            <span>Retour au formulaire</span>
        </a>
        <button id="printButton" class="action-btn action-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            <span>Imprimer</span>
        </button>
    </div>

    <!-- A4 Document -->
    <div class="a4-sheet" id="pdfContent">
        <!-- Header -->
        <header class="doc-header">
            <img src="EST Fkih Ben Saleh (1).png" class="doc-logo" alt="EST FBS Logo">
            <div class="doc-header-text">
                <p>Royaume du Maroc</p>
                <p>Ministere de l'Education Nationale, de la Formation Professionnelle,</p>
                <p>de l'Enseignement Superieur et de la Recherche Scientifique</p>
                <p>Universite Sultan Moulay Slimane</p>
                <p class="doc-header-est">L'Ecole Superieure de Technologie — Fkih Ben Salah</p>
            </div>
            <img src="UMS.png" class="doc-logo" alt="UMS Logo">
        </header>

        <!-- Main Content -->
        <main class="doc-main">
            <!-- Title -->
            <div class="doc-title">
                <h1>Fiche d'evaluation du <?php echo esc($fiche); ?></h1>
                <h2><?php echo esc($academicYear); ?></h2>
            </div>

            <!-- Student Info -->
            <div class="doc-info">
                <div class="doc-info-row">
                    <span class="doc-info-label">Filiere :</span>
                    <span class="doc-info-value"><?php echo esc($filiere); ?></span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Nom & Prenom de l'etudiant :</span>
                    <span class="doc-info-value"><?php echo esc($Nom_etudiant); ?></span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Titre du <?php echo esc($Abbreviation__stage); ?> :</span>
                    <span class="doc-info-value"><?php echo esc($Titre_Rapport); ?></span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Encadrant :</span>
                    <span class="doc-info-value"><?php echo esc($Nom_Prenom_encadrent); ?></span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Date de soutenance :</span>
                    <span class="doc-info-value"><?php echo esc($Date_soutenance ? date('d/m/Y', strtotime($Date_soutenance)) : ''); ?></span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Heure :</span>
                    <span class="doc-info-value"><?php echo esc($Heure_soutenance ? date('H:i', strtotime($Heure_soutenance)) : ''); ?></span>
                </div>
                <div class="doc-info-row">
                    <span class="doc-info-label">Lieu :</span>
                    <span class="doc-info-value"><?php echo esc($Lieu_soutenance); ?></span>
                </div>
            </div>

            <!-- Evaluation Table -->
            <div class="doc-section">
                <h3 class="doc-section-title">Mode d'evaluation</h3>
                <table class="doc-table eval-table">
                    <thead>
                        <tr>
                            <th class="col-criteria">Critere</th>
                            <th class="col-percent">Pourcentage</th>
                            <th class="col-note">Note / 20</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rapport</td>
                            <td class="text-center"><?php echo esc($Percent_rapport * 100); ?>%</td>
                            <td class="text-center"><?php echo esc($Note_rapport); ?></td>
                        </tr>
                        <tr>
                            <td>Presentation orale et discussions</td>
                            <td class="text-center"><?php echo esc($Percent_presentation_orale * 100); ?>%</td>
                            <td class="text-center"><?php echo esc($Note_presentation_orale); ?></td>
                        </tr>
                        <tr>
                            <td>Note des encadrants</td>
                            <td class="text-center"><?php echo esc($Percent_encadrant * 100); ?>%</td>
                            <td class="text-center"><?php echo esc($Note_encadrant); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td><strong>Note finale</strong></td>
                            <td class="text-center"><strong>100%</strong></td>
                            <td class="text-center"><strong><?php echo esc($Note_finale); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Jury Table -->
            <div class="doc-section">
                <h3 class="doc-section-title">Membres du jury</h3>
                <table class="doc-table jury-table">
                    <thead>
                        <tr>
                            <th>Membres</th>
                            <th>Nom et Prenom</th>
                            <th>Emargement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < $jury_num; $i++): ?>
                        <tr>
                            <?php if (isset($jury_details[$i])): ?>
                                <td><?php echo esc($jury_details[$i]['Role']); ?></td>
                                <td><?php echo esc($jury_details[$i]['NomPrenom']); ?></td>
                                <td class="sig-cell">
                                    <div class="sig-wrap">
                                        <canvas id="signatureCanvas<?php echo $i; ?>" class="sig-canvas" width="200" height="30"></canvas>
                                        <button type="button" class="sig-clear no-print" onclick="clearSignature(<?php echo $i; ?>)">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                </td>
                            <?php else: ?>
                                <td>—</td>
                                <td>—</td>
                                <td></td>
                            <?php endif; ?>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <!-- Coordinator Signature -->
            <div class="doc-coordinator">
                <h3 class="doc-section-title">Le coordonnateur de la filiere <?php echo esc($fil); ?></h3>
                <div class="coord-sig-wrap">
                    <canvas id="coordinatorSignatureCanvas" class="coord-canvas" width="300" height="80"></canvas>
                    <button type="button" class="sig-clear no-print" onclick="clearCoordinatorSignature()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="doc-footer">
            <p>Ecole Superieure de Technologie — Fkih Ben Salah</p>
            <p>Hay Tighnari, Route Nationale N11, 23200 Fkih Ben Salah, B.P: 336</p>
            <p>Tel.: 05.23.43.46.66 / 05.23.43.49.99 | Email: estfbs@usms.ma | <a href="http://estfbs.usms.ac.ma/">estfbs.usms.ac.ma</a></p>
        </footer>
    </div>

    <script>
        let jury_num = <?php echo sanitize_int($jury_num); ?>;
    </script>
    <script src="generate_signature.js"></script>
    <script src="generate_pdf.js"></script>
</body>
</html>
