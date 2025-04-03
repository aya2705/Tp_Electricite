Traitement "Services" :
each service provides functions or methods related to a specific aspect of the app.

example : 
 - consommationService : provides methods to manage consumptions (submitMonthlyClientConsumption(), getLatestConsumption()...)
 - anomalieService : provides methods to manage consumption anomalies (checkForAnomaly(consumption), generateMonthlyConsumptionAnomaly()... )
 - facturationService : provides methods to manage invoices (generateFacture(), saveFacture() )
 - reclamationService .... 

 the idea is to adopt a layered architecture !! 

 business logic services do specific tasks : 
  - receive HTTP requests from the view (example: give me all factures)
  - conduct logic and use data access objects 
  - then either render the view by executing files from the View (IHM) folder or return data in the form of JSON to the Browser.

IHM : will contain templates (with Javascript for AJAX calls !! )

DB : contains data access objects


