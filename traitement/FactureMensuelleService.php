<?php
include_once __DIR__ . "/../DB/FactureMensuelleDAO.php";
include_once __DIR__ . "/../models/FactureMensuelle.php";
include_once __DIR__ . "/../DB/ConsommationDAO.php";
require_once __DIR__ . '/../DB/TarificationDAO.php';

class FactureMensuelleService
{
    private $factureMensuelleRepository;
    private $consommationRepository;
    private $tarificationDAO;

    public function __construct()
    {
        // used to submit a new facture
        $this->factureMensuelleRepository = new FactureMensuelleDAO();
        $this->consommationRepository = new ConsommationDAO();
        $this->tarificationDAO = new TarificationDAO();
    }

    // Ajout d'un paramètre optionnel pour la consommation précédente
    public function submitFactureMensuelle($clientId, $clientName, $currentConsommation, $previousConsommation = null)
    {
        $previousKw = ($previousConsommation !== null) ? $previousConsommation->getKw() : 0;
        $currentKw = $currentConsommation->getKw();
        $consommationKw = $currentKw - $previousKw;

        // Calculer le montant basé sur la consommation réelle
        $montant = $this->calculateMontant($consommationKw);

        $factureMensuelle = new FactureMensuelle($clientId, $clientName, $currentConsommation, $consommationKw, $montant);
        $this->factureMensuelleRepository->submitNewFactureMensuelle($factureMensuelle);
    }

    public function calculateMontant($kw)
    {
        $tarification = $this->tarificationDAO->getTarification();

        $montantHT = 0;
        if ($kw <= 100) {
            $montantHT = $kw * $tarification['tranche1'];
        } elseif ($kw <= 300) {
            $montantHT = (100 * $tarification['tranche1']) + (($kw - 100) * $tarification['tranche2']);
        } else {
            $montantHT = (100 * $tarification['tranche1']) + (200 * $tarification['tranche2']) + (($kw - 300) * $tarification['tranche3']);
        }

        $tva = $montantHT * ($tarification['tva'] / 100);
        return round($montantHT + $tva, 2);
    }

    public function getLastFactureMensuelleByClient($clientId)
    {
        $lastFacture = $this->factureMensuelleRepository->getLastFactureByClientId($clientId);
        return $lastFacture;
    }

    public function getAllFacturesByClient($clientId)
    {
        $Factures = $this->factureMensuelleRepository->getFacturesMensuellesByClientId($clientId);
        return $Factures;
    }

    public function getAllFacturesMensuelles()
    {
        $factures = $this->factureMensuelleRepository->getAllFacturesMensuelles();
        return $factures;
    }

    public function getAnnualAverageMontant($clientId, $year) {
        return $this->factureMensuelleRepository->getAnnualAverageMontant($clientId, $year);
    }

    /*
    public function generateFactureMensuelleTemplate($factureId) {
        $factureData = $this->factureMensuelleRepository->getFactureById($factureId);
        $consommation = $this->consommationMensuelleRepository->getConsumptionById($factureData['consommation_id']);

        // Create PDF with enhanced design
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('Système de Facturation Électrique');
        $pdf->SetAuthor('Société des Électricités');
        $pdf->SetTitle('Facture ' . $factureData['facture_id']);

        $pdf->SetMargins(15, 25, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Header section
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'FACTURE D\'ÉLECTRICITÉ', 0, 1, 'R');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, 'Société des Électricités', 0, 1, 'R');
        $pdf->Cell(0, 5, '123 Avenue Rif, 90002 Tétouan', 0, 1, 'R');
        $pdf->Cell(0, 5, 'Tél: +212 6 12 34 56 78', 0, 1, 'R');
        $pdf->Line(15, 40, 195, 40);
        $pdf->Ln(10);

        // Client information
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, "Client " . $factureData['client_id'], 0, 1);

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Facture N°: FAC-' . str_pad($factureData['facture_id'], 6, '0', STR_PAD_LEFT), 0, 1);
        $pdf->Cell(0, 10, 'Date d\'émission: ' . date('d/m/Y', strtotime($factureData['date_emission'])), 0, 1);
        $pdf->Cell(0, 10, 'Consommation: ' . $consommation->getKw() . ' kWh', 0, 1);
        $pdf->Cell(0, 10, 'Montant: ' . number_format($factureData['montant'], 2) . ' MAD', 0, 1);




        // Footer
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->MultiCell(0, 5, 'Mentions légales: Paiement sous 30 jours. Pénalité de retard: 10% du montant après 30 jours.', 0, 'J');

        // Generate and output PDF
        $nomClient = preg_replace('/[^a-zA-Z0-9-_]/', '_', $factureData['client_id']);
        $filename = 'Facture_' . $nomClient . '_' . date('Y-m') . '.pdf';

        // Save to file system
        $dossierFactures = __DIR__ . '/./factures/';
        if (!file_exists($dossierFactures)) {
            mkdir($dossierFactures, 0755, true);
        }
        $cheminComplet = $dossierFactures . $filename;
        $pdf->Output($cheminComplet, 'F');

        // Download PDF
        $pdf->Output($filename, 'D');
    } */
}
