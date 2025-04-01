<?php
require_once __DIR__ . '/../DB/connexion.php';
require_once __DIR__.'/../models/Facture.php';
require_once __DIR__.'/../DB/factureDAO.php';
require_once __DIR__.'/../DB/clientDAO.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de facture invalide");
}

$facture_id = (int)$_GET['id'];

try {
  
    $db = creerConnexion();
    $factureDAO = new FactureDAO($db);
    $clientDAO = new ClientDAO($db);
    
    $facture = $factureDAO->getById($facture_id);
    if (!$facture) {
        die("Facture non trouvée");
    }
    
    $client = $clientDAO->getById($facture->getClientId());
    
   
    require_once 'C:/xampp/htdocs/Tp_Electricite/vendor/tecnickcom/tcpdf/tcpdf.php';
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
  
    $pdf->SetCreator('Système de Facturation Électrique');
    $pdf->SetAuthor('Société des Électricités');
    $pdf->SetTitle('Facture ' . $facture_id);
    
 
    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->AddPage();

    
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'FACTURE D\'ÉLECTRICITÉ', 0, 1, 'R');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, 'Société des Électricités', 0, 1, 'R');
    $pdf->Cell(0, 5, '123 Avenue Rif, 90002 Tétouan', 0, 1, 'R');
    $pdf->Cell(0, 5, 'Tél: +212 6 12 34 56 78', 0, 1, 'R');
    $pdf->Line(15, 40, 195, 40);
    $pdf->Ln(10);


    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Client', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, $client->getFullName(), 0, 1);
    $pdf->Cell(0, 5, $client->getAddress(), 0, 1);
    $pdf->Cell(0, 5, 'Tél: ' . $client->getPhone(), 0, 1);
    $pdf->Ln(5);

   
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Détails de la facture', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    
    $pdf->Cell(40, 5, 'Référence:', 0, 0);
    $pdf->Cell(0, 5, 'FACT-' . $facture_id, 0, 1);
    
    $pdf->Cell(40, 5, 'Période:', 0, 0);
    $pdf->Cell(0, 5, date('F Y', strtotime($facture->getPeriode())), 0, 1);
    
    $pdf->Cell(40, 5, 'Consommation:', 0, 0);
    $pdf->Cell(0, 5, $facture->getConsommation() . ' kWh', 0, 1);
    
    $pdf->Cell(40, 5, 'Date émission:', 0, 0);
    $pdf->Cell(0, 5, date('d/m/Y', strtotime($facture->getDateEmission())), 0, 1);
    
    if ($facture->getStatutPaiement() === 'payée') {
        $pdf->Cell(40, 5, 'Date paiement:', 0, 0);
        $pdf->Cell(0, 5, date('d/m/Y', strtotime($facture->getDatePaiement())), 0, 1);
    }
    
    $pdf->Ln(10);

    $consommation = $facture->getConsommation();
   
    $tranche1 = min($consommation, 100);
    $prix_tranche1 = 0.82;
    $montant1 = $tranche1 * $prix_tranche1;

    
    $tranche2 = ($consommation > 100) ? min($consommation - 100, 50) : 0;
    $prix_tranche2 = 0.92;
    $montant2 = $tranche2 * $prix_tranche2;


    $tranche3 = ($consommation > 150) ? $consommation - 150 : 0;
    $prix_tranche3 = 1.10;
    $montant3 = $tranche3 * $prix_tranche3;

    $totalHT = $montant1 + $montant2 + $montant3;
    $tva = $totalHT * 0.10;
    $totalTTC = $totalHT + $tva + $facture->getPenalites();

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Détails tarifaires', 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(100, 7, 'Tranche de consommation', 1, 0, 'C');
    $pdf->Cell(45, 7, 'Prix unitaire (DH)', 1, 0, 'C');
    $pdf->Cell(45, 7, 'Montant (DH)', 1, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(100, 7, 'Premiers 100 kWh', 1, 0);
    $pdf->Cell(45, 7, number_format($prix_tranche1, 2, ',', ' '), 1, 0, 'R');
    $pdf->Cell(45, 7, number_format($montant1, 2, ',', ' '), 1, 1, 'R');

    if ($montant2 > 0) {
        $pdf->Cell(100, 7, 'De 101 à 150 kWh', 1, 0);
        $pdf->Cell(45, 7, number_format($prix_tranche2, 2, ',', ' '), 1, 0, 'R');
        $pdf->Cell(45, 7, number_format($montant2, 2, ',', ' '), 1, 1, 'R');
    }

    if ($montant3 > 0) {
        $pdf->Cell(100, 7, 'Plus de 151 kWh', 1, 0);
        $pdf->Cell(45, 7, number_format($prix_tranche3, 2, ',', ' '), 1, 0, 'R');
        $pdf->Cell(45, 7, number_format($montant3, 2, ',', ' '), 1, 1, 'R');
    }

   
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(145, 7, 'Total HT', 1, 0, 'R');
    $pdf->Cell(45, 7, number_format($totalHT, 2, ',', ' '), 1, 1, 'R');

    $pdf->Cell(145, 7, 'TVA (10%)', 1, 0, 'R');
    $pdf->Cell(45, 7, number_format($tva, 2, ',', ' '), 1, 1, 'R');

    if ($facture->getPenalites() > 0) {
        $pdf->Cell(145, 7, 'Pénalités de retard', 1, 0, 'R');
        $pdf->Cell(45, 7, number_format($facture->getPenalites(), 2, ',', ' '), 1, 1, 'R');
    }

    $pdf->Cell(145, 7, 'Total TTC', 1, 0, 'R');
    $pdf->Cell(45, 7, number_format($totalTTC, 2, ',', ' '), 1, 1, 'R');


    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->MultiCell(0, 5, 'Mentions légales: Paiement sous 30 jours. Pénailté de retard: 10% du montant après 30 jours.', 0, 'J');

    
    $nomClient = preg_replace('/[^a-zA-Z0-9-_]/', '_', $client->getFullName());
    $filename = 'Facture_' . $nomClient . '_' . date('Y-m') . '.pdf';

  
    $dossierFactures = __DIR__.'/./factures/';
    if (!file_exists($dossierFactures)) {
        mkdir($dossierFactures, 0755, true);
    }
    $cheminComplet = $dossierFactures . $filename;
    $pdf->Output($cheminComplet, 'F');

    $pdf->Output($filename, 'D');

} catch (Exception $e) {
    die("Erreur lors de la génération du PDF: " . $e->getMessage());
}