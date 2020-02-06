# Trivago Case Study

## Requirements
- Docker

## Installation
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

## API Documentation
- `src/openapi.yaml` file can found that document the API based on swagger open source, please refere to it for
available endpoints and required parameters/data/headers

## Git Repository
- If you want to see commit history in the repository, click [here](https://github.com/islam94ibrahim/trivagor_case_study)
