# Item list app - Development exercise

## Start project with Docker Compose

Build and start up containers.
```
docker-compose up -d --build
```

Run bootstrap script.
```
docker exec -it itemlist_app bash bootstrap.sh
```

Go to http://localhost:8000 and try this app :)

## Tests

Feature tests can be run executing:
```
docker exec -it itemlist_app ./vendor/bin/phpunit
```
