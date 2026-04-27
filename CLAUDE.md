# WMS Gestionale Magazzino - Contesto Progetto

## Overview
WMS (Warehouse Management System) SaaS multi-tenant per PMI italiane.
Ogni azienda cliente ha il proprio sottodominio: `azienda1.gestionale.it`

## Stack Tecnico
- **Backend**: Laravel 11, PHP 8.3+
- **Database**: MySQL 8
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **PDF**: barryvdh/laravel-dompdf
- **Excel**: maatwebsite/excel
- **Permessi**: spatie/laravel-permission
- **Auth**: Laravel Breeze (Blade stack)

## Architettura Multi-Tenant
- Identificazione tenant via subdomain (middleware `IdentifyTenant`)
- Ogni tabella ha `company_id` con global scope automatico
- Il tenant corrente è disponibile via `app('currentCompany')` o `auth()->user()->company`
- Isolamento dati garantito a livello di query

## Ruoli Utente
- `admin` - Accesso completo, gestione utenti e impostazioni
- `manager` - Responsabile: tutto tranne impostazioni avanzate
- `warehouse` - Magazziniere: operazioni magazzino
- `readonly` - Solo lettura di tutti i dati

## Struttura Directory Chiave
```
app/
  Http/
    Controllers/         # Controller per ogni feature
    Middleware/          # IdentifyTenant, CheckRole
    Requests/            # Form Requests con validazione italiana
  Models/               # Tutti i modelli con relazioni e global scope
  Policies/             # Policy per autorizzazione per ruolo
  Jobs/                 # CheckStockAlerts, SendExpiryNotifications
  Console/Commands/     # Comandi artisan custom
  Exports/              # Classi export Excel
  Imports/              # Classi import Excel
  Services/             # Business logic (MovementService, StockService)
resources/
  views/
    layouts/            # Layout principale con sidebar
    components/         # Componenti Blade riutilizzabili
    auth/               # Login, registro
    dashboard/          # Dashboard KPI
    products/           # CRUD prodotti
    warehouses/         # Mappa magazzino
    movements/          # Movimentazioni
    purchase-orders/    # Ordini di acquisto
    sales-orders/       # Ordini di vendita
    picking/            # Picking list
    inventory/          # Sessioni inventario
    reports/            # Report e statistiche
    settings/           # Impostazioni azienda
```

## Convenzioni
- Lingua italiana per UI e messaggi di validazione
- Soft delete su tutte le entità principali
- Audit trail via colonne `created_by_user_id` + timestamp
- No N+1: usare sempre eager loading con `with()`
- Indici su tutte le FK e colonne di ricerca

## Scelte Implementative
- Multi-tenant tramite subdomain (non DB separate): più semplice da gestire
- Permessi tramite enum `role` su users (non spatie roles per semplicità)
- FIFO automatico suggerito nel picking (non forzato, operatore può cambiare)
- Alert email via Laravel Queue (database driver in dev, redis in prod)
- PDF generati on-demand con dompdf
- Import Excel asincrono per file grandi

## Comandi Utili
```bash
php artisan migrate:fresh --seed  # Reset DB con dati demo
php artisan queue:work            # Avvia worker code
php artisan wms:check-alerts      # Check manuale alert scorte
php artisan tinker                # Debug interattivo
npm run dev                       # Frontend dev server
npm run build                     # Build production assets
```

## Variabili .env Importanti
```
APP_URL=http://localhost
TENANT_DOMAIN=gestionale.it      # Dominio base per tenant
MAIL_ALERT_FROM=noreply@gestionale.it
STOCK_ALERT_DAYS_BEFORE_EXPIRY=30  # Giorni prima scadenza per alert
```
