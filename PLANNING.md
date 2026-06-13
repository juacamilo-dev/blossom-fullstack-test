# PLANNING.md — Transaction Management System
**Blossom Full Stack Test**
**Developer:** Juan Camilo Rodriguez
**Repository:** https://github.com/juacamilo-dev/blossom-fullstack-test
**Start Date:** June 12, 2025
**Deadline:** June 19, 2025

---

## Project Overview

A Transaction Management System built with a PHP REST API backend, React frontend, MySQL database, and Docker containerization. The system allows creating, listing, filtering, and deleting financial transactions.

---

## Architecture

blossom-fullstack-test/

├── backend/          # PHP REST API

├── frontend/         # React application

├── docker/           # Docker configuration files

├── docker-compose.yml

├── PLANNING.md

├── PERFORMANCE.md

└── README.md

---

## Task Breakdown

### Task 1 — Project Setup & Repository
**Estimated time:** 2 hours
**Status:** In progress

- [x] Create GitHub repository
- [x] Create PLANNING.md
- [ ] Define folder structure
- [ ] Initial commit

**Dependencies:** None

---

### Task 2 — Docker Setup
**Estimated time:** 6 hours
**Status:** Pending

- [ ] Create Dockerfile for PHP backend
- [ ] Create Dockerfile for React frontend
- [ ] Create MySQL container configuration
- [ ] Create docker-compose.yml with 3 containers (backend, frontend, database)
- [ ] Configure Nginx as web server
- [ ] Test full Docker setup end to end
- [ ] Document setup instructions in README.md

**Dependencies:** Task 1
**Resources:** Docker documentation, docker-compose reference

---

### Task 3 — MySQL Database Setup
**Estimated time:** 3 hours
**Status:** Pending

- [ ] Design transactions table schema
- [ ] Define columns: transactionID, accountNumberFrom, accountNumberTypeFrom, accountNumberTo, accountNumberTypeTo, traceNumber, amount, creationDate, reference
- [ ] Implement unique alphanumeric traceNumber generation
- [ ] Create SQL migration file
- [ ] Test database connection from PHP

**Dependencies:** Task 2
**Resources:** MySQL documentation

---

### Task 4 — PHP REST API Backend
**Estimated time:** 12 hours
**Status:** Pending

- [ ] Setup PHP project structure
- [ ] Configure database connection with PDO
- [ ] Implement POST /transactions endpoint
- [ ] Implement GET /transactions endpoint with pagination and filtering (date range, type)
- [ ] Implement DELETE /transactions/{id} endpoint
- [ ] Apply input validation on all endpoints
- [ ] Implement proper HTTP status codes
- [ ] Implement error handling
- [ ] Test all endpoints

**Dependencies:** Task 3
**Resources:** PHP PDO documentation, REST API best practices

---

### Task 5 — React Frontend
**Estimated time:** 12 hours
**Status:** Pending

- [ ] Initialize React project with Vite
- [ ] Setup project folder structure (components, pages, services)
- [ ] Create API service layer for backend communication
- [ ] Build transaction dashboard with list view
- [ ] Implement sorting and filtering options (date range, type)
- [ ] Build form to create new transactions
- [ ] Implement delete transaction functionality
- [ ] Make UI responsive and user-friendly
- [ ] Connect all components to backend API

**Dependencies:** Task 4
**Resources:** React documentation, Vite documentation

---

### Task 6 — Performance Optimization
**Estimated time:** 6 hours
**Status:** Pending

- [ ] Create transactions.csv file with test data
- [ ] Build CSV import script in PHP
- [ ] Run import of 500,000 to 1,000,000 rows
- [ ] Measure import performance and query times
- [ ] Document results and limitations in PERFORMANCE.md

**Dependencies:** Task 3, Task 4
**Resources:** MySQL bulk insert documentation

---

### Task 7 — Code Review
**Estimated time:** 3 hours
**Status:** Pending

- [ ] Review txnExportService.php file
- [ ] Identify code quality issues
- [ ] Suggest improvements and best practices
- [ ] Document findings in CODE_REVIEW.md

**Dependencies:** Task 4
**Resources:** PHP best practices documentation

---

### Task 8 — Documentation & Final Delivery
**Estimated time:** 4 hours
**Status:** Pending

- [ ] Write README.md with full setup instructions
- [ ] Document all API endpoints
- [ ] Finalize PERFORMANCE.md
- [ ] Finalize CODE_REVIEW.md
- [ ] Final testing of complete system
- [ ] Clean up code and comments
- [ ] Final commit and delivery

**Dependencies:** All tasks
**Resources:** Markdown documentation guide

---

## Timeline

| Day | Date | Tasks |
|-----|------|-------|
| Day 1 | June 12-13 | Task 1 (Setup), Task 2 (Docker basics) |
| Day 2 | June 13-14 | Task 3 (Database), Task 4 (PHP API) |
| Day 3 | June 14-15 | Task 4 (PHP API continued) |
| Day 4 | June 15-16 | Task 5 (React Frontend) |
| Day 5 | June 16-17 | Task 5 (React continued), Task 6 (Performance) |
| Day 6 | June 17-18 | Task 7 (Code Review), Task 8 (Documentation) |
| Day 7 | June 18-19 | Final testing, fixes and delivery |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | React 18, Vite, CSS3 |
| Backend | PHP 8.2, PDO |
| Database | MySQL 8.0 |
| Web Server | Nginx |
| Containerization | Docker, Docker Compose |
| Version Control | Git, GitHub |

---

## Known Limitations & Risks

- PHP and Docker are new technologies for the developer, learning curve expected
- Unit tests (PHPUnit) may not be fully implemented due to time constraints
- Performance optimization at 1M rows may have limitations documented in PERFORMANCE.md
- Laravel/Symfony framework not used; plain PHP with PDO instead

---

## Notes

This project is being developed by a junior developer using modern AI-assisted development tools (Cursor + Claude Code) following professional vibe coding practices: every AI-generated code block is reviewed and understood before committing.