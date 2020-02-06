# Trivago Case Study

## Installation
- Make sure you have Docker installed on your system
- Copy `.env.example` and rename it to `.env` file inside `/src` directory
- Run `docker-compose exec php composer install` inside `/src` directory, 
please note that the `composer` might need to be a relative path based on system PATH variables
- Run `docker-compose exec php php artisan migrate` inside `/src` directory to migrate the database, 
please note that the `artisan` might need to be a relative path based on system PATH variables
- Run `docker-compose build && docker-compose up -d` to build the docker environment
- Start testing the application through `http://localhost:8080/`

## Docker
- Containers created and their ports are as follows:
    - **nginx** - `:8080`
    - **mysql** - `:3306`
    - **php** - `:9000`
