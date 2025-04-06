<?php
include_once __DIR__ . '/../DB/consommationDAO.php';
require_once __DIR__ . '/../models/consommationMensuelle.php';
require_once __DIR__ . '/../models/monthlyConsumptionAnomaly.php'; // Include the anomaly model
require_once __DIR__ . '/../traitement/FactureMensuelleService.php'; // Include the anomaly model

// as a client i need to submit a monthly submission (kw + image)
// when i submit a new monthly consumption (it should be stored in the data base (monthly consumption)) 
// i need a function that will take in the clientId and the consumption oject

// constraints , after creating a monthly consumption , we need to know if we should create a monthly anomalie or create a monthlyFacture

// if the monthly consumption is really superior or really inferior than the monthly consumption of the last month
// then this consumption will be flagged as an anomalie

class consommationService
{
    private $consommationRepository;
    private $factureMensuelleService;
    // here we will inject the data access object for consommationService
    public function __construct()
    {
        $this->factureMensuelleService = new FactureMensuelleService();
        $this->consommationRepository = new ConsommationDAO();
    }

    public function getLastMonthlyConsumption($clientId)
    {
        return $this->consommationRepository->getLastNormalConsumption($clientId);
    }

    public function submitConsommationMensuelle($clientId, $consommationMensuelle)
    {
        // Récupérer le dernier relevé avant l'insertion
        $lastNormalConsumption = $this->consommationRepository->getLastNormalConsumption($clientId);

        $previousKw = $lastNormalConsumption ? $lastNormalConsumption->getKw() : 0;
        $currentKw = $consommationMensuelle->getKw();
        $isAnomaly = $this->checkForMonthlyConsumptionAnomaly($clientId, $currentKw);

        $clientName = $this->consommationRepository->getClientName($clientId);

        $saveResult = $this->consommationRepository->saveConsumption($clientId, $consommationMensuelle);
        $persistedConsumption = $saveResult['consumption'];
        $compteurId = $saveResult['compteur_id'];

        if ($isAnomaly) {
            $difference = abs($currentKw - $previousKw);
            $previousId = $lastNormalConsumption ? $lastNormalConsumption->getId() : null;
            $entryDate = $persistedConsumption->getCreatedAt();
            $previousValue = $previousKw;
            $enteredValue = $currentKw;
            $imagePath = $persistedConsumption->getImagePath();

            $this->consommationRepository->createAnomaly(
                $persistedConsumption->getId(),
                $previousId,
                $clientName,
                $compteurId,
                $entryDate,
                $previousValue,
                $enteredValue,
                $difference,
                $imagePath
            );
            return ['status' => 'anomaly', 'consumption' => $persistedConsumption];

        } else {
            // Passer le dernier relevé obtenu AVANT l'insertion
            $this->factureMensuelleService->submitFactureMensuelle($clientId, $clientName, $persistedConsumption, $lastNormalConsumption);
            return ['status' => 'normal', 'consumption' => $persistedConsumption];
        }
    }

    private function checkForMonthlyConsumptionAnomaly($clientId, $kw)
    {
        $lastNormalConsumption = $this->consommationRepository->getLastNormalConsumption($clientId);
        if (!$lastNormalConsumption) {
            return false; // No previous consumption to compare with
        }
        $previousKw = $lastNormalConsumption->getKw();
        return $kw >= $previousKw * 10 || $kw < $previousKw;
    }

    public function getAllMonthlyConsumptionsWithAnomaly()
    {
        $anomaliesData = $this->consommationRepository->getAllAnomalies();
        $anomalies = [];

        foreach ($anomaliesData as $data) {
            $anomaly = new MonthlyConsumptionAnomaly();

            $anomaly->setAnomalyId($data['anomalie_id'] ?? null);
            $anomaly->setClientName($data['client_name'] ?? 'Inconnu');
            $anomaly->setMeterId($data['compteur_id'] ?? 'N/A');
            $anomaly->setEntryDate($data['entry_date'] ?? null);
            $anomaly->setPreviousValue($data['previous_value'] ?? 0);
            $anomaly->setEnteredValue($data['entered_value'] ?? 0);
            $anomaly->setDifference($data['difference'] ?? 0);
            $anomaly->setStatus($data['status'] ?? 'En attente');
            $anomaly->setClientId(null); // Not provided by DAO

            $anomalies[] = $anomaly;
        }

        return $anomalies;
    }

    // this should return a consumption related to an anomaly
    public function getConsumptionByAnomalyId($anomalieId)
    {
        return $this->consommationRepository->getConsumptionByAnomalyId($anomalieId);
    }

    // Ajout de la méthode pour récupérer les détails d'une anomalie
    public function getAnomalyDetails($anomalyId) {
        return $this->consommationRepository->getAnomalyDetails($anomalyId);
    }

    // as a Fournisseur after viewing the anomaly i need to correct it by updating the consumption and generating the facture
    public function treatMonthlyConsumptionWithAnomaly($clientId, $anomalyId, $correctedKW)
    {
        // Récupérer le dernier relevé normal pour ce client
        $lastNormalConsumption = $this->consommationRepository->getLastNormalConsumption($clientId);

        // Corrige la consommation et supprime l'anomalie
        $data = $this->consommationRepository->correctConsumptionAndDeleteAnomaly($anomalyId, $correctedKW);

        // Si le relevé précédent est identique au relevé corrigé, le laisser à null
        if ($lastNormalConsumption && $lastNormalConsumption->getId() === $data['persistedConsumption']->getId()) {
            $lastNormalConsumption = null;
        }

        // Générer la facture en passant la consommation corrigée et, le cas échéant, la consommation précédente
        $this->factureMensuelleService->submitFactureMensuelle(
            $data['clientId'],
            $data['clientName'],
            $data['persistedConsumption'],
            $lastNormalConsumption
        );
    }
}