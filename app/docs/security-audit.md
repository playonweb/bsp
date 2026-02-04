# Security Audit Report

**Date:** 2026-04-01
**Target:** PHP Registry Application  

## 1. Executive Summary

A security audit was performed on the `app/` codebase. The application is a simple PHP-based user registry with an API and a frontend. 

**Critical** vulnerabilities were identified, primarily related to **SQL Injection** risks due to the use of insecure escaping mechanisms (`addslashes`) instead of prepared statements. Additionally, **Private Information (PII) Exposure** is a high risk as the application allows unauthenticated users to reveal full contact details via a simple query parameter.

Immediate action is recommended to migrate database interactions to **PDO Prepared Statements** and to implement proper **Authentication** for accessing sensitive data.

## 2. Vulnerability Findings

| ID | Title | Severity | Location | Description |
|----|-------|----------|----------|-------------|
| **VULN-01** | SQL Injection via Insecure Escaping | **Critical** | `app/core/base_model.php` | The application uses `addslashes` to escape user input. This is known to be insecure and can be bypassed in certain configurations. No prepared statements are used. |
| **VULN-02** | Unauthenticated PII Exposure | **High** | `app/controllers/api/v1/users.php` | The endpoint `/api/v1/users/:id?reveal=true` exposes full email and phone numbers without authentication. This allows scrapers to harvest user data easily. |
| **VULN-03** | Information Disclosure (Debug Mode) | **Medium** | `app/core/database.php` | The `handleError` method calls `die()` with the full query and stack trace. If triggered in production, this leaks database structure and code paths to attackers. |
| **VULN-04** | Lack of CSRF Protection | **Medium** | `app/views/index.php` | Forms do not include Anti-CSRF tokens. While the impact is lower for a public registry, it allows attackers to forge requests to add users. |
| **VULN-05** | Double Encoding of Data | **Low** | `app/models/user.php` | Input is HTML-entity encoded before storage (`BaseModel::escapeValue`) and again on output (`User::formatRows`). This can lead to data integrity issues. |

## 3. Detailed Analysis

### VULN-01: SQL Injection
The `BaseModel::escapeValue` method uses `htmlentities(addslashes($value))`. This does not properly secure the application against SQL injection, especially if the database connection charset is not strictly controlled or if edge-case inputs are used.

**Recommendation:** Migrate all database queries to use `mysqli` prepared statements or `PDO`.

### VULN-02: PII Exposure
The `reveal=true` parameter acts as a bypass for the obfuscation logic. Since there is no login system or API key requirement, any user can iterate through generic numeric IDs (1, 2, 3...) and collect a database of valid emails and phone numbers.

**Current dirty solution:** Implement rate-limiting by host IP in `router.php`.

**Recommendation:** Additional captcha checks.

### VULN-03: Debug Information Leakage
Line 49 of `app/core/database.php` explicitly prints the stack trace:
```php
die("(" . $this->mysqli->errno . ") " . ... print_r(debug_backtrace(),true) ...);
```

**Recommendation:** Log errors to a file (`error_log`) and show a generic "Internal Server Error" message to the user.

## 4. Remediation Plan

1.  **Immediate Fixes (High Priority):**
    *   Disable debug output in `app/core/database.php`.
    *   Implement **Prepared Statements** in `BaseModel` or at least switch to `mysqli_real_escape_string` universally (though Prepared Statements are the only true fix).
2.  **Strategic Improvements:**
    *   Protect the `reveal` endpoint with stricter rate limiting (e.g., 5 requests per minute per IP) or CAPTCHA.
    *   Add CSRF tokens to the submission form.

## 5. Security Posture Score

**Current Score: D**
The application functions but relies on outdated and insecure practices (string concatenation for SQL) that pose significant risks in a hostile environment (public internet).
