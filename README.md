# PHP Test Application.

👉 **[docs/system-design.md](app/docs/system-design.md)**

👉 **[docs/chsllenges.md](app/docs/challenges.md)**

## Quick start

```sh
docker compose -f .setup/docker/docker-compose.yaml up
```

Seed dummy data
```sh
docker compose -f .setup/docker/docker-compose.yaml exec app php database/seed.php
```

Visit [http://localhost:8080](http://localhost:8080)

