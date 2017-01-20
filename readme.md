# battery.ebiene.de

Source Code von [battery.ebiene.de](battery.ebiene.de) Website und API.

`Battery Status` Progressive Web App für BMW i-Modelle ermittelt und zeigt Live-Informationen rund um den Fahrzeug-Akku. Die App bedient sich an der gleichen Schnittstelle, die auch von der deutschsprachigen BMW ConnectedDrive Website verwendet wird. Für die Nutzung der Schnittstelle wird ein Bearer-Token benötigt, den die Applikation von der BMW ConnectedDrive Website automatisch einholt.


### Warnung

Keine Garantie für Richtigkeit und Aktualität. Inbetriebnahme auf eigene Gefahr und Verantwortung. Implementierung ausschließlich zu Demozwecken.


### Voraussetzungen

* Apache-Webserver mit PHP
* BMW ConnectedDrive Zugangsdaten


### Installation

1. Datei `.htaccess` nach Wünschen anpassen.
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
