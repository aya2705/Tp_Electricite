<?php
require_once __DIR__ . '/../DB/connexion.php';
require_once __DIR__ . '/../models/Facture.php';
require_once __DIR__ . '/../models/Tarif.php';
require_once __DIR__ . '/../DB/factureDAO.php';
require_once __DIR__ . '/../DB/tarifDAO.php';

$db = creerConnexion();
$factureDAO = new FactureDAO($db);
$tarifDAO = new TarifDAO($db);

// 1. Récupérer le tarif actuel
$tarif = $tarifDAO->getTarifActuel();
if (!$tarif) {
    die("Aucun tarif actif trouvé");
}

// 2. Récupérer les consommations validées à facturer
$stmt = $db->prepare("
    SELECT c.client_id, c.periode, c.valeur as consommation
    FROM consommations c
    WHERE c.statut = 'validée' AND c.facture_id IS NULL
    AND c.periode = ?
");
$periode = date('Y-m', strtotime('last month'));
$stmt->execute([$periode]);

// 3. Générer les factures
while ($consommation = $stmt->fetch()) {
    $montant = $tarif->calculerMontantTTC($consommation['consommation']);
    
    $facture = new Facture([
        'client_id' => $consommation['client_id'],
        'periode' => $consommation['periode'],
        'consommation' => $consommation['consommation'],
        'montant' => $montant,
        'statut_paiement' => 'impayée',
        'date_emission' => date('Y-m-d'),
        'tarif_id' => $tarif->getTarifId()
    ]);
    
    $factureDAO->create($facture);
    
    // Marquer la consommation comme facturée
    $db->prepare("UPDATE consommations SET facture_id = ? WHERE client_id = ? AND periode = ?")
       ->execute([$facture->getFactureId(), $consommation['client_id'], $consommation['periode']]);
}

echo "Factures générées avec succès pour la période $periode";
?>