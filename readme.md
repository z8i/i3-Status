# battery.ebiene.de

Source Code von [battery.ebiene.de](https://battery.ebiene.de) Website und API.

`Battery Status` Progressive Web App für BMW i-Modelle ermittelt und zeigt Live-Informationen rund um den Fahrzeug-Akku. Die App bedient sich an gleicher Schnittstelle, die auch von der deutschsprachigen BMW ConnectedDrive Website verwendet wird. Für die Nutzung der Schnittstelle wird ein Bearer-Token benötigt, den die App von der BMW ConnectedDrive Website automatisch einholt.

Die Webseite nach Einrichtung im Smartphone-Browser aufrufen und zum Homescreen hinzufügen. Ab diesem Zeitpunkt lässt sich die Web App vom Homescreen heraus im Vollbildmodus starten.


<p align="center">
    <img src="https://raw.githubusercontent.com/sergejmueller/battery.ebiene.de/master/img/screenshot-1.png" width="360" height="740" alt="Battery Statusanzeige">
    <br>
    Battery Statusanzeige
</p>


### Warnung

Keine Garantie für Richtigkeit und Aktualität. Inbetriebnahme auf eigene Gefahr und Verantwortung. Implementierung ausschließlich zu Demozwecken.


### Voraussetzungen

* Apache-Webserver mit PHP
* BMW ConnectedDrive Zugangsdaten


### Installation

1. Datei `.htaccess` nach Wünschen anpassen, insbesondere [Zeilen 20-21](https://github.com/sergejmueller/battery.ebiene.de/blob/master/.htaccess#L20-L21).
2. Datei `token.json` im Ordner `api/` beschreibbar anlegen.
3. Datei `auth.json` im Ordner `api/` mit BMW ConnectedDrive Zugangsdaten anlegen:

```json
{
    "username": "XYZ",
    "password": "XYZ",
    "vehicle": "XYZ"
}
```

| Feld       | Beschreibung                    |
| ---------- |:-------------------------------:|
| `username` | BMW ConnectedDrive Benutzername |
| `password` | BMW ConnectedDrive Passwort     |
| `vehicle`  | 17-stellige Fahrgestellnummer   |


### Sicherheit

Um Zugriffe auf sensible (JSON-)Dateien mit Zugangs- und Token-Daten zu unterbinden, *muss* in `.htaccess` folgender Code-Snippet aufgenommen werden (in der Installationsdatei `.htaccess` [bereits vorhanden](https://github.com/sergejmueller/battery.ebiene.de/blob/master/.htaccess#L33-L36)):

```apache
<FilesMatch "(^\.|\.(json|md)$)">
    order deny,allow
    deny from all
</FilesMatch>
```


### Datenausgabe

Nachfolgende Datenwerte zeigt die `Battery Status` Web App aktuell an:

* Charge Status (Prozent)
* Electric Range (Kilometer)
* Fully Charged (Reststunden)
* State of Charge (kWh)
* State of Charge Max (kWh)


### App-Icon

Von [Makeable](https://www.iconfinder.com/makea)
