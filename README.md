# Trivago Case Study

## Installation
- Make sure you have Docker installed on your system
- Copy `.env.example` and rename it to `.env` file inside `/src` directory
- Run `docker-compose build && docker-compose up -d` to build the docker environment
- Run `docker-compose exec php composer install` inside `/src` directory
- Run `docker-compose exec php php artisan migrate` inside `/src` directory to migrate the database
- Start testing the application through `http://localhost:8080/`

## Docker
- Containers created and their ports are as follows:
    - **nginx** - `:8080`
    - **mysql** - `:3306`
    - **php** - `:9000`


## Testing
- Run `phpunit` inside `src` directory to run the tests
- Code coverage can be found under `report/index.html`
- Code coverage reached is +97% based on the Test driven development approach
