<?php
require_once '../../models/consommationMensuelle.php';
class ConsommationDAO {
private $db;

    public function __construct() {
     //   $this->db = Database::getInstance()->getConnection();
    }

    // returns persisted consumption
    public function saveConsumption($clientId, $consommationMensuelle) {
        // Simulate database storage
        $consumption = new ConsommationMensuelle(
            $consommationMensuelle->getKw(),
            $consommationMensuelle->getImagePath()
        );
        
        // Simulate setting an ID as if from database
        $consumption->setId(uniqid());
        $consumption->setIsAbnormal(false);
        
        return $consumption;
    }

    // get lastly submitted consumption
    public function getLastSubmittedConsumption($clientId){
        // here we will do a query 
        // then return the result and create an object 
        return new ConsommationMensuelle(4500, '../assets/image/the_beast.jpg', 'test', false);
    }
    // get all consumptions wil isAbnormal set to true 
    public function getAllAbnormalMonthlyConsumptions(){
        return array(
            0 => new ConsommationMensuelle(4500, '/path', 'test', true),
            1 => new ConsommationMensuelle(4500, '/path', 'test', true),
        );  
    }
    // corrects an abnormal consumption by setting isAbnormal to false and correcting the consumption in kw
    // returns the corrected consumption
    public function correctConsumption($consumptionId, $correctedKW, $isAbnormal){}
}