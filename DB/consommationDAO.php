<?php

class ConsommationDAO {
    // returns persisted consumption
    public function saveConsumption($clientId, $consommationMensuelle){}
    // get lastly submitted consumption
    public function getLastSubmittedConsumption($clientId){}
    // get all consumptions wil isAbnormal set to true 
    public function getAllAbnormalMonthlyConsumptions(){}
    // corrects an abnormal consumption by setting isAbnormal to false and correcting the consumption in kw
    // returns the corrected consumption
    public function correctConsumption($consumptionId, $correctedKW, $isAbnormal){}
}