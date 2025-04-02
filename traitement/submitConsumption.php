<?php
session_start();
require_once '../traitement/consommationService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kw = filter_input(INPUT_POST, 'current-value', FILTER_VALIDATE_FLOAT);
    
    // Handle file upload
    $imagePath = null;
    if (isset($_FILES['meter-photo']) && $_FILES['meter-photo']['error'] === 0) {
        $uploadDir = '../uploads/meters/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['meter-photo']['name'];
        $imagePath = $uploadDir . $fileName;
        
        move_uploaded_file($_FILES['meter-photo']['tmp_name'], $imagePath);
    }

    $consumption = new ConsommationMensuelle($kw, $imagePath);
    $service = new ConsommationService();
    
    try {
        $result = $service->submitConsommationMensuelle($_SESSION['user_id'], $consumption);
        echo json_encode(['success' => true, 'message' => 'Consommation enregistrée avec succès']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
