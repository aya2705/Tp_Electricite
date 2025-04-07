<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit;
}

include_once "../DB/FactureAnnuelleDAO.php";
include_once "../DB/ConsommationAnnuelleDAO.php";
include_once "../DB/ClientDAO.php";
require_once __DIR__ . '/../vendor/autoload.php'; // Load autoloader

if (!isset($_GET['id'])) {
    die("ID de facture manquant");
}

try {
    $factureAnnuelleDAO = new FactureAnnuelleDAO();
    $consommationAnnuelleDAO = new ConsommationAnnuelleDAO();
    $clientDAO = new ClientDAO();

    $factureId = $_GET['id'];
    $facture = $factureAnnuelleDAO->getById($factureId);
    
    if (!$facture) {
        die("Facture non trouvée");
    }
    
    // Check if user has access to this invoice
    if ($_SESSION['role'] === 'client' && $_SESSION['client_id'] != $facture->getClientId()) {
        die("Vous n'avez pas accès à cette facture");
    }
    
    // Get client information
    $client = $clientDAO->getClientById($facture->getClientId());
    
    // Get consumption details
    $consommation = $consommationAnnuelleDAO->getParClientEtAnnee($facture->getClientId(), $facture->getAnnee());

    // Create PDF with enhanced design
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator('Système de Facturation Électrique');
    $pdf->SetAuthor('Société des Électricités');
    $pdf->SetTitle('Facture Annuelle ' . $facture->getAnnee());

    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->AddPage();

    // Header
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->Cell(0, 10, 'FACTURE ANNUELLE ' . $facture->getAnnee(), 0, 1, 'C');

    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Référence: FA-' . str_pad($facture->getFactureId(), 6, '0', STR_PAD_LEFT), 0, 1, 'C');
    
    $pdf->Ln(10);

    // Client Information
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Informations Client', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, "Nom: " . $client->getFullName(), 0, 1);
    $pdf->Cell(0, 5, "Adresse: " . $client->getAddress(), 0, 1);
    $pdf->Cell(0, 5, "Téléphone: " . $client->getPhone(), 0, 1);
    $pdf->Cell(0, 5, "Client ID: " . $client->getClientId(), 0, 1);

    $pdf->Ln(10);

    // Invoice Details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Détails de la Facture', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    
    $pdf->Cell(0, 5, 'Année de facturation: ' . $facture->getAnnee(), 0, 1);
    $pdf->Cell(0, 5, 'Date d\'émission: ' . date('d/m/Y', strtotime($facture->getDateEmission())), 0, 1);
    
    // Consumption info
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Consommation', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    
    // Create a table for consumption breakdown
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(80, 7, 'Description', 1, 0, 'L', true);
    $pdf->Cell(50, 7, 'Consommation', 1, 0, 'R', true);
    $pdf->Cell(50, 7, 'Montant (MAD)', 1, 1, 'R', true);
    
    $pdf->Cell(80, 7, 'Consommation annuelle attendue', 1, 0, 'L');
    $pdf->Cell(50, 7, number_format($consommation->getConsommationAttendue(), 2) . ' kWh', 1, 0, 'R');
    $pdf->Cell(50, 7, '-', 1, 1, 'R');
    
    $pdf->Cell(80, 7, 'Consommation annuelle réelle', 1, 0, 'L');
    $pdf->Cell(50, 7, number_format($consommation->getConsommationReelle(), 2) . ' kWh', 1, 0, 'R');
    $pdf->Cell(50, 7, '-', 1, 1, 'R');
    
    $pdf->Cell(80, 7, 'Écart', 1, 0, 'L');
    $ecart = $consommation->getEcart();
    $ecartText = ($ecart > 0 ? '+' : '') . number_format($ecart, 2) . ' kWh';
    $pdf->Cell(50, 7, $ecartText, 1, 0, 'R');
    $pdf->Cell(50, 7, '-', 1, 1, 'R');
    
    $pdf->Ln(5);
    
    // Billing details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Montant à payer', 0, 1);
    
    // Calculate TVA
    $montantHT = round($facture->getMontantTotal() / 1.18, 2);
    $tva = $facture->getMontantTotal() - $montantHT;
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(130, 7, 'Montant HT:', 0, 0, 'R');
    $pdf->Cell(50, 7, number_format($montantHT, 2) . ' MAD', 0, 1, 'R');
    
    $pdf->Cell(130, 7, 'TVA (18%):', 0, 0, 'R');
    $pdf->Cell(50, 7, number_format($tva, 2) . ' MAD', 0, 1, 'R');
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(130, 7, 'Total TTC:', 0, 0, 'R');
    $pdf->Cell(50, 7, number_format($facture->getMontantTotal(), 2) . ' MAD', 0, 1, 'R');
    
    // Footer with legal text
    $pdf->Ln(15);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->MultiCell(0, 4, "Note: Cette facture annuelle récapitule votre consommation électrique pour l'année " . $facture->getAnnee() . ". Elle est établie sur base de la consommation réelle relevée.\n\nMentions légales: Paiement sous 30 jours. Des pénalités de retard de 10% seront appliquées après cette date.", 0, 'J');
    
    // Generate filename
    $filename = 'Facture_Annuelle_' . $facture->getClientId() . '_' . $facture->getAnnee() . '.pdf';
    
    // Save to file system
    $dossierFactures = __DIR__ . '/factures/annuelles/';
    if (!file_exists($dossierFactures)) {
        mkdir($dossierFactures, 0755, true);
    }
    $cheminComplet = $dossierFactures . $filename;
    $pdf->Output($cheminComplet, 'F');

    // Download PDF
    $pdf->Output($filename, 'D');

} catch (Exception $e) {
    error_log("Error generating annual PDF: " . $e->getMessage());
    die("Erreur lors de la génération du PDF: " . $e->getMessage());
}