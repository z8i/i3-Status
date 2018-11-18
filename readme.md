### Nächste Schritte
- Statistik statt in Textdatei in der Datenbank ablegen
- Benutzeranmeldung überarbeiten (optisch)
- Tabellen für Benutzeranmeldung automatisch anlegen bei Neueinrichtung

# i3 Status
Weiterentwicklung von battery.ebiene.de und i-Battery.

Initial Source Code von [battery.ebiene.de](https://battery.ebiene.de) Website und API.

Ursprünge im [goingelectric.de-Forum](https://www.goingelectric.de/forum/bmw-i3-laden/habe-mir-eine-webapp-fuer-den-batterie-status-gebaut-t21224-210.html)

Aktualisiert und weiterentwickelt von Endurance und anderen, siehe auch:
[Versteckte geheime aber wichtige Infos zum BMW i3](https://okedv.dyndns.org/wbb/blog/index.php?entry/46-versteckte-geheime-aber-wichtige-infos-zum-bmw-i3/)
[Connecteddrive WebApp fuer i3 und Plugins](https://okedv.dyndns.org/wbb/blog/index.php?entry/51-connecteddrive-webapp-fuer-i3-und-plugins/)
[WebApp fuer BMW i3-8 und Plugins](https://okedv.dyndns.org/wbb/blog/index.php?entry/56-webapp-fuer-bmw-i3-8-und-plugins/)


`Battery Status` Progressive Web App für BMW i-Modelle ermittelt und zeigt Live-Informationen rund um den Fahrzeug-Akku. Die App bedient sich an gleicher Schnittstelle, die auch von der deutschsprachigen BMW ConnectedDrive Website verwendet wird. Für die Nutzung der Schnittstelle wird ein Bearer-Token benötigt, den die App von der BMW ConnectedDrive Website automatisch einholt.

Die Webseite nach Einrichtung im Smartphone-Browser aufrufen und zum Homescreen hinzufügen. Ab diesem Zeitpunkt lässt sich die Web App vom Homescreen heraus im Vollbildmodus starten.

### Warnung

Keine Garantie für Richtigkeit und Aktualität. Inbetriebnahme auf eigene Gefahr und Verantwortung. Implementierung ausschließlich zu Demozwecken.

### Voraussetzungen

* Apache-Webserver mit PHP
* BMW ConnectedDrive Zugangsdaten

### Installation

1. Datei `.htaccess` nach Wünschen anpassen
2. Datei `token.json` im Ordner `api/access/` beschreibbar anlegen.
3. Datei `auth.json` im Ordner `api/access/` mit BMW ConnectedDrive Zugangsdaten anlegen:

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
4. Datei `db.php` im Ordner `api/access/` mit den DB-Zugangsdaten ausstatten
5. Initial-Benutzer von Hand in der DB anlegen, mit password_hash(z.B. von [hier](http://www.passwordtool.hu/php5-password-hash-generator))

### Benutzerabfrage
Die Benutzer der WebApp werden über ein login-Script abgefragt, welches eine Server-Session erstellt.
Wenn man nicht angemeldet ist, wird man automatisch auf die login.php weitergeleitet.
Um neue Benutzer anzulegen, kann die update.php verwendet werden, dort wird ein "Admin-Key" abgefragt, der in der db.php gesetzt werden muss.



### Sicherheit
Die Konfigurationsdateien haben jeweils `.htaccess`-Daten im Ordner, die anderen Seiten werden per Server-Session abgesichert.


### App-Icon

Von [Makeable](https://www.iconfinder.com/makea)
