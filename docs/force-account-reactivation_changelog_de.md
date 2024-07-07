### 1.1.2
(2024-07-07)
* Fix: Wenn ein Benutzer mit Admin-Rechten aber ohne Gründer-Status einen ACP-Login durchgeführt hat, verursachte das einen FATAL, da in diesem Kontext eine benötigte phpBB Funktion nicht vorhanden war. Jetzt wird geprüft, ob die betreffende phpBB Funktion im Kontext vorhanden ist und wenn nicht, wird die entsprechende phpBB Komponente nachgeladen.
* Ergänzend zum Fix; FAR reagiert nur noch bei primären Logins, jedoch nicht mehr bei sekundären ACP-Logins. Das war ohnehin nicht sinnvoll.

### 1.1.1
(2024-06-24)

* Reines Optimier-Update, keine funktionellen Änderungen.
* Repo Struktur Composer-tauglich gestaltet.

### 1.1.0
(2024-04-21)

* Fix: Der seltene (exotische) Fall, wenn ein Benutzer keiner Gruppe zugeordnet ist, wird jetzt berücksichtigt, da dies zu einem FATAL führen konnte.
* Benutzerkonten bei denen es noch keinen Login gab, können jetzt optional ebenfalls berücksichtigt werden. Dafür gibt es einen neuen Schalter, der per Standard deaktiviert ist.
* Code Optimierung:
  * Unnötig aufwendigen SQL Code reduziert.
  * Kleinere Verbesserungen.
* Sprachdateien:
  * 2 neue Variablen für den neuen Schalter.
  * Kleinere Änderungen bei den deutschen Paketen.

### 1.0.1
(2024-04-03)

* Fix: In einer speziellen (seltenen) Situation konnte es vorkommen, dass ein Benutzer bei der Anmeldung von FAR ignoriert wurde. Dieser Fehler trat dann auf, wenn der Benutzer Mitglied der NRU Gruppe war und der Administrator die NRU Gruppe in FAR ausgeschlossen hat und dann zu einem späteren Zeitpunkt die NRU Funktion bei phpBB deaktiviert hat.
* Fix: Wenn ein neu registrierter Benutzer nicht durch die NRU Prüfung von FAR ausgeschlossen wurde, dann musste der Benutzer bei der ersten Anmeldung im Forum das Konto ein zweites Mal aktivieren. Das erste Mal regulär durch phpBB und das zweite Mal durch FAR. Die Ursache dafür war, das bei neu registrierten Benutzern das Datum 1.1.1970 als letzter Besuch in der Datenbank eingetragen wird und FAR das nicht berücksichtigt hat.
* Einstellungen:
  * Es werden jetzt die für FAR benötigten und optionalen phpBB Dienste gelistet mit deren jeweiligen Zuständen; Grüner Haken (aktiviert), rotes Kreuz (deaktiviert).
  * Ist die E-Mail Funktion von phpBB deaktiviert, dann werden bei FAR sämtliche Optionsgruppen abgeblendet dargestellt um zu signalisieren, dass FAR keine Funktion hat.
  * Ist die NRU Funktion von phpBB deaktiviert, dann wird bei FAR die zugehörige NRU Optionsgruppe abgeblendet dargestellt um zu signalisieren, dass diese keine Bedeutung hat.
* E-Mail Template erneut überarbeitet.

### 1.0.0
(2024-03-24)

* Erste offizielle Version.
