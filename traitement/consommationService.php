<?php
require_once __DIR__ . '/../DB/consommationDAO.php';
require_once __DIR__ . '/../models/consommationMensuelle.php';
// as a client i need to submit a monthly submission (kw + image)
// when i submit a new monthly consumption (it should be stored in the data base (monthly consumption)) 
// i need a function that will take in the clientId and the consumption oject

// constraints , after creating a monthly consumption , we need to know if we should create a monthly anomalie or create a monthlyFacture

// if the monthly consumption is really superior or really inferior than the monthly consumption of the last month
// then this consumption will be flagged as an anomalie

class consommationService {
    private $consommationRepository;
    // here we will inject the data access object for consommationService
    public function __construct() {
        $this->consommationRepository = new ConsommationDAO();
    }

    // this function will return the lastly submitted consumption (works well)
    public function getLastMonthlyConsumption($clientId){
        return $this->consommationRepository->getLastSubmittedConsumption($clientId);
    }

    // as a client i want to submit a cosommation mensuelle, if no anomalie is detected we create a facture and generate it, if an anomalie is detected the consumption is flagged and no facture is created
    // either ways the consommation is persisted 
    public function submitConsommationMensuelle($clientId, $consommationMensuelle) { 
        // here we should call the checkForAnomalieMensuelle to check for an anomalie
        /*
        if(checkForAnomalieMensuelle($clientId,$consommationMensuelle->kw)) {
              // if an anomalie is detected we :
                    // set consommation mensuelle (isAbnormal -> true)
                    // persist it, this returns the persisted consumption
                    persistedConsumption = consommationRepository.saveConsumption($clientId, $consommationMensuelle)
                    // return a message that signals to the user that the submitted consumption is abnormal.
        } else {
              // if no anomalie is detected we will create a new consumption with isAbnormal set to false
              // we will save it in the database using "consommationRepository.saveConsumption($clientId, $consommationMensuelle)"
              // we will catch the persisted consumption
              // we will call the facturationService to create a factureMensuelle and link it to the user in the database
              // factureService.createFactureMensuelle($clientId, consommationMensuelle) 
        }
        */
        // For now, we'll assume no anomalies
        echo "reached the service method that persists the consumption now calling DAO";
        return $this->consommationRepository->saveConsumption($clientId, $consommationMensuelle);
    }

    // this function checks if there is an abnormal difference between the last consumption and the new submitted one it either returns true or false  
    public function checkForMonthlyConsumptionAnomaly($clientId, $kw) {
        // here we will retrieve the last consumption submitted by this client 
        //$consumption = consommationRepository.getLastSubmittedConsumption($clientId)
        // then we will compare $consumption->kw with $kw if the difference is big we will return true
        // else we return false
    }

    // as a Fournisseur i need all abnormal monthly consumptions
    public function getAllMonthlyConsumptionsWithAnomaly() {
        // this will return all monthly consumptions that have isAbnormal set to true
        // consommationRepository.getAllAbnormalMonthlyConsumptions() 
    }

    // as a Fournisseur after viewing the anomaly i need to correct it by updating the consumption and generating the facture
    public function treatMonthlyConsumptionWithAnomaly($consumptionId, $correctedConsumptionKW){
        // here we should retrieve the consumption 
        // correctConsumption($consumptionId, $correctedConsumptionKW, true) // the true is for is abnormal
        // then we should create a facture for this corrected consumption
    }
}