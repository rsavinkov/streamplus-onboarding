# Installation

1. docker-compose up -d
1. docker-compose exec php-fpm composer install
1. docker-compose exec php-fpm cp /application/.env.dist /application/.env
1. docker-compose exec php-fpm php bin/console doctrine:migtrations:migrate
1. Check link http://localhost:60000/onboarding