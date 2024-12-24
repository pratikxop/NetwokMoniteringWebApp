
CREATE DATABASE networkiocl;

USE networkiocl;

CREATE TABLE network (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255) NOT NULL,
    router_ip VARCHAR(15) NOT NULL,
    airtel_ip VARCHAR(15),
    airtel_bandwidth VARCHAR(10),
    airtel_status ENUM('Up', 'Down') DEFAULT 'Up',
    airtel_status_since DATETIME DEFAULT NULL,
    bsnl_ip VARCHAR(15),
    bsnl_bandwidth VARCHAR(10),
    bsnl_status ENUM('Up', 'Down') DEFAULT 'Up',
    bsnl_status_since DATETIME DEFAULT NULL,
    jio_ip VARCHAR(15),
    jio_bandwidth VARCHAR(10),
    jio_status ENUM('Up', 'Down') DEFAULT 'Up',
    jio_status_since DATETIME DEFAULT NULL,
    pgcil_ip VARCHAR(15),
    pgcil_bandwidth VARCHAR(10),
    pgcil_status ENUM('Up', 'Down') DEFAULT 'Up',
    pgcil_status_since DATETIME DEFAULT NULL,
    token VARCHAR(32) NOT NULL
);

CREATE TABLE downtime (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_provider VARCHAR(50) NOT NULL,
    downtime_duration VARCHAR(50) NOT NULL,
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
