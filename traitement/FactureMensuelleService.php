<?php
include_once __DIR__ . "/../DB/FactureMensuelleDAO.php";
include_once __DIR__ . "/../models/FactureMensuelle.php";
include_once __DIR__ . "/../DB/ConsommationDAO.php";
require_once __DIR__ . '/../DB/TarificationDAO.php';
require_once __DIR__ . '/../traitement/notificationService.php';

class FactureMensuelleService
{
    private $factureMensuelleRepository;
    private $consommationRepository;
    private $tarificationDAO;
    private $notificationService;

    public function __construct()
    {
        $this->factureMensuelleRepository = new FactureMensuelleDAO();
        $this->consommationRepository = new ConsommationDAO();
        $this->tarificationDAO = new TarificationDAO();
        $this->notificationService = new NotificationService();
    }

    public function submitFactureMensuelle($clientId, $clientName, $currentConsommation, $previousConsommation = null)
    {
        $previousKw = ($previousConsommation !== null) ? $previousConsommation->getKw() : 0;
        $currentKw = $currentConsommation->getKw();
        $consommationKw = $currentKw - $previousKw;

        $montant = $this->calculateMontant($consommationKw);

        $factureMensuelle = new FactureMensuelle($clientId, $clientName, $currentConsommation, $consommationKw, $montant);
        $this->factureMensuelleRepository->submitNewFactureMensuelle($factureMensuelle);

        // Ajouter une notification pour la facture
        $this->notificationService->addNotification(
            $clientId,
            'facture',
            $factureMensuelle->getFactureId(),
            'Votre facture a été générée avec succès. Veuillez la consulter dans votre espace client.'
        );

        return true;
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
}
