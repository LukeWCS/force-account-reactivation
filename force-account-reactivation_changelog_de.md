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

