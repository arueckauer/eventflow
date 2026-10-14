# Hauptsache schnell? KI-gestützte Entwicklung ohne Kontrollverlust
**Sebastian Bergmann**

> Die Softwareentwicklung steht 2026 an einem Wendepunkt. KI-gestützte Werkzeuge versprechen ein nie dagewesenes Tempo, doch ohne ein solides Fundament wird aus dieser Geschwindigkeit schnell Leichtsinn statt Fortschritt. In dieser praxisorientierten Schulung lernst du, wie du moderne Entwicklungspraktiken einschließlich KI-Agenten nutzt und dabei die Kontrollmechanismen behältst, die Qualität, architektonische Integrität und langfristige Tragfähigkeit sichern.
>
> Geschwindigkeit ohne Kontrolle führt zu technischen Schulden und fragilen Systemen, Kontrolle ohne Geschwindigkeit wird den Anforderungen des Marktes nicht gerecht. Der Weg nach vorn liegt in umfassenden automatisierten Sicherheitsnetzen, die Teams schneller arbeiten lassen, weil sie ihren Qualitätskontrollen vertrauen können. Du lernst Automatisierung und moderne Werkzeuge nicht als Ersatz für Disziplin kennen, sondern als Mittel, um höhere Velocity auf einem Fundament konsequenter Qualitätssicherung zu erreichen.
>
> Mit der Beispielanwendung in diesem Repository arbeitet [Sebastian Bergmann](https://phpunit.expert/de.html) in seiner Schulung „[Hauptsache schnell? KI-gestützte Entwicklung ohne Kontrollverlust](https://phpunit.expert/schulungen/hohe-velocity-ohne-kontrollverlust.html)“.

---

## EventFlow

Ein leichtgewichtiges Ticketing- und Sitzplatzreservierungssystem für Veranstaltungen.
Dieses Repository ist das Beispielprojekt der Schulung „[Hauptsache schnell? KI-gestützte Entwicklung ohne Kontrollverlust](https://phpunit.expert/schulungen/hohe-velocity-ohne-kontrollverlust.html)“.
Die Werkzeugkette ist bereits für dich eingerichtet.
In den Sitzungen baust du darauf die Domäne aus.

### Voraussetzungen

- PHP 8.5.7 oder neuer
- Die Erweiterung [Xdebug](https://xdebug.org/) oder [PCOV](https://github.com/krakjoe/pcov) (nötig, um Code Coverage zu erfassen)

Alle anderen Werkzeuge (PHPUnit, PHPStan, Deptrac, Infection, PHP-CS-Fixer, Rector, Composer) liegen in diesem Repository in `tools/`, es muss also nichts weiter heruntergeladen werden.

### Hausaufgabe vor der ersten Sitzung

Bitte erledige das, bevor die erste Sitzung beginnt:

Klone dieses Repository und lass das Quality Gate in deiner Arbeitskopie laufen:

```bash
git clone <URL dieses Repositorys> eventflow
cd eventflow
./tools/composer install
./tools/composer ci
```

`tools/deptrac`, `tools/rector` und `tools/phpunit.phar` sind symbolische Links.
Git unter Linux und macOS legt sie auch als solche an.
Unter Windows klonst du bitte in WSL: Git für Windows legt symbolische Links standardmäßig als einfache Textdateien an.

`composer ci` muss grün durchlaufen.
Wenn nicht, melde dich bitte vor der ersten Sitzung.

### Ausgangsstände der späteren Sitzungen

Nach jeder Sitzung wird der Ausgangsstand der nächsten als Branch dieses Repositorys veröffentlicht: `session-2`, `session-3` und `session-4`.
Jeder dieser Branches fügt einen Commit mit der Lösung der vorangegangenen Übung hinzu, `git diff main session-2` zeigt also die Lösung der ersten Übung.
Committe zuerst deine eigene Arbeit, dann wechsle:

```bash
git fetch
git switch session-2
```

### Das Quality Gate im Container

Wenn du PHP 8.5.7 nicht lokal installieren kannst, liegt ein `Containerfile` bei, mit dem du alles in einem Container mit Podman oder Docker ausführen kannst.
Das Image ist auf genau die PHP-Version festgelegt, mit der in der Schulung gearbeitet wird, und bringt die Xdebug-Erweiterung mit, die für die Code Coverage nötig ist.

Baue das Image einmal:

```bash
podman build -t eventflow-ci -f Containerfile .
```

Dann lass das komplette Quality Gate gegen deine Arbeitskopie laufen:

```bash
podman run --rm -v "$PWD":/app:Z eventflow-ci
```

Der Container erzeugt den Autoloader und führt `composer ci` für dich aus; ist der Lauf grün, bist du für die Schulung bereit.
Statt `podman` kannst du auch `docker` verwenden; auf Systemen ohne SELinux kannst du das `:Z` am Volume-Mount weglassen.

Statt `ci` lässt sich jedes andere Composer-Script ausführen, indem du seinen Namen auf der Kommandozeile übergibst:

```bash
podman run --rm -v "$PWD":/app:Z eventflow-ci refactoring
podman run --rm -v "$PWD":/app:Z eventflow-ci tests
```

Beachte, dass `refactoring` die Dateien in deiner eingebundenen Arbeitskopie verändert.

### Interaktive Werkzeug-Shell mit Docker Compose

Mit `compose.yaml` kannst du einen dauerhaften Container starten und darin eine Bash öffnen:

```bash
docker compose up -d --build php
docker compose exec --user "$(id -u):$(id -g)" php bash
```

Du startest im Projektverzeichnis `/app`. Die Benutzerzuordnung verhindert, dass die Werkzeuge neue Dateien mit Root-Eigentümer in deiner Arbeitskopie erzeugen. Die folgenden Aliase werden beim Image-Build eingerichtet und in jeder interaktiven Bash automatisch geladen:

| Alias | Kommando                                      | Zweck                     |
| ----- | --------------------------------------------- | ------------------------- |
| `s`   | `php tools/php-cs-fixer fix --dry-run --diff` | Coding Standard prüfen    |
| `f`   | `php tools/php-cs-fixer fix`                  | Formatierung korrigieren  |
| `a`   | `php tools/phpstan analyse`                   | Statische Analyse         |
| `d`   | `php tools/deptrac analyse --no-progress`     | Architektur prüfen        |
| `t`   | `php tools/phpunit --compact`                 | Tests ausführen           |
| `m`   | `php tools/infection`                         | Mutation Testing          |
| `r`   | `php tools/rector --dry-run`                  | Refactorings nur anzeigen |

Zusätzliche Argumente lassen sich anhängen, etwa `t --filter MoneyTest`. `f` verändert Quelldateien; `s` und `r` zeigen Änderungen nur an. Verwende `bash`, nicht `sh`, damit die Aliase geladen werden. Das Terminal ermöglicht die automatische Farberkennung der Werkzeuge.

Compose überspringt den Image-Entrypoint. Erzeuge deshalb vor der ersten Prüfung beziehungsweise nach Änderungen an den Composer-Abhängigkeiten den Autoloader:

```bash
php tools/composer install --no-interaction --no-progress
```

Das komplette Quality Gate führst du in dieser Shell weiterhin mit Composer aus:

```bash
php tools/composer --ansi run-script --timeout=0 ci
```

Mit `exit` verlässt du die Shell; der Container läuft weiter. Nach Änderungen an den Aliasen in `tools/container-aliases.bash` baust du das Image erneut mit `docker compose up -d --build php`.

### Das Quality Gate

`composer ci` führt alle sechs Feedback-Schleifen nacheinander aus.
Jede fängt eine andere Klasse von Fehlern, und jede läuft auch in der CI-Pipeline (`.github/workflows/ci.yaml`):

| Kommando                    | Werkzeug     | Findet                                                             |
|-----------------------------|--------------|--------------------------------------------------------------------|
| `composer coding-standard`  | PHP-CS-Fixer | Abweichungen von Formatierung und Coding Standard                  |
| `composer static-analysis`  | PHPStan      | Typfehler und unmöglichen Code (Level 10)                          |
| `composer architecture`     | Deptrac      | Verbotene Abhängigkeiten zwischen den Schichten                    |
| `composer tests`            | PHPUnit      | Falsches Verhalten                                                 |
| `composer mutation-testing` | Infection    | Tests, die zu schwach sind, um eine Verhaltensänderung zu bemerken |
| `composer rector-check`      | Rector       | Anwendbare Refactorings (Dry Run, ohne Dateien zu ändern)           |

Mit den Kommandos oben kannst du jede Stufe auch einzeln ausführen.

### Refactoring

`./tools/composer refactoring` schreibt den Code für dich um, zum Beispiel damit du moderne PHP-Syntax nutzt:
Es führt Rector für automatisierte Refactorings aus und danach PHP-CS-Fixer, um den Coding Standard des Ergebnisses herzustellen.
Prüfe die Änderungen, bevor du sie committest.

### Struktur

Der Code folgt einer Schichtung nach Domain-Driven Design.
Die Architekturregeln erzwingt Deptrac (siehe `deptrac.yaml`):
Die Schicht `Domain` ist das Herz des Systems und darf von keiner anderen Schicht abhängen.
Insbesondere darf sie nie auf `Infrastructure` zugreifen.

```
src/
├── Domain/                    # Entitäten, Value Objects, Aggregate, Domain Services
│   ├── SeatNumber             # Vollständig implementiertes Value Object als Vorlage
│   ├── DomainEvent            # Marker-Interface für Domain Events
│   ├── RecordsDomainEvents    # Trait für Aggregate, die Domain Events aufzeichnen
│   ├── PaymentGateway         # Port für die Abwicklung von Zahlungen
│   ├── PaymentResult          # Ergebnis eines Zahlungsversuchs
│   ├── Exception              # Marker-Interface für Ausnahmen der Domäne
│   └── InvalidSeatNumberException
├── Application/               # Use Cases, die die Domäne orchestrieren
└── Infrastructure/            # Adapter zur Außenwelt (Persistenz, Zahlung, ...)
    └── InMemoryPaymentGateway # In-Memory-Adapter für den Port PaymentGateway

tests/
├── Unit/                      # Schnelle, isolierte Tests für Domain und Application
├── Integration/               # Tests für die Adapter in Infrastructure
└── Architecture/              # Zusätzliche Architekturtests, falls vorhanden
```

### Das Value Object als Vorlage

`Thephpcc\EventFlow\Domain\SeatNumber` und sein Test `Thephpcc\EventFlow\Domain\SeatNumberTest` sind absichtlich schon für dich implementiert.
Sie sind die Vorlage für den Stil, in dem alles andere in diesem Projekt geschrieben wird:
unveränderliche Value Objects, benannte Konstruktoren, verhaltensorientierte Tests und 100 % Mutation Coverage.
Lies sie zuerst.

### Geldbeträge

`Money::from(1234, 'EUR')` erzeugt einen unveränderlichen Betrag von 1234 Cent.
`amount()` liefert die Cent als Integer, `currency()` das Value Object `Currency`.
`Currency::fromString()` unterstützt ausschließlich die Codes `EUR` und `GBP`;
andere Codes führen zu `UnknownCurrencyException`.

Negative Startbeträge führen zu `InvalidMoneyAmountException`. `add()` und
`subtract()` liefern neue Objekte, wobei negative Ergebnisse für Erstattungen
erlaubt sind. Unterschiedliche Währungen führen zu `CurrencyMismatchException`,
Integer-Überläufe zu `MoneyOverflowException`. `equals()` vergleicht Betrag und
Währung.
