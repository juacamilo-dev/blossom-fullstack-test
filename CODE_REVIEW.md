# CODE_REVIEW.md — Code Review Report

**Reviewer:** Juan Camilo Rodriguez  
**Date:** June 2026  
**File reviewed:** backend/routes/transactions.php  

---

## Overview

This review analyzes the current implementation of the transactions API backend,
identifying areas for improvement in code quality, security, maintainability,
and performance.

---

## Issues Found

### 1. Missing input sanitization on string fields
**Severity:** High  
**Location:** `transactions.php` — POST case

**Current code:**
```php
':accountNumberFrom' => $body['accountNumberFrom'],
```

**Problem:** String fields are inserted directly without sanitizing whitespace
or special characters. A user could submit leading/trailing spaces or
unexpected characters.

**Recommendation:**
```php
':accountNumberFrom' => trim($body['accountNumberFrom']),
```
Apply `trim()` to all string inputs before inserting into the database.

---

### 2. No maximum length validation on string fields
**Severity:** Medium  
**Location:** `transactions.php` — POST case

**Problem:** The database defines `accountNumberFrom` as `VARCHAR(20)` but the
API does not validate that the submitted value is within that limit. A value
longer than 20 characters will cause a database error instead of a clean
validation message.

**Recommendation:**
```php
if (strlen($body['accountNumberFrom']) > 20) {
    http_response_code(400);
    echo json_encode(["error" => "accountNumberFrom must not exceed 20 characters"]);
    exit();
}
```

---

### 3. Duplicate traceNumber generation could loop indefinitely
**Severity:** Medium  
**Location:** `transactions.php` — `generateTraceNumber()` and POST case

**Current code:**
```php
do {
    $traceNumber = generateTraceNumber();
    $check = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE traceNumber = ?");
    $check->execute([$traceNumber]);
} while ($check->fetchColumn() > 0);
```

**Problem:** In theory this loop could run indefinitely if the traceNumber
space becomes exhausted, though extremely unlikely with 12 alphanumeric
characters (36^12 combinations).

**Recommendation:** Add a maximum retry counter:
```php
$maxRetries = 10;
$retries = 0;
do {
    if ($retries++ >= $maxRetries) {
        http_response_code(500);
        echo json_encode(["error" => "Could not generate unique trace number"]);
        exit();
    }
    $traceNumber = generateTraceNumber();
    $check = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE traceNumber = ?");
    $check->execute([$traceNumber]);
} while ($check->fetchColumn() > 0);
```

---

### 4. config.php headers sent on CLI execution
**Severity:** Low  
**Location:** `config.php` — lines 1-8

**Problem:** The `header()` calls in `config.php` are designed for HTTP
responses but cause warnings when the file is included from CLI scripts
like `import.php`. This generates noise in the import output.

**Recommendation:** Wrap headers in an HTTP context check:
```php
if (php_sapi_name() !== 'cli') {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    // ... rest of headers
}
```

---

### 5. No authentication or authorization
**Severity:** High  
**Location:** All endpoints

**Problem:** All API endpoints are publicly accessible without any
authentication. Any user who knows the API URL can create or delete
transactions.

**Recommendation:** Implement JWT authentication middleware:
```php
function validateToken() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    // validate JWT token
    // return 401 if invalid
}
```

---

### 6. Error messages expose internal details
**Severity:** Medium  
**Location:** `config.php` — catch block

**Current code:**
```php
echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
```

**Problem:** Exposing the raw exception message in production reveals internal
database configuration details to potential attackers.

**Recommendation:** Log the detailed error internally and return a generic
message to the client:
```php
error_log($e->getMessage());
echo json_encode(["error" => "Internal server error. Please try again later."]);
```

---

## Positive Aspects

- PDO prepared statements are used correctly, preventing SQL injection attacks.
- HTTP status codes are applied consistently (200, 201, 400, 404, 405).
- Pagination is implemented with both total count and page metadata.
- Dynamic query building for filters is clean and readable.
- The `traceNumber` uniqueness is verified before insert, not relying solely
  on the database UNIQUE constraint to handle collisions.

---

## Summary

| Severity | Count |
|----------|-------|
| High | 2 |
| Medium | 3 |
| Low | 1 |
| **Total** | **6** |

The codebase is functional and demonstrates solid understanding of REST API
design and SQL security fundamentals. The main areas for improvement are
input validation hardening, authentication, and separating CLI from HTTP
execution contexts.