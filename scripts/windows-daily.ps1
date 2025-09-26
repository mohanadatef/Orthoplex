param(
    [string]$action = "status"
)
switch ($action) {
    "up" {
        docker-compose up -d
    }
    "down" {
        docker-compose down
    }
    "build" {
        docker-compose build --no-cache
    }
    "logs" {
        docker-compose logs -f
    }
    default {
        docker-compose ps
    }
}
