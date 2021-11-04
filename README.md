# puq_commitments_forecaster

---------------------------------------------------------------

### Installation Activation / Deactivation

System for management and planning of outside and inside commitments.

Functions:

- Adding PDF documents
- Classification of documents by type
- Creation and classification commitment
- Mapping documents to commitments
- Importing invoices from accounting systems
- Creation of reports and forecasts.

### WHMCS part setup guide
**Attention!!!**

A WebDAV server/account from NextCloud is required for the correct operation of the module.
Failure to do so makes it impossible to use the system.
PDF files are stored on the NextCloud server and cataloged by type and date of the document (date entered in the document, example: date of receipt of the invoice)

Copy the module folder to the WHMCS 
```
"modules/addons" 
```

Then in the administrator panel of WHMCS: 
```
System Settings -> Addons Modules
```

Activate the module by clicking the **"Activate"** button in the "Commitments forecaster" module. 
Activating the module, adding the necessary tables in the WHMCS database with the "puq_" prefix


**Attention!!!** 

Deactivating the module automatically deletes the tables in the database associated with that module.


---------------------------------------------------------------
Testing:
WHMCS: 8.1.0

NextClous: 21.0.0

--------------------------------------------------------------


