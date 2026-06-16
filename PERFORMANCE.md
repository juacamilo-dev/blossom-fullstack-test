# PERFORMANCE.md — CSV Import Performance Report

## Test Environment

| Component | Details |
|-----------|---------|
| Host OS | Windows 11 |
| Container | Docker Desktop (WSL2) |
| Database | MySQL 8.0 (Docker container) |
| PHP Version | 8.2 (Docker container) |
| Hardware | Intel/AMD processor, standard laptop |

---

## Test Results

### Import of 500,000 rows

| Metric | Result |
|--------|--------|
| Total rows inserted | 500,000 |
| Total time | 430.97 seconds (~7.2 minutes) |
| Average speed | 1,160 rows/second |
| Batch size | 1,000 rows per commit |
| CSV file size | ~65 MB (estimated) |

---

## Optimizations Applied

The following techniques were used to maximize import speed:

**1. Batch commits** — Instead of committing after every row, rows are committed in batches of 1,000. This reduces the number of disk writes dramatically.

**2. Disabled autocommit** — `SET autocommit=0` prevents MySQL from writing to disk after every single INSERT.

**3. Disabled unique checks** — `SET unique_checks=0` temporarily disables index validation during import, significantly speeding up inserts on tables with UNIQUE constraints like `traceNumber`.

**4. Disabled foreign key checks** — `SET foreign_key_checks=0` skips referential integrity checks during bulk load.

**5. PDO prepared statements** — The INSERT statement is prepared once and executed 500,000 times, avoiding repeated SQL parsing overhead.

---

## Limitations

**Docker overhead** — Running MySQL inside a Docker container on Windows adds significant I/O overhead compared to a native Linux installation. On a production Linux server, speeds of 5,000-10,000