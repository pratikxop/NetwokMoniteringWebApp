# Network Monitoring Web App


A web-based system designed to monitor the real-time status of Internet Service Providers (ISPs) such as Airtel, BSNL, Jio, and PGCL. This app stores network details, tracks downtime history, and provides actionable insights for network administrators.


---


## 🚀 Features


- **Track ISP Status**: Monitor the health of Airtel, BSNL, Jio, and PGCL networks in real time.
- **Database Storage**: Log network IPs, bandwidth usage, status updates, and downtime durations.
- **Downtime Logging**: Record outages with precise timestamps for analysis.
- **Real-time Monitoring**: Assess uptime and failures dynamically.
- **Secure API Access**: Authentication tokens ensure secure data access.


---


## 🛠️ Database Setup


1. **Create Database**
   ```sql
   CREATE DATABASE networkiocl;
   USE networkiocl;
   ```


2. **Create Network Monitoring Table**
   ```sql
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
   ```


3. **Create Downtime Tracking Table**
   ```sql
   CREATE TABLE downtime (
   	id INT AUTO_INCREMENT PRIMARY KEY,
   	service_provider VARCHAR(50) NOT NULL,
   	downtime_duration VARCHAR(50) NOT NULL,
   	recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP
   );
   ```


---


## 📦 Installation


1. **Clone the Repository**
   ```bash
   git clone https://github.com/Subhaa9/Network-Monitoring-Web-App.git
   cd Network-Monitoring-Web-App
   ```


2. **Set Up MySQL Database**
   - Create the database using the SQL scripts provided above.
   - Ensure MySQL is running and update the connection settings in your application.


3. **Start the Web Application**
   - Run the backend service or deploy it on a web server.


---


## 📡 Usage


- Add network entries with IP addresses, bandwidth, and real-time status.
- Monitor ISP uptime and identify frequent failures.
- View downtime logs to analyze network reliability.


---


## 🤝 Contributing


We welcome contributions! Follow these steps:


1. Fork this repository.
2. Create a new branch for your feature (`feature-xyz`).
3. Commit your changes and push them to your branch.
4. Submit a Pull Request.


---


## 📜 License


This project is licensed under the MIT License. Feel free to use, modify, and contribute to it.


---


## ⭐ Support Us


If you find this project useful, consider giving it a ⭐ on GitHub! Your support helps us grow 🚀!
