# PHP Test Application.

👉 **[docs/challenges.md](app/docs/challenges.md)**

👉 **[docs/solutions.md](app/docs/solutions.md)**

## Quick start

```sh
docker compose -f .setup/docker/docker-compose.yaml up
```

Seed dummy data
```sh
docker compose -f .setup/docker/docker-compose.yaml exec app php database/seed.php
```

Visit [http://localhost:8080](http://localhost:8080)

