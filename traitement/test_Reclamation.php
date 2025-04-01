<?php
require_once '../DB/models/Reclamation.php';

$reclamation = Reclamation::getReclamationById(11); // Remplace 1 par un ID existant
echo "<pre>";
print_r($reclamation);
echo "</pre>";
