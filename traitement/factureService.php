<?php
class FactureService {
    private $factureDAO;
    private $tarifDAO;
    private $consommationService;

    public function __construct(
        FactureDAO $factureDAO, 
        TarifDAO $tarifDAO,
        ConsommationService $consommationService
    ) {
        $this->factureDAO = $factureDAO;
        $this->tarifDAO = $tarifDAO;
        $this->consommationService = $consommationService;
    }

    public function genererFacturesPourPeriode($periode_id) {
        $consommations = $this->consommationService->getConsommationsValidees($periode_id);
        $count = 0;
        
        foreach ($consommations as $consommation) {
            try {
                $this->creerFacture(
                    $consommation->getClientId(),
                    $consommation->getValeur(),
                    $consommation->getPeriode()
                );
                $count++;
            } catch (Exception $e) {
               
                error_log("Erreur création facture pour client " . 
                         $consommation->getClientId() . ": " . $e->getMessage());
            }
        }
        
        return $count;
    }
}
?>
