# Commitments forecaster for WHMCS

---------------------------------------------------------------

### Installation Activation / Deactivation

System for management and planning of outside and inside commitments.

### Functions:

- Adding PDF documents
- Classification of documents by type
- Creation and classification commitment
- Mapping documents to commitments
- Importing invoices from accounting systems
- Creation of reports and forecasts.

### Installation Activation / Deactivation

### WHMCS part setup guide
**Attention!!!**

A WebDAV server/account from NextCloud is required for the correct operation of the module.
Failure to do so makes it impossible to use the system.
PDF files are stored on the NextCloud server and cataloged by type and date of the document (date entered in the document, example: date of receipt of the invoice)

Copy the module folder to the WHMCS 
``
"modules/addons" 
``

From GitHUB
```
git clone https://github.com/PUQ-sp-z-o-o/puq_commitments_forecaster.git
cp -r puq_commitments_forecaster WHMCS_DIR/modules/addons/
```
or 
```
wget https://cf.puq.info/whmcs-module/puq_cf_WHMCS-latest.tar.gz
tar -xzvf puq_cf_WHMCS-latest.tar.gz
cp -r puq_commitments_forecaster WHMCS_DIR/modules/addons/
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
#### Testing:

WHMCS: 8.1.0

NextCloud: 21.0.0

--------------------------------------------------------------
Full documentation can be found here.

EN: https://doc.puq.info/books/en-whmcs-cf-manual

PL: https://doc.puq.info/books/pl-instrukcja-whmcs-cf

---------------------------------------------------------------

The project was created as an amateur, but with a professional approach.

The project is entirely financed by PUQ sp.z o.o. https://puq.pl/

If you would like to help, with the development of the project, financially or in any other way, please let us know 

Contact: cf@puq.info

Site: www.puq.info
