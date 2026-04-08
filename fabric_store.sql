-- Fabric Store Database Schema
-- Import this file via phpMyAdmin or run: mysql -u root -p < fabric_store.sql

CREATE DATABASE IF NOT EXISTS fabric_store;
USE fabric_store;

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    profile VARCHAR(255)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    price INT NOT NULL,
    image VARCHAR(255),
    product_detail VARCHAR(1000),
    status VARCHAR(20) DEFAULT 'active'
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20),
    name VARCHAR(100),
    number VARCHAR(20),
    email VARCHAR(100),
    address VARCHAR(255),
    address_type VARCHAR(20),
    method VARCHAR(50),
    product_id VARCHAR(20),
    price INT,
    qty INT,
    date DATE DEFAULT (CURRENT_DATE),
    status VARCHAR(50),
    payment_status VARCHAR(50)
);

-- Messages table
CREATE TABLE IF NOT EXISTS message (
    id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20),
    name VARCHAR(50),
    email VARCHAR(100),
    subject VARCHAR(255),
    message VARCHAR(500)
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20),
    product_id VARCHAR(20),
    price INT,
    qty INT DEFAULT 1
);

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20),
    product_id VARCHAR(20),
    price INT
);

-- Sample Data (optional)
-- INSERT INTO admin VALUES ('adm001', 'Store Admin', 'admin@fabricstore.com', SHA1('admin123'), 'default.jpg');