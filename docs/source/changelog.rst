Changelog
=========

v3.0.1
------

* bei Rechnungskauf-Vorgängen wird keine Zinsposition mehr angezeigt
* der Express-Button erhält nun den korrekten Betrag (Produktpreis & Warenkorb-Betrag)
* die Anzahl der Raten wird aus dem Express-Button übernommen  
* die Initialisierungslogik wurde überarbeitet (detailierteres Fehlerreporting & zuverlässigere Initialisierung)

v3.0.0
------

* easyCredit-Rechnung wurde als weitere Zahlungsart integriert

v2.1.9
------

* behebt ein Problem, durch das der Zahlungsstatus nicht gesetzt wurde

v2.1.8
------

* behebt ein Problem mit dem Express-Checkout, durch das die Adresse nicht verifiziert werden konnte
* die asynchrone Authorisierung wurde entfernt, da die Transaktionen synchron autorisiert und geprüft werden

v2.1.7
------

* Verbesserung der Kompatibilität mit Drittanbieter-Plugin
* Fehlermeldungen werden nun, wenn verfügbar, in deutscher Sprache ausgegeben
* bei Ratenkauf-Initialisierung wird die Produkt-URL und die URL des Hauptbildes übertragen
* der Betrag bei Express Checkout wird nun versionsübergreifend inkl. Zahlungsartenaufschlag berechnet
* die Bestellnummer / Session-ID wird nun bei Überlänge abgeschnitten
* das Debug-Logging funktioniert nun auch mit älteren Shopware-Versionen

v2.1.6
------

* Verbesserung der Performance

v2.1.5
------

* Verbesserung der Kompatibilität mit älteren Shopware-Versionen (Zins-Flex)

v2.1.4
------

* Integration von Ausnahmemöglichkeiten von Zins-Flex

v2.1.3
------

* Ratenrechner-Widget & Express-Button können nun auch im OffCanvas-Warenkorb angezeigt werden
* das Plugin definiert nun eine eigene Berechnung für die Betragsregeln des Risiko-Managements bei der die Zinsen herausgerechnet werden
* eigene Bilder in den Marketing-Komponenten können nun auf das Standardbild zurückgesetzt werden
* behebt einen Fehler bei dem Body-Klassen ohne Leerzeichen zusammengefügt wurden (Block `frontend_index_body_classes`)
* PHP 8.2 Kompatbilität

v2.1.2
------

* behebt einen Case sensitivity-Fehler, der ein erfolgreiches Update verhinderte

v2.1.1
------

* umfangreiche Marketing-Komponenten wurden eingefügt und sind über das Backend konfigurierbar
* die Einstellung für das Ratenrechner-Widget wird wieder berücksichtigt
* der Button zum Überprüfen der Zugangsdaten ist wieder vorhanden

v2.1.0
------

* Express-Checkout: der Ratenkauf kann direkt von der Produktdetailseite oder aus dem Warenkorb heraus gestartet werden


v2.0.5
------

* behebt ein Problem beim Update auf v2.0.4

v2.0.4
------

* behebt ein Problem, bei dem das Konfigurationsfeld API-Passwort doppelt angezeigt 
* Werte des alten Feldes werden automatisch in das neue Feld migriert (bei Multistore-Konfigurationen mit Vererbungen sind die Zugangsdaten ggf. zusätzlich manuell zu prüfen)
* obsolete CSS-Definitionen wurden entfernt

v2.0.3
------

* eine Bestellung kann nur abgeschlossen werden, wenn der Transaktionstatus PREAUTHORIZED ist, andernfalls erhält der Kunde eine Fehlermeldung
* eine Bestellung wird nur als bezahlt markiert, wenn der Transaktionsstatus bei Aufruf des AuthorizationCallback AUTHORIZED ist
* in der Transaktionsübersicht werden keine abgebrochenen Bestellungen mehr angezeigt
* das Ratenrechner-Widget wird in bestimmten Fällen nicht mehr doppelt angezeigt

v2.0.2
------

* Änderungen zum Markenrelaunch von easyCredit-Ratenkauf

v2.0.1
------

* behebt einen Fehler bei Übertragung der Anrede

v2.0.0
-------

* Migration auf ratenkauf by easyCredit API v3
* Integration von EasyCredit Ratenkauf Web-Komponenten

v1.8.7
------

* Prüfung auf ausgewählte Zahlart in Subscriber (behebt ein Problem mit einer Onepage Checkout Extension)

v1.8.6
------

* "Der Finanzierungsbetrag liegt ausserhalb der zulässigen Beträge" wird nun als INFO statt als ERROR geloggt
* es wurden textliche Anpassungen und Vereinheitlichungen durchgeführt  
* der Menüpunkt unter "Zahlungen" im Backend wird nun auch bei einem Plugin-Update angelegt

v1.8.5
------

* Änderung zur Kompatibilität mit Shopware 5.7.7

v1.8.4
------

* tritt beim Abschliessen der Bestellung ein Fehler in der Kommunikation auf, erhält die Bestellung den neuen Status "Bestellstatus bei Fehlern"
* Änderungen zur Kompatibilität mit Shopware >= 5.7
* der Ratenrechner wird nun so dargestellt, dass der Inhalt vollständig ohne Scrollen gezeigt wird

v1.8.3
-------

* Vor- und Nachname werden nun in die Adressüberprüfung einbezogen
* die Ratenanzahl wird nun nicht mehr statisch übergeben
* die API-Library wurde auf v.1.6 aktualisiert

v1.8.2
------

* die Versandmethode wird nun bei Initialisierung zuverlässig übertragen

v1.8.1
------

* verbessert die Zuverlässigkeit im Zusammenspiel mit Drittanbieter-Plugins

v1.8.0
------
* eine Versandart kann für „Click & Collect“ definiert werden
* die Konfiguration wurde übersichtlicher strukturiert
* die API-Library wurde aktualisiert und wird nun über Composer eingebunden
* beim Entfernen der Zinsen wird explizit auf die Zahlungsart geprüft

v1.7.3
------
* Verbesserung der Kompatibilität mit Drittanbieter-Zahlungsplugins

v1.7.2
------
* der Link zu "Was ist ratenkauf by easyCredit" wurde aktualisiert

v1.7.1
------
* Schriftart in Merchant-Interface wurde ausgetauscht
* kleinere Fehlerbehebungen in Transaktionsmanager

v1.7.0
------
* Integration des Merchant-Interfaces zur Transaktionsverwaltung
* der vormals integrierte Tilgunsplan wurde vollständig entfernt

v1.6.6
------
* durch Pickware generierte Rechnungen enthalten nun keine MwSt. mehr

v1.6.5
------
* Konstante \Shopware::VERSION wird nun auf Existenz geprüft (führte zu Fehler in Shopware > 5.6)

v1.6.4
------
* die Bestellnummer wird bei der Zahlungsbestätigung an API übergeben
* es wird nun die v2 der easyCredit API verwendet (ausgenommen "Zugangsdaten testen")
* HTML Tags werden vor Übermittlung an die API aus Versandart entfernt (verhindert API Fehler bei img-Tag in Versandart)
* Erweitere Cache-Invalidierung bei Plugin-Update
* die Zahlartenfelder werden nun immer in definierter Reihenfolge dargestellt
* Fehlerbehebung SW 5.0: Zahlungsdatum führt zu Column not found 'name', Zahlungsdatum entfernt
* wenn die Bestellung nicht bestätigt werden kann, z.B. durch Timeout, wird eine entsprechende Fehlermeldung angezeigt; der Zahlungsstatus bleibt "offen"

v1.6.3
------
* Anpassung der Merchant Interface URL für Transaktionssuche (z.B. /transaktionen/TKTW2J)
* Optimierung des Loggings für die Merchant API
* Anpassung der Widget URL auf neue Version

v1.6.2
------
* das Widget wird nun auch bei Beträgen bis 10.000 EUR angezeigt

v1.6.1
------
* behebt einen Fehler beim Auslesen der System-Version in Shopware 5.6.0

v1.6.0
------
* Integration von Transaktions-Statusänderung bei Status für "Lieferung melden" & "Rückabwicklung melden"

v1.5.5
------
* behebt ein Problem beim Plugin-Update, dass dazu führte dass Ressourcen ohne Neuinstallation nicht mehr korrekt angezeigt wurden

v1.5.4
------
* CSS- und JS-Ressourcen werden nun auch über die Konsole (sw:theme:cache:generate) korrekt kompiliert (umgeht einen Fehler in Shopware, Widget-Anzeige)
* durch eine Änderung in Shopware 5.5.8 kam es zu einem Fehler im Checkout. Das Plugin wurde entsprechend angepasst, damit dieser Fehler nicht mehr auftritt

v1.5.3
------
* die Zinsen werden nun nach einem Abbruch der Bestellung / Wechsel der Zahlungsart zuverlässig entfernt (siehe #3594)
* Bestellstatus und Zahlungsstatus Dropdown zeigen ihre Werte nun zuverlässig an (siehe #3592)
* der "Modus" (Artikeltyp) der Zinsen wird nach Bestellung angepasst, um ein korrektes Steuerhandling in Rechnung zu erreichen
* die Zustimmungserklärung wird nun pro Store gecacht (Multi-Store Kompatibilität)

v1.5.2
------
* Möglichkeit der Änderung der Adresse bei nicht akzeptierten Adressen oder Adresskombinationen über konditional eingeblendete Lightbox (#3526)
* Angabe einer abweichenden Lieferadresse im Bestätigungsschritt ist nicht mehr möglich bei Zahlart ratenkauf by easyCredit
* die statische Zustimmungserklärung wird einen Tag im Shop des Händlers gecacht, bevor ein neuer Request an die API erfolgt (Performance)

v1.5.1
------
* Möglichkeit hinzugefügt, Ratenkaufzinsen im Backend automatisch aus Bestellungen und in Rechnungen zu entfernen
* Fehlermeldungen werden nicht mehr als Snippets ausgegeben

v1.5.0
------
* Anpassungen zur Kompatibilität mit Shopware 5.5 RC 1
* das Widget-Plugin wurde durch eine neue Version ersetzt (Entfernung von Bootstrap zur Reduzierung des Konfliktpotentials)
* die Fehlermeldung bei Ändern der Lieferadresse im Backend wird nun zuverlässig angezeigt
* bei Anpassung der Standard-Zahlungsmethode im Kundenaccount wird die Zustimmungserklärung nicht mehr angezeigt
* obsolete Funktionen wurden entfernt

v1.4.9
------
* das Widget kann nun, ohne Leeren des Caches, zuverlässig deaktiviert/aktiviert werden

v1.4.8
------
* Verbesserung der Kompatibilität mit aktuellen und zukünftigen Versionen von Shopware
* Verbessertes Handling von Zahlartenabschlägen in Verbindung mit dem ratenkauf by easyCredit
* Angleichung des Wordings zum easyCredit Händlerinterface

v1.4.7
------
* Anpassung von Links wegen Website Relaunch

v1.4.6
------
* Verbesserung der Kompatibilität mit aktuellen und zukünftigen Versionen von Shopware

v1.4.4
------
* behebt ein Problem, dass das Speichern von ratenkauf by easyCredit Bestellungen im Backend verhindert hat
* zuverlässigere Anzeige des Ratenkauf-Widgets durch Verwendung eines anderen Events

v1.4.3
------
* behebt fehlerhaftes Verhalten in bestimmten Umgebungen (Checkout zeigt weisse Seite, #3418)
* optimierte Darstellung der Zahlungsart (Payment Selection & Confirm-Seite)
* Anpassung zur Verwendung mit Custom Products Plugin (Produkte ohne Preis werden nicht an API gesendet)
* Code Cleanup: entfernt Verweise auf altes Emotion Template
* Widget wird auch bei deaktiviertem asynchronem JS-Loading angezeigt
* Performance-Optimierung Widget

v1.4.1
------
* #3408: Upgrade Anzeige in Shopware Marketplace ist für dieses Modul korrekt
* #3408: JS Fehler, wenn Modul als Letztes in Zahlungsarten-Auswahl
* doppelte Anzeige des Widgets in manchen Umgebungen
* Upgrade der API-Library
* behebt ein Fehlverhalten, wenn API Warning zurückliefert

v1.3.0
------
* Shopware 5.3.x Kompatibilität
* kein Support mehr für Shopware 4.x

v1.2.0
------
* Shopware 5.2.x Kompatibilität
* Rechtliche API-Übertragungsnachricht wird vom easyCredit Server dynamisch abgerufen
* easyCredit API v4

v1.1.0
------
* Kompatibilitättests

v1.0.0
------
* erstes öffentliches Release
