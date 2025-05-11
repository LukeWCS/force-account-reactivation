### 1.1.3
* Release (2024-12-12)
* PHP:
  * Code verwendet jetzt 7.4 Features.
  * Code strikter gestaltet.
* ACP-Controller:
  * Aktuelle Funktion `select_struct()` von EMP 3.0 übernommen.
* `ext.php`/`composer.json` angepasst:
  * PHP: 7.1.3-8.3.x -> 7.4.0-8.4.x

### 1.1.2
* Release (2024-07-07)
* Fix: Wenn `group_memberships()` im Kontext fehlt, wird die phpBB Komponente nachgeladen. Damit wird ein FATAL beim ACP-Login von nicht-Gründern behoben.
* ACP-Logins werden von FAR jetzt generell ignoriert.

### 1.1.1
* Release (2024-04-26)
* ACP-Template:
  * Im Twig Makro `status()` kann jetzt direkt die Sprachvariable für die Beschreibung übergeben werden, wodurch ein separates `lang()` entfällt.
  * Aktuelles Makro `footer()` von LMR übernommen.
* ACP-Controller:
  * Aktuelle Funktion `set_meta_template_vars()` von LMR übernommen.
  * Kleinere Änderungen.
* Sprachdateien:
  * Kleinere Änderungen.

### 1.1.0
* Release (2024-04-21)
* Fix: Der seltene (exotische) Fall, wenn ein Benutzer keiner Gruppe zugeordnet ist, wird jetzt berücksichtigt, da dies zu einem FATAL führen konnte.
* Benutzerkonten bei denen es noch keinen Login gab, können jetzt optional ebenfalls berücksichtigt werden.
* ACP-Template:
  * Neuen Schalter eingebaut für non-login Konten.
* ACP-Controller:
  * Den neuen non-login Schalter verdrahtet.
* Code Optimierung:
  * Unnötig aufwendigen SQL Code reduziert.
  * Kleinere Verbesserungen.
* JS:
  * Abblenden an den neuen Schalter angepasst.
* CSS:
  * Einheitliche Notation von Werten kleiner 1.
* Sprachdateien:
  * 2 neue Variablen für den neuen Schalter.
  * Kleinere Änderungen bei den deutschen Paketen.
* Migration:
  * Neue Migration für den non-login Schalter.

### 1.0.1
* Release (2024-04-03)
* Fix: In einer seltenen Situation konnte es vorkommen, dass ein Benutzer bei der Anmeldung von FAR ignoriert wurde. Dieser Fehler trat dann auf, wenn der Benutzer Mitglied der NRU Gruppe war und der Administrator die NRU Gruppe in FAR ausgeschlossen hat und dann zu einem späteren Zeitpunkt die NRU Funktion bei phpBB deaktiviert hat.
* Fix: Wenn ein neu registrierter Benutzer nicht durch die NRU Prüfung von FAR ausgeschlossen wurde, dann musste der Benutzer bei der ersten Anmeldung im Forum das Konto ein zweites Mal aktivieren. Das erste Mal regulär durch phpBB und das zweite Mal durch FAR. Die Ursache dafür war, das bei neu registrierten Benutzern das Datum 1.1.1970 als letzter Besuch in der Datenbank eingetragen wird und FAR das nicht berücksichtigt hat.
* ACP-Template:
  * Es werden jetzt die für FAR benötigten und optionalen phpBB Dienste gelistet mit deren jeweiligen Zuständen.
  * Ist die E-Mail-Funktion von phpBB deaktiviert, dann werden bei FAR jetzt sämtliche Optionsgruppen abgeblendet dargestellt um zu signalisieren, dass FAR keine Funktion hat.
  * Ist die NRU Funktion von phpBB deaktiviert, dann wird bei FAR jetzt die zugehörige NRU Optionsgruppe abgeblendet dargestellt um zu signalisieren, dass diese keine Bedeutung hat.
  * Das `select()` Makro hatte einen kleinen Fehler bei der Parameterübergabe, der bei FAR jedoch keine Auswirkung hatte, da das nur Mehrfachauswahl betraf.
* ACP-Controller:
  * `select_struct()` von ToggleControl übernommen zum generieren des Pulldown-Arrays.
  * 2 neue Template Variablen für E-Mail und NRU Funktion.
* JS:
  * `dimOptionGroup()` eingebaut, das ich in anderen Exts verwende um Optionsgruppen abzublenden.
* Sprachdateien:
  * E-Mail Template erneut überarbeitet.
  * 2 Neue Variablen für die Dienste-Anzeige.

### 1.0.0
* Release (2024-03-24)
* ACP-Template:
  * Im Pulldown der Excludes werden Systemgruppen jetzt fett dargestellt, wie bei phpBB.
  * `select()` Makro für die Fett-Darstellung erweitert.
  * `text()` Makro entfernt, wurde nur in der Alpha benötigt.
* Sprachdateien:
  * Texte präzisiert.

#### 1.0.0-b3
* Benutzerverwaltung:
  * In der Übersicht wird bei einem Benutzer jetzt auch das Datum der letzten Passwort-Änderung angezeigt.
  * Ebenso wird in der Übersicht angezeigt, seit wann ein Konto inaktiv ist. Diese Anzeige erscheint nur, wenn das Konto entweder durch einen Admin oder durch FAR deaktiviert wurde.
* Listener:
  * Es werden jetzt Template Variablen für den User Manager generiert.
* Templates:
  * 1 neues Event Template für den User Manager.
* Sprachdateien:
  * Es wird jetzt ein eigenes E-Mail Template benutzt, damit der Text passend für FAR gestaltet werden kann.
  * 1 neue Sprachdatei für den User Manager.
 
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
