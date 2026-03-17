# CS6093-Network-Security-Project
# TransactiWar – Battle for Security, Compete for Supremacy

## Team - 4
- CS23BTECH11006 : Anusha Kumaresan
- CS23BTECH11018 : Dondeti Samhitha
- CS23BTECH11030 : Kukkala Aashritha Reddy
- CS23BTECH11041 : Nishi Baranwal
- CS23BTECH11044 : Paidala Vindhya

[GitHub Project link](https://github.com/NishiB137/CS6093-Network-Security-Project)

## Project Overview

TransactiWar is a web application developed for the CS6903: Network Security course (2025–26) at IIT Hyderabad.

The project focuses on implementing functionalities like authentication, profile management, and financial transactions in PHP.

---

# Features & Page Functionality

## 1. Registration
Allows new users to create an account with:
- Unique username
- Unique Email
- Password

Every new user is initialized with a balance of Rs. 100.

---

## 2. Login
Provides secure authentication for existing users with session management.

---

## 3. Home
User dashboard where users can:
- View their current balance

---

## 4. Profile
Users can:
- View their own or other users' profiles
- Update their biography (long content supported)
- Upload profile images

---

## 5. Money Transfer
Allows users to transfer money to others using username.

Features include:
- Prevent negative balance transactions
- Optional comments for each transfer

---

## 6. Transaction History
Displays a complete log of transactions including:
- Sender details
- Receiver details
- Transfer amount
- Comments

---

## 7. User Search
Displays list of all users and allows to search for other users by exact match of:
- Username

---

## Logging System

A backend logging utility records every request with the following information:

```
<Webpage, Username, Timestamp, Client IP Address>
```

This helps with security auditing and monitoring.

---

# Prerequisites

Before running the project, ensure the following are installed:

- Docker
- Docker Compose

---

# Setup Instructions

## 1. Environment Configuration and Certificate

Navigate to the transactiwar directory and create the .env file.
Create certificate using the following command in docker/certs/ folder:
```
mkdir -p docker/certs
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/certs/server.key \
  -out docker/certs/server.crt \
  -subj "/C=IN/ST=Telangana/L=Hyderabad/O=IIT Hyderabad/OU=CS/CN=<IP_address>"

```
Additionly, permissions to view server.log needs to be given using chmod.

Before building and running the application, ensure the required permissions and directories are set:

```bash
# Give execute permission to setup script
chmod +x database/setup_permissions.sh

# Create logs directory
mkdir -p logs

# Create log file
touch logs/server.log

# Set read/write permissions for logging
chmod 666 logs/server.log

# Create uploads directory (used by the web server)
mkdir -p public/uploads/profile_images

# Give read-write-executable permissioans
chmod -R 777 public/uploads/profile_images
```

## 2. Build and Run the Application

From the root of the project folder run:

```bash
sudo docker compose up -d --build
```

This command will:
- Build Docker containers
- Start the application and database services

---

## 3. Automatic Account Creation

To populate the database with test users, run the provided script:

```bash
sudo docker compose exec web php scripts/create_users.php
```

This will create initial user accounts automatically.

---

## 4. Access the Application

Open your browser and go to:

```
https://10.96.1.220
```

or
```
https://localhost:443
```
If the setup is successful, you will see the login page.

---

# Helpful Commands

## Stop the Application

```bash
sudo docker compose down
```

## Reset the Environment

A helper script is provided to reset the deployment environment:

```bash
./reset.sh
```

This script will clean up containers and restore the environment. Execution permission must be given to run it.