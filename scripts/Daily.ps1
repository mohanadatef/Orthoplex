
# Daily.ps1
param(
    [string]$Action = "start"  # start, stop, build, logs
)

switch ($Action) {
    "start" {
        docker-compose up -d
        Write-Host "Containers started"
    }
    "stop" {
        docker-compose down
        Write-Host "Containers stopped"
    }
    "build" {
        docker-compose build
        Write-Host "Containers rebuilt"
    }
    "logs" {
        docker-compose logs -f
    }
    default {
        Write-Host "Unknown action. Use start|stop|build|logs"
    }
}
