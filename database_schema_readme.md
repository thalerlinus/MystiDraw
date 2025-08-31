# Datenbankschema – MystiDraw (aus Migrations abgeleitet)

Stand: 2025-08-29
Quelle: Alle Dateien unter `database/migrations/`
Hinweis: Typen/Constraints gemäß Migrations (Laravel). Ziel-DB geht von MySQL/MariaDB aus.

---

## Übersicht
- Auth/Users: `users`, `password_reset_tokens`, `sessions`
- System: `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`
- Katalog: `categories`, `products`, `product_images`
- Raffles: `raffles`, `raffle_pricing_tiers`, `raffle_items`, `raffle_purchases`
- Bestellungen & Zahlungen: `orders`, `order_items`, `payments`, `invoice_counters`
- Tickets: `tickets`, `ticket_outcomes`
- Inventar: `user_items`, `user_inventory`
- Adressen & Versand: `addresses`, `order_addresses`, `shipments`, `shipment_items`
- Abos/Stripe: `subscriptions`, `subscription_items`
- Newsletter: `newsletter_subscriptions`

---

## Tabellen im Detail

### users
- id (PK, BIGINT)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, null)
- password (string)
- remember_token (string[100], null)
- is_admin (boolean, default false)
- cookie_consent (json, null)
- cookie_consent_updated_at (timestamp, null)
- newsletter_subscribed (boolean, default false)
- stripe_id (string, index, null)
- pm_type (string, null)
- pm_last_four (string[4], null)
- trial_ends_at (timestamp, null)
- created_at / updated_at (timestamps)

Beziehungen:
- 1:n zu `orders`, `addresses`, `shipments`, `payments` (nullable)

---

### password_reset_tokens
- email (string, PK)
- token (string)
- created_at (timestamp, null)

---

### sessions
- id (string, PK)
- user_id (FK -> users.id, null, index)
- ip_address (string[45], null)
- user_agent (text, null)
- payload (longText)
- last_activity (int, index)

---

### cache
- key (string, PK)
- value (mediumText)
- expiration (int)

### cache_locks
- key (string, PK)
- owner (string)
- expiration (int)

---

### jobs
- id (PK)
- queue (string, index)
- payload (longText)
- attempts (unsigned tinyint)
- reserved_at (unsigned int, null)
- available_at (unsigned int)
- created_at (unsigned int)

### job_batches
- id (string, PK)
- name (string)
- total_jobs, pending_jobs, failed_jobs (int)
- failed_job_ids (longText)
- options (mediumText, null)
- cancelled_at, created_at, finished_at (int, null)

### failed_jobs
- id (PK)
- uuid (string, unique)
- connection (text)
- queue (text)
- payload (longText)
- exception (longText)
- failed_at (timestamp, default current)

---

### categories
- id (PK)
- name (string)
- slug (string, unique)
- parent_id (FK -> categories.id, null, ON DELETE SET NULL)
- hero_image_path (string, null)
- hero_image_alt (string, null)
- thumbnail_path (string, null)
- created_at / updated_at

---

### products
- id (PK)
- sku (string, unique)
- name (string)
- description (text, null)
- base_cost (decimal(10,2), null)
- active (boolean, default true)
- default_tier (enum['A','B','C','D','E'], null)
- thumbnail_path (string, null)
- created_at / updated_at

### product_images
- id (PK)
- product_id (FK -> products.id, CASCADE)
- path (string)
- alt (string, null)
- sort_order (int, default 0)
- is_primary (boolean, default false)
- created_at / updated_at

---

### raffles
- id (PK)
- category_id (FK -> categories.id, CASCADE)
- name (string)
- slug (string, unique)
- status (enum['draft','scheduled','live','paused','sold_out','finished','archived'], default 'draft')
- starts_at, ends_at (dateTime, null)
- base_ticket_price (decimal(10,2))
- currency (char(3))
- public_stats (boolean, default false)
- tickets_total (int, default 0)
- tickets_sold (int, default 0)
- next_ticket_serial (unsignedBigInt, default 0)
- created_at / updated_at

### raffle_pricing_tiers
- id (PK)
- raffle_id (FK -> raffles.id, CASCADE)
- min_qty (int)
- unit_price (decimal(10,2))
- created_at / updated_at

### raffle_items
- id (PK)
- raffle_id (FK -> raffles.id, CASCADE)
- product_id (FK -> products.id, CASCADE)
- tier (enum['A','B','C','D','E'])
- quantity_total (int)
- quantity_awarded (int, default 0)
- weight (int, default 1)
- is_last_one (boolean, default false)
- created_at / updated_at

### raffle_purchases
- id (PK)
- raffle_id (FK -> raffles.id, CASCADE)
- user_id (FK -> users.id, CASCADE)
- quantity (unsigned int)
- unit_price (decimal(10,2))
- amount (decimal(10,2))
- currency (string(3))
- payment_intent_id (string, unique)
- status (string, default 'pending') // pending|succeeded|failed|expired
- created_at / updated_at
- Index: (raffle_id, status)

---

### orders
- id (PK)
- user_id (FK -> users.id, CASCADE)
- status (enum['pending','paid','failed','refunded','cancelled'], default 'pending')
- type (string(30), index, null) // z.B. 'raffle' | 'shipping'
- total (decimal(10,2))
- currency (char(3))
- provider_fee (decimal(10,2), default 0)
- paid_at (dateTime, null)
- meta (json, null)
- shipping_address_id (FK -> addresses.id, null, ON DELETE SET NULL)
- billing_address_id (FK -> addresses.id, null, ON DELETE SET NULL)
- created_at / updated_at

### order_items
- id (PK)
- order_id (FK -> orders.id, CASCADE)
- raffle_id (FK -> raffles.id, CASCADE)
- quantity (int)
- unit_price (decimal(10,2))
- subtotal (decimal(10,2))
- type (string(40), null)
- meta (json, null)
- created_at / updated_at

### payments
- id (PK)
- order_id (FK -> orders.id, null, CASCADE)
- user_id (FK -> users.id, null, ON DELETE SET NULL)
- provider (enum['stripe','paypal','manual','other'])
- provider_txn_id (string, null)
- invoice_number (string, unique, null)
- credit_note_number (string, unique, null)
- amount (decimal(10,2))
- currency (char(3))
- status (enum['pending','succeeded','failed','refunded'], default 'pending')
- paid_at (dateTime, null)
- email_sent_at (timestamp, null)
- refund_email_sent_at (timestamp, null)
- raw_response (json, null)
- success_email_sent (boolean, default false)
- created_at / updated_at

### invoice_counters
- id (PK)
- year (YEAR, unique)
- last_sequence (unsigned int, default 0)
- last_credit_sequence (unsigned int, default 0)
- created_at / updated_at

---

### tickets
- id (PK)
- raffle_id (FK -> raffles.id, CASCADE)
- user_id (FK -> users.id, CASCADE)
- order_id (FK -> orders.id, null, CASCADE)
- serial (unsignedBigInt, unique)
- price_paid (decimal(10,2))
- status (enum['created','paid','drawn','refunded','void'], default 'created')
- drawn_at (dateTime, null)
- created_at / updated_at

### ticket_outcomes
- id (PK)
- ticket_id (FK -> tickets.id, unique, CASCADE)
- raffle_item_id (FK -> raffle_items.id, null, ON DELETE SET NULL)
- product_id (FK -> products.id, null, ON DELETE SET NULL)
- tier (enum['A','B','C','D','E'])
- decided_by (enum['instant','batch'])
- rng_seed (string, null)
- rng_roll (decimal(10,6), null)
- status (enum['assigned','reserved_for_shipping','fulfilled'], default 'assigned')
- assigned_at (dateTime)
- fulfilled_at (dateTime, null)
- created_at / updated_at

---

### user_items
- id (PK)
- user_id (FK -> users.id, CASCADE)
- product_id (FK -> products.id, CASCADE)
- ticket_outcome_id (FK -> ticket_outcomes.id, unique, CASCADE)
- status (enum['owned','reserved_for_shipping','shipped'], default 'owned')
- owned_at (dateTime)
- shipped_at (dateTime, null)
- created_at / updated_at

### user_inventory
- id (PK)
- user_id (FK -> users.id, CASCADE)
- product_id (FK -> products.id, CASCADE)
- quantity (int)
- last_ticket_id (FK -> tickets.id, null, ON DELETE SET NULL)
- created_at / updated_at
- Unique: (user_id, product_id)

---

### addresses
- id (PK)
- user_id (FK -> users.id, CASCADE)
- label (string, null)
- first_name, last_name (string)
- company (string, null)
- street (string)
- house_number (string, null)
- address2 (string, null)
- postal_code (string)
- city (string)
- state (string, null)
- country_code (char(2))
- country (string, null)
- phone (string, null)
- is_default (boolean, default false)
- created_at / updated_at

### order_addresses
- id (PK)
- order_id (FK -> orders.id, CASCADE)
- type (enum['shipping','billing'])
- first_name, last_name (string)
- company (string, null)
- street (string)
- house_number (string, null)
- address2 (string, null)
- postal_code (string)
- city (string)
- state (string, null)
- country_code (char(2))
- country (string, null)
- phone (string, null)
- created_at / updated_at

### shipments
- id (PK)
- user_id (FK -> users.id, CASCADE)
- order_id (FK -> orders.id, null, ON DELETE SET NULL)
- order_address_id (FK -> order_addresses.id, CASCADE)
- status (enum['draft','queued','label_printed','shipped','delivered','returned'], default 'draft')
- carrier (string, null)
- service (string, null)
- tracking_number (string, null)
- tracking_url (string, null)
- weight_g (int, null)
- cost (decimal(10,2), null)
- currency (char(3), null)
- label_path (string, null)
- shipped_at (dateTime, null)
- delivered_at (dateTime, null)
- created_at / updated_at

### shipment_items
- id (PK)
- shipment_id (FK -> shipments.id, CASCADE)
- user_item_id (FK -> user_items.id, CASCADE)
- created_at / updated_at

---

### subscriptions
- id (PK)
- user_id (foreignId, index, KEIN FK in Migration gesetzt)
- type (string)
- stripe_id (string, unique)
- stripe_status (string, index mit user_id)
- stripe_price (string, null)
- quantity (int, null)
- trial_ends_at (timestamp, null)
- ends_at (timestamp, null)
- created_at / updated_at
- Index: (user_id, stripe_status)

### subscription_items
- id (PK)
- subscription_id (foreignId, index, KEIN FK in Migration gesetzt)
- stripe_id (string, unique)
- stripe_product (string)
- stripe_price (string)
- quantity (int, null)
- created_at / updated_at
- Index: (subscription_id, stripe_price)

---

### newsletter_subscriptions
- id (PK)
- user_id (FK -> users.id, unique, CASCADE)
- unsubscribe_token (string[64], unique)
- subscribed_at (timestamp, null)
- unsubscribed_at (timestamp, null)
- created_at / updated_at

---

## Relationen (Kurzüberblick)
- users 1:n orders, addresses, shipments, tickets, payments(?), raffle_purchases
- orders 1:n order_items, payments, shipments(?), 1:1..n order_addresses (über type)
- raffles 1:n raffle_items, raffle_pricing_tiers, order_items, tickets, raffle_purchases
- products 1:n product_images, raffle_items, user_items, user_inventory
- tickets 1:1 ticket_outcomes; tickets n:1 orders/users/raffles
- ticket_outcomes 1:1 user_items
- user_items n:1 shipments (über shipment_items Junction)
- shipments 1:n shipment_items

## Wichtige ENUMs
- raffles.status: draft|scheduled|live|paused|sold_out|finished|archived
- orders.status: pending|paid|failed|refunded|cancelled
- payments.provider: stripe|paypal|manual|other
- payments.status: pending|succeeded|failed|refunded
- tickets.status: created|paid|drawn|refunded|void
- ticket_outcomes.tier: A|B|C|D|E
- ticket_outcomes.decided_by: instant|batch
- ticket_outcomes.status: assigned|reserved_for_shipping|fulfilled
- user_items.status: owned|reserved_for_shipping|shipped
- order_addresses.type: shipping|billing
- shipments.status: draft|queued|label_printed|shipped|delivered|returned

## Indexe & Uniques (Auswahl)
- users.email (unique), users.stripe_id (index)
- products.sku (unique)
- categories.slug (unique)
- raffles.slug (unique)
- tickets.serial (unique)
- ticket_outcomes.ticket_id (unique)
- user_items.ticket_outcome_id (unique)
- user_inventory (user_id, product_id) (unique)
- newsletter_subscriptions.user_id (unique), .unsubscribe_token (unique)
- payments.invoice_number (unique), payments.credit_note_number (unique)
- raffle_purchases.payment_intent_id (unique), index (raffle_id, status)
- subscriptions: index (user_id, stripe_status); subscription_items: index (subscription_id, stripe_price)

## Hinweise
- Einige Spalten wurden nachträglich nullable gemacht (z. B. `tickets.order_id`, `payments.order_id`).
- `subscriptions`/`subscription_items` nutzen `foreignId`, aber die Migration setzt keinen FK-Constraint (nur Indexe).
- Währungen sind als `char(3)` hinterlegt (ISO-4217 erwartet).

