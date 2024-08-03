# Assessment NEXT BASKET
Developer: Sarmad Kamal

Resume: https://rb.gy/3hgssk

Submission Date: 03 August 2024 at 04:00 PM (GMT+5)

Assessment Link: https://docs.google.com/document/d/1ORROUV1Gsqpyrp8MEjUl9pnIJsFOS-j2NEyv4NPpiWs/edit?usp=sharing


This assessment demonstrates a microservices architecture using Symfony, RabbitMQ, and Docker.

## Prerequisites

- Docker
- Docker Compose

## Technologies Used

- Symfony
- PHP 8.2
- PostgreSQL
- RabbitMQ
- Docker v4.33.0

## Setup

1. clone repository https://github.com/sarmadkamal464/microservice-assessment
   
```bash
cd microservice-assessment
```

2. Build and start the Docker containers:

```bash 
docker compose up -d --build
```

3. Install dependencies for both services (Optional as we will do it automatically):
   
```bash
docker compose exec users-service composer install
docker compose exec notifications-service composer install
```

## Usage

1. Create a new user:

```bash
curl -X POST -H "Content-Type: application/json" -d '{"email":"user@example.com","firstName":"John","lastName":"Doe"}' http://localhost:8080/users
```

2. Check the logs of the notifications service to see the consumed message:

open file : notifications-service/var/log/dev.log

Note: If you don't see any logs try to run the Message Listener manually:

```bash
docker compose exec notifications-service php bin/console messenger:consume amqp
```

## Running Tests

1. Run tests for the users service:

```bash
docker compose exec users-service php bin/phpunit
```

2. Run tests for the notifications service:

```bash
docker compose exec notifications-service php bin/phpunit
```

## Architecture

This project consists of two microservices:

1. Users Service: Handles user creation and dispatches events.
2. Notifications Service: Consumes user creation events and logs them.

The services communicate via RabbitMQ message broker.


