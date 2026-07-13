# GlowCommerce - E-Commerce Platform Backend

Welcome to the **GlowCommerce** repository. Below is the comprehensive system documentation, architecture diagrams, ERDs, API references, payment flows, and deployment guides required for your job assessment.

---

## 1. System Architecture

```mermaid
graph TD
    subgraph Client Tier [Frontend Client]
        Vercel[Vercel SPA Frontend]
    end

    subgraph Access Tier [Public Gateway]
        Ngrok[Ngrok Secure Tunnel]
    end

    subgraph Application Tier [Laravel Backend]
        Laravel[Laravel 11 App]
        Sanctum[Sanctum Authenticator]
        Strategy[Payment Strategy Resolver]
        StripeG[Stripe Gateway Client]
        BkashG[bKash Gateway Client]
    end

    subgraph Data Tier [Storage]
        MySQL[(MySQL Database)]
    end

    subgraph Third-Party APIs
        StripeAPI[Stripe REST API]
        BkashAPI[bKash Tokenized Checkout]
    end

    %% Routing
    Vercel -->|JSON API Calls| Ngrok
    Ngrok -->|Port 8000 Forwarding| Laravel
    Laravel --> Sanctum
    Laravel --> MySQL
    Laravel --> Strategy
    Strategy --> StripeG
    Strategy --> BkashG
    StripeG -->|HTTP Intents API| StripeAPI
    BkashG -->|HTTP Checkout API| BkashAPI
    StripeAPI -->|Webhook Status Events| Laravel
```

---

## 2. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        boolean is_admin
        timestamp created_at
    }
    CATEGORIES {
        bigint id PK
        string name
        bigint parent_id FK
        timestamp created_at
    }
    PRODUCTS {
        bigint id PK
        bigint category_id FK "nullable"
        string name
        string sku UK
        decimal price
        int stock
        string status "active/inactive"
        timestamp created_at
    }
    ORDERS {
        bigint id PK
        bigint user_id FK
        decimal total_amount
        string status "pending/paid/canceled"
        timestamp created_at
    }
    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        int quantity
        decimal price
        decimal subtotal
        timestamp created_at
    }
    PAYMENTS {
        bigint id PK
        bigint order_id FK
        string provider "stripe/bkash"
        string transaction_id UK
        decimal amount
        string status "pending/success/failed"
        json raw_response
        timestamp created_at
    }

    USERS ||--o{ ORDERS : places
    CATEGORIES ||--o{ CATEGORIES : nests
    CATEGORIES ||--o{ PRODUCTS : categorizes
    ORDERS ||--o{ ORDER_ITEMS : contains
    PRODUCTS ||--o{ ORDER_ITEMS : referenced_in
    ORDERS ||--o{ PAYMENTS : settles
```

---

## 3. Payment Flows

### 3.1 Stripe Integration Flow
```mermaid
sequenceDiagram
    autonumber
    actor Customer as User Browser
    participant App as Laravel Backend
    participant Stripe as Stripe API

    Customer->>App: POST /api/payments/checkout {order_id, provider: "stripe"}
    activate App
    App->>Stripe: HTTP POST /v1/payment_intents (amount, currency)
    Stripe-->>App: Return Client Secret & PaymentIntent ID (pi_xxxx)
    App->>App: Create database Payment record (status: pending, txn_id: pi_xxxx)
    App-->>Customer: Return Client Secret & Intent Details
    deactivate App

    Customer->>Customer: User processes payment (Simulated CC screen)
    Customer->>App: POST /api/payments/stripe/confirm {payment_intent_id}
    activate App
    App->>Stripe: HTTP POST /v1/payment_intents/pi_xxxx/confirm
    Stripe-->>App: Returns status: succeeded
    App->>App: DB Transaction: lockForUpdate() Order & Products
    App->>App: Verify stock level & Decrement stock level
    App->>App: Update Order (status: paid), Payment (status: success)
    App-->>Customer: Return order/payment details (succeeded)
    deactivate App

    Note over App, Stripe: Webhook Fallback Event Listener
    Stripe->>App: Webhook POST /api/payments/stripe/webhook {type: "payment_intent.succeeded"}
    App->>App: DB Transaction: locks Order & reduces stock (if not already processed)
    App-->>Stripe: 200 OK
```

### 3.2 bKash Integration Flow
```mermaid
sequenceDiagram
    autonumber
    actor Customer as User Browser
    participant App as Laravel Backend
    participant bkash as bKash API

    Customer->>App: POST /api/payments/checkout {order_id, provider: "bkash"}
    activate App
    App->>bkash: Get Grant Token (app_key, app_secret)
    bkash-->>App: ID Token
    App->>bkash: HTTP POST /checkout/create (amount, callbackURL)
    bkash-->>App: Return paymentID (TR00xxx) & checkoutUrl
    App->>App: Create database Payment record (status: pending, txn_id: TR00xxx)
    App-->>Customer: Return paymentID & checkoutUrl
    deactivate App

    Customer->>Customer: User views bKash payment portal
    Customer->>App: POST /api/payments/bkash/execute {payment_id}
    activate App
    App->>bkash: Get Grant Token
    bkash-->>App: ID Token
    App->>bkash: HTTP POST /checkout/execute {paymentID}
    bkash-->>App: Return transactionStatus: "Completed"
    App->>App: DB Transaction: lockForUpdate() Order & Products
    App->>App: Verify stock level & Decrement stock level
    App->>App: Update Order (status: paid), Payment (status: success)
    App-->>Customer: Return success callback details
    deactivate App
```

---

## 4. API Endpoints Documentation

All requests should carry headers:
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer <your_access_token> (Required for protected routes)
```

| Method | Endpoint | Protection | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/register` | Public | Registers a new account. Returns Bearer token. |
| `POST` | `/api/login` | Public | Logs in a user. Returns Bearer token. |
| `POST` | `/api/user/toggle-admin` | Auth | Developer helper to switch user role (Customer <-> Admin). |
| `GET` | `/api/products` | Public | Lists all active products. Admins see active & inactive. |
| `GET` | `/api/products/{id}` | Public | Details of a single product. Inactive is restricted to admin. |
| `POST` | `/api/products` | Admin | Create a new product. SKU must be unique. |
| `PUT` | `/api/products/{id}` | Admin | Edit an existing product. |
| `DELETE` | `/api/products/{id}` | Admin | Delete a product. |
| `POST` | `/api/orders` | Auth | Place an order. Calculates subtotals/totals deterministically. |
| `GET` | `/api/orders/{id}` | Auth | View order details. Enforces ownership check. |
| `GET` | `/api/orders` | Auth | Lists the user's placed orders (paginated). |
| `GET` | `/api/payments` | Auth | Lists user's ledger of payment transactions. |
| `POST` | `/api/payments/checkout` | Auth | Initiates provider checkouts (Stripe/bKash). |
| `POST` | `/api/payments/stripe/confirm` | Auth | Confirms Stripe payment & runs safe stock reduction. |
| `POST` | `/api/payments/stripe/webhook` | Public | Stripe status update webhook handler. |
| `POST` | `/api/payments/bkash/execute` | Auth | Executes bKash checkout & runs safe stock reduction. |
| `GET` | `/api/payments/bkash/query/{id}` | Auth | Queries status of bKash transaction. |

---

## 5. Deployment Guide

### 5.1 Local Installation
1. Clone the repository and navigate into it:
   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   ```
2. Configure `.env` file settings:
   - Database credentials (`DB_DATABASE=ecommerce`, `DB_USERNAME=root`, `DB_PASSWORD=`).
   - Payment credentials (keys for Stripe / bKash).
3. Run migrations and database seeders:
   ```bash
   php artisan migrate --seed
   ```
   *(Creates: admin account: `admin@example.com`, customer: `customer@example.com` - passwords are `secret123`)*.
4. Launch local dev server:
   ```bash
   php artisan serve
   ```
5. Visit `http://127.0.0.1:8000` in your web browser.

