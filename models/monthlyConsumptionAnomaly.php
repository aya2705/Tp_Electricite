<?php
class MonthlyConsumptionAnomaly {
    private $anomalyId;
    private $clientName;
    private $clientId;
    private $meterId;
    private $entryDate;
    private $previousValue;
    private $enteredValue;
    private $difference;
    private $status;
    
    public function __construct($data = []) {
        $this->anomalyId = $data['anomaly_id'] ?? null;
        $this->clientName = $data['client_name'] ?? null;
        $this->clientId = $data['client_id'] ?? null;
        $this->meterId = $data['meter_id'] ?? null;
        $this->entryDate = $data['entry_date'] ?? null;
        $this->previousValue = $data['previous_value'] ?? null;
        $this->enteredValue = $data['entered_value'] ?? null;
        $this->difference = $data['difference'] ?? null;
        $this->status = $data['status'] ?? 'En attente';
    }

    // Getters
    public function getAnomalyId() { return $this->anomalyId; }
    public function getClientInfo() { return $this->clientName . " (#" . $this->clientId . ")"; }
    public function getMeterId() { return $this->meterId; }
    public function getEntryDate() { return $this->entryDate; }
    public function getPreviousValue() { return $this->previousValue; }
    public function getEnteredValue() { return $this->enteredValue; }
    public function getDifference() { return $this->difference; }
    public function getStatus() { return $this->status; }

    // Setters
    public function setAnomalyId($value) { $this->anomalyId = $value; }
    public function setClientName($value) { $this->clientName = $value; }
    public function setClientId($value) { $this->clientId = $value; }
    public function setMeterId($value) { $this->meterId = $value; }
    public function setEntryDate($value) { $this->entryDate = $value; }
    public function setPreviousValue($value) { $this->previousValue = $value; }
    public function setEnteredValue($value) { $this->enteredValue = $value; }
    public function setDifference($value) { $this->difference = $value; }
    public function setStatus($value) { $this->status = $value; }
}
