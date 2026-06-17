# Transaction Management System
**Blossom Full Stack Test**

A full-stack Transaction Management System built with PHP REST API, React frontend, MySQL database, and Docker containerization. The system includes unit tests with PHPUnit and was performance tested with 800,000 rows.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | React 18, Vite, CSS3 |
| Backend | PHP 8.2, PHP-FPM, PDO |
| Database | MySQL 8.0 |
| Web Server | Nginx |
| Containerization | Docker, Docker Compose (4 containers) |
| Testing | PHPUnit 11 (9 tests, 34 assertions) |

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- [Git](https://git-scm.com/) installed

---

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/juacamilo-dev/blossom-fullstack-test.git
cd blossom-fullstack-test
```

### 2. Start the application

```bash
docker-compose up --build
```

This single command will:
- Download all required Docker images
- Build the PHP-FPM backend, Nginx web server and React frontend containers
- Initialize the MySQL database with the transactions table
- Start all four containers connected on the same network

### 3. Access the application

| Service | URL |
|---------|-----|
| Frontend (React) | http://localhost:3000 |
| Backend API (Nginx + PHP-FPM) | http://localhost:8000 |
| Database (MySQL) | localhost:3306 |

---

## Running Unit Tests

```bash
# First install PHP dependencies
docker exec blossom_backend composer install

# Then run the test suite
docker exec blossom_backend ./vendor/bin/phpunit
```

Expected output:
```
PHPUnit 11.5.55 by Sebastian Bergmann and contributors.
.........                         9 / 9 (100%)
OK (9 tests, 34 assertions)
```

## API Endpoints

### GET /transactions
Retrieve all transactions with optional filtering and pagination.

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| type | string | Filter by `credit` or `debit` |
| date_from | string | Filter from date (YYYY-MM-DD) |
| date_to | string | Filter to date (YYYY-MM-DD) |
| page | integer | Page number (default: 1) |
| limit | integer | Results per page (default: 10) |

**Example:**
GET /transactions?type=credit&page=1&limit=10

**Response:**
```json
{
  "data": [...],
  "pagination": {
    "total": 100,
    "page": 1,
    "limit": 10,
    "total_pages": 10
  }
}
```

---

### POST /transactions
Create a new transaction.

**Request Body:**
```json
{
  "accountNumberFrom": "1234567890",
  "accountNumberTypeFrom": "savings",
  "accountNumberTo": "0987654321",
  "accountNumberTypeTo": "checking",
  "amount": 1500.00,
  "type": "credit",
  "description": "Payment description",
  "reference": "REF001"
}
```

**Response (201):**
```json
{
  "message": "Transaction created successfully",
  "data": {
    "transactionID": 1,
    "traceNumber": "A3BX92KL04MN",
    ...
  }
}
```

---

### DELETE /transactions/{id}
Delete a transaction by ID.

**Example:**
DELETE /transactions/1

**Response (200):**
```json
{
  "message": "Transaction deleted successfully",
  "data": {...}
}
```

---

## Database Schema

```sql
CREATE TABLE transactions (
    transactionID        INT AUTO_INCREMENT PRIMARY KEY,
    accountNumberFrom    VARCHAR(20) NOT NULL,
    accountNumberTypeFrom VARCHAR(10) NOT NULL,
    accountNumberTo      VARCHAR(20) NOT NULL,
    accountNumberTypeTo  VARCHAR(10) NOT NULL,
    traceNumber          VARCHAR(20) NOT NULL UNIQUE,
    amount               DECIMAL(15,2) NOT NULL,
    type                 ENUM('credit', 'debit') NOT NULL,
    description          TEXT,
    creationDate         DATETIME DEFAULT CURRENT_TIMESTAMP,
    reference            VARCHAR(100)
);
```

---

## Project Structure
blossom-fullstack-test/

├── backend/

│   ├── database/

│   │   └── init.sql              # Database schema

│   ├── routes/

│   │   └── transactions.php      # API endpoints

│   ├── tests/

│   │   └── TransactionApiTest.php # PHPUnit tests

│   ├── config.php                 # Database connection

│   ├── index.php                  # Entry point / Router

│   ├── import.php                 # CSV bulk import script

│   ├── generate_csv.php           # CSV test data generator

│   └── phpunit.xml                # PHPUnit configuration

├── docker/

│   ├── frontend/

│   │   └── Dockerfile

│   ├── nginx/

│   │   └── default.conf           # Nginx configuration

│   └── php/

│       └── Dockerfile

├── frontend/

│   ├── src/

│   │   ├── components/            # React components

│   │   ├── pages/                 # Page components

│   │   ├── services/              # API communication layer

│   │   └── hooks/                 # Custom React hooks

│   └── vite.config.js

├── docker-compose.yml

├── PLANNING.md

├── PERFORMANCE.md

├── CODE_REVIEW.md

└── README.md
---

## Stopping the Application

```bash
docker-compose down
```

To stop and remove all data (including database):

```bash
docker-compose down -v
```

---

## Performance

The system was tested with a bulk import of 800,000 rows:

| Metric | Result |
|--------|--------|
| Total rows imported | 800,000 |
| Total time | 703.32 seconds |
| Average speed | 1,137 rows/second |

Full details in [PERFORMANCE.md](PERFORMANCE.md).

---

## Known Limitations

- Backend built with plain PHP and PDO instead of Laravel/Symfony framework
- Unit tests cover API endpoints via HTTP requests (integration-style with PHPUnit)
- Performance optimization at scale documented in PERFORMANCE.md
- Code review performed on backend code as txnExportService.php was not provided by Blossom