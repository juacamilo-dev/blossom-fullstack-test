CREATE DATABASE IF NOT EXISTS blossom_transactions;
USE blossom_transactions;

CREATE TABLE IF NOT EXISTS transactions (
    transactionID INT AUTO_INCREMENT PRIMARY KEY,
    accountNumberFrom VARCHAR(20) NOT NULL,
    accountNumberTypeFrom VARCHAR(10) NOT NULL,
    accountNumberTo VARCHAR(20) NOT NULL,
    accountNumberTypeTo VARCHAR(10) NOT NULL,
    traceNumber VARCHAR(20) NOT NULL UNIQUE,
    amount DECIMAL(15,2) NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    description TEXT,
    creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    reference VARCHAR(100)
);