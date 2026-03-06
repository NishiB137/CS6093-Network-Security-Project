# CS6093-Network-Security-Project
CS6093 Network Security Project - Transactiwar

## Prerequisites
* Docker
* Docker Compose

## Set Up

1. **Set up environment variables:**
   Add the .env file (the real contributors will have this hehe.)

2. **Build and start the containers:**

    Run the following command:
    ```
    sudo docker compose up -d --build
    ```

3. **Access the application:**
   
    Open your browser and navigate to: http://localhost:8000
   
    If connection and set up is right, a successful message will be printed.

5. **Helpful CommandsStop the application:**
    - Wipe the database and start fresh:
   
        If you change your .env database credentials or update the init.sql schema, you must destroy the Docker volume to force PostgreSQL to initialize everything again. Run the following commands:   
        ```
        sudo docker compose down -v
        sudo docker compose up -d --build
        ```
    - To see logs from all containers run the following:
        ```
        sudo docker compose logs
        ```