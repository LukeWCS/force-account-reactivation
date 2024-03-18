#### 1.0.0-b2
* Statt die NRUs über das Pulldown ausschliessen zu können, gibt es dafür jetzt einen separaten Schalter. Ist ein Benutzer Mitglied der NRUs, wird die Ausschluss-Liste komplett ignoriert und nur dieser Schalter berücksichtigt.
* Listener:  
  * Neuer SQL Code um die ID der NRU Gruppe zu ermitteln.
  * Code geändert um den neuen Schalter zu berücksichtigen.
* ACP-Template:
  * Separate Anzeige für die Mehrfachauswahl-Erklärung.
  * Neuen Schalter für die NRUs hinzugefügt.
* ACP-Controller:
  * Den neuen NRU Schalter verdrahtet.
  * Im Pulldown für die Excludes werden die NRUs nicht mehr gelistet.
* Sprachdateien:
  * Neue Variablen für den neuen NRU Schalter hinzugefügt.
  * Texte präzisiert.
  * Erklärung für die Mehrfachauswahl von der Erklärung für die ausgeschlossenen Gruppen getrennt.
* Migration:
  * Neue Config Variable für den NRU Schalter.

#### 1.0.0-b1
* Initial Release.
* Erste interne Testversion.
