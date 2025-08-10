# 🎲 MystiDraw - Mystery-Box Plattform

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-12-1f2937?style=for-the-badge&logo=laravel&logoColor=white&labelColor=1e3a8a" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Vue.js-3-1f2937?style=for-the-badge&logo=vue.js&logoColor=white&labelColor=1e3a8a" alt="Vue.js 3">
  <img src="https://img.shields.io/badge/Tailwind-CSS-1f2937?style=for-the-badge&logo=tailwindcss&logoColor=white&labelColor=1e3a8a" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Design-Navy%20%26%20Gold-fbbf24?style=for-the-badge&labelColor=1e3a8a" alt="Design Colors">
</div>

---

## 🎯 Über MystiDraw

**MystiDraw** ist eine moderne Full-Stack-Webanwendung für den Online-Verkauf von Mystery-Box Losen mit **100% Gewinngarantie**. Käufer können Lose verschiedener Kategorien erwerben und gewinnen sofort - es gibt keine Nieten, nur Überraschungen!

### 🌟 Das Konzept

- **Sofortige Ziehung:** Nach dem Kauf erfährst du direkt deinen Gewinn
- **100% Gewinnchance:** Jedes Los ist ein Gewinn (Kategorien A-E)
- **Inventar-System:** Sammle Gewinne und erstelle später Versandpakete
- **Kostenloser Versand:** Aus Deutschland, 1-3 Werktage Lieferzeit
- **Vielfältige Kategorien:** Anime, Gaming, Lifestyle und mehr

---

## 🎨 Design & Branding

### Hauptfarben
```css
/* Navy-Blau Palette */
--navy-900: #0f172a  /* Hauptfarbe für UI-Elemente */
--navy-800: #1e293b  /* Hover-States */
--navy-700: #334155  /* Sekundäre Elemente */

/* Gold/Gelb Akzente */
--yellow-400: #fbbf24  /* Highlight-Farbe */
--yellow-500: #f59e0b  /* Button-Hover */

/* Hintergrund */
--white: #ffffff      /* Haupthintergrund */
--gray-50: #f9fafb    /* Sekundärer Hintergrund */
```

### Logo & Icons
- **Hauptlogo:** MystiDraw mit Mystery-Box Icon
- **Farben:** Navy-Blau (#1e3a8a) mit Gold-Akzenten (#fbbf24)
- **Stil:** Modern, vertrauenswürdig, einladend

---

## ⚡ Kernfunktionen

### 🎫 Mystery-Box System
- **Losverkauf** mit flexiblen Preisen & Staffelpreisen
- **Sofortige Ziehung** beim Kauf (keine Wartezeit)
- **Gewinn-Kategorien** A-E mit verschiedenen Werten
- **100% Gewinngarantie** - keine leeren Lose

### 📦 Inventar & Versand
- **Digitales Inventar** pro Benutzer
- **Paket-Erstellung** aus gesammelten Gewinnen
- **Kostenloser Versand** aus Deutschland
- **Tracking & Updates** für alle Sendungen

### 🗂️ Kategorien & Organization
- **Hierarchische Kategorien** (z.B. Anime → One Piece)
- **Visuelle Präsentation** mit Hero-Bildern
- **Kategoriebasierte Raffles** für zielgruppenspezifische Inhalte
- **Flexible Zuordnung** - Raffles können mehrere Kategorien haben

### 👤 Benutzerverwaltung
- **Registrierung & Login** mit Laravel Sanctum
- **Adressverwaltung** mit Versand-Snapshots
- **Bestellhistorie** und Gewinn-Tracking
- **Profilverwaltung** und Einstellungen

---

## 🛠️ Technologie-Stack

### Backend
- **Laravel 12** (PHP 8.4) - Robuste Business-Logik
- **MySQL** - Relationale Datenbank für komplexe Beziehungen
- **Redis** - Caching & Queue-System für Performance
- **Laravel Horizon** - Queue-Monitoring und Management
- **Laravel Sanctum** - API-Authentifizierung

### Frontend
- **Vue.js 3** - Moderne, reaktive Benutzeroberfläche
- **Tailwind CSS 3** - Utility-First Styling Framework
- **Inertia.js** - Nahtlose SPA-Integration
- **Vite** - Schnelles Asset-Bundling

### Development & Deployment
- **Laravel Sail** - Docker-basierte Entwicklungsumgebung
- **Composer** - PHP Dependency Management
- **NPM** - JavaScript Package Management

---

## 📊 Datenbank-Design

### Wichtige Tabellen
```
raffles
├── name, slug, status
├── starts_at, ends_at
├── base_ticket_price, currency
└── category_id (Hauptkategorie)

raffle_items
├── raffle_id, product_id
├── tier (A-E), quantity
└── probability_weight

tickets
├── user_id, raffle_id
├── purchase_price, status
└── outcome (gewonnenes Item)

user_inventory
├── user_id, product_id
├── quantity, source_ticket_id
└── status (available, shipped)
```

---

## 🎯 User Journey

### 1. 🏠 Startseite
- **Hero-Bereich:** Erklärung des Konzepts
- **Aktuelle Raffles:** Carousel mit verfügbaren Mystery-Boxes
- **Wie es funktioniert:** 3-Schritte Erklärung
- **Features:** Warum MystiDraw wählen

### 2. 🎲 Raffle-Auswahl
- **Kategorie-Filter:** Anime, Gaming, Lifestyle
- **Detailansicht:** Mögliche Gewinne, Preise, Verfügbarkeit
- **Live-Updates:** Verbleibende Lose, kürzliche Gewinne

### 3. 🛒 Kauf-Prozess
- **Los-Auswahl:** Anzahl der Lose
- **Sofortige Ziehung:** Gewinn wird direkt angezeigt
- **Inventar-Update:** Gewinn landet im digitalen Inventar

### 4. 📦 Inventar-Verwaltung
- **Gewinn-Übersicht:** Alle gesammelten Items
- **Paket-Erstellung:** Kombiniere Items für Versand
- **Versand-Tracking:** Status-Updates bis zur Lieferung

---

## 🔧 Admin-Funktionen

### Raffle-Management
- **Neue Raffles** erstellen und konfigurieren
- **Item-Pools** mit Tier-System (A-E) verwalten
- **Preise & Verfügbarkeit** in Echtzeit anpassen
- **Statistiken** und Performance-Tracking

### Kategorie-Verwaltung
- **Hierarchische Struktur** für organisierte Navigation
- **Hero-Bilder** für visuelles Marketing
- **Slug-Generierung** für SEO-optimierte URLs
- **Mehrfach-Zuordnungen** für flexible Kategorisierung

### Benutzer & Bestellungen
- **Benutzer-Übersicht** mit Aktivitäts-Historie
- **Versand-Management** mit Tracking-Integration
- **Support-Tools** für Kundenservice
- **Reporting** und Analytics

---

## 🚀 Installation & Setup

### Voraussetzungen
```bash
- PHP 8.4+
- Node.js 18+
- MySQL 8.0+
- Redis
- Composer
```

### Installation
```bash
# Repository klonen
git clone <repository-url>
cd MystiDraw

# Dependencies installieren
composer install
npm install

# Environment konfigurieren
cp .env.example .env
php artisan key:generate

# Datenbank setup
php artisan migrate --seed

# Assets kompilieren
npm run build

# Server starten
php artisan serve
```

---

## 🎨 UI/UX Design Prinzipien

### Farb-Psychologie
- **Navy-Blau:** Vertrauen, Professionalität, Stabilität
- **Gold/Gelb:** Aufregung, Optimismus, Premium-Gefühl
- **Weiß:** Klarheit, Sauberkeit, Fokus

### Benutzer-Erfahrung
- **Sofortige Belohnung:** Kein Warten auf Ziehungsergebnisse
- **Transparenz:** Klare Gewinnchancen und Kategorien
- **Gamification:** Sammeln, Kombinieren, Überraschen lassen
- **Trust-Building:** Sichere Zahlungen, zuverlässiger Versand

---

## 📈 Roadmap & Erweiterungen

### Phase 1 - MVP ✅
- [x] Grundlegendes Raffle-System
- [x] Benutzerregistrierung und -verwaltung
- [x] Basis Inventar-System
- [x] Admin-Dashboard

### Phase 2 - Enhancement 🚧
- [ ] Mobile App (React Native)
- [ ] Live-Benachrichtigungen
- [ ] Social Features (Teilen von Gewinnen)
- [ ] Erweiterte Analytics

### Phase 3 - Scale 🔮
- [ ] Multi-Sprachen Support
- [ ] Internationale Versand-Optionen
- [ ] Partner-Integration
- [ ] VIP/Premium System

---

## 🤝 Contributing

Contributions sind willkommen! Bitte beachte unsere Coding Standards:

- **PSR-12** für PHP Code
- **ESLint** Konfiguration für JavaScript
- **Conventional Commits** für Git Messages
- **Feature Branches** für alle Änderungen

---

## 📄 Lizenz

Dieses Projekt ist unter der MIT Lizenz veröffentlicht. Siehe `LICENSE` Datei für Details.

---

<div align="center">
  <h3>🎲 MystiDraw - Wo jedes Los ein Gewinn ist! 🏆</h3>
  <p><em>Gebaut mit ❤️ und Navy-Blau/Gold Design</em></p>
</div>