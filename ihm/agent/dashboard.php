<?php
session_start();

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Location: ../connexion.php');
    exit;
}

// Get possible years for the select dropdown (current year and 5 past years)
$currentYear = date('Y');
$years = range($currentYear, $currentYear - 5);

// Get success/error messages
$successMessage = isset($_GET['success']) ? $_GET['success'] : '';
$errorMessage = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Agent - Gestion des Factures</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
        }
        .file-upload {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            transition: border 0.3s ease;
        }
        .file-upload:hover {
            border-color: var(--secondary-color);
        }
        .file-info {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
        .file-info pre {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .file-form {
            margin-top: 20px;
        }
        .instructions {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-left: 4px solid var(--secondary-color);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Espace Agent</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="../deconnexion.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Interface Agent</h1>
            
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-card">
                
                
                <div class="file-form">
                <form action="../../traitement/agentService.php?action=upload_consumption" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="annee">Année de consommation:</label>
                            <select id="annee" name="annee" class="form-control" required>
                                <?php foreach($years as $year): ?>
                                    <option value="<?php echo $year; ?>" <?php echo ($year == $currentYear) ? 'selected' : ''; ?>>
                                        <?php echo $year; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="file-upload">
                            <label for="consumption_file">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                                <p>Cliquez ou déposez votre fichier ici</p>
                            </label>
                            <input type="file" id="consumption_file" name="consumption_file" accept=".txt" style="display: none;" required>
                            <div id="file-name">Aucun fichier sélectionné</div>
                        </div>
                        
                        <div class="file-info">
                            <h3>Format de fichier attendu</h3>
                            <p>Le fichier doit être un fichier texte (.txt) avec un identifiant client et une consommation par ligne:</p>
                            <pre>client_id,consommation_kw
1,3500
2,4200
3,2800</pre>
                            <p>Chaque ligne représente un client et sa consommation annuelle en kilowatt-heures (kWh).</p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Importer le fichier
                        </button>
                    </form>
                </div>
            </div>

            
        </div>
    </div>
    
    <script>
        // Display selected filename
        document.getElementById('consumption_file').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Aucun fichier sélectionné';
            document.getElementById('file-name').textContent = fileName;
        });
        
        // Enable drag and drop
        const dropArea = document.querySelector('.file-upload');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropArea.style.borderColor = '#007bff';
        }
        
        function unhighlight() {
            dropArea.style.borderColor = '#ccc';
        }
        
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('consumption_file').files = files;
            document.getElementById('file-name').textContent = files[0] ? files[0].name : 'Aucun fichier sélectionné';
        }
    </script>
</body>
</html>