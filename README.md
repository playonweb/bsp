# Alink
A Better Stack's PHP Test Application.
A premium, production-ready user management system built for the Better Stack Senior Fullstack Engineer challenge.

## 🚀 Quick Start

Ensure you have [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/) installed.

```bash
# 1. Start the services
docker-compose -f .setup/docker-compose.yaml up -d --build

# 2. Access the application
Open http://localhost:8080
```

## ✨ Features

- **Apple Pro Design**: Retina-ready dark mode with glassmorphism effects.
- **Real-time Search**: Live filtering by city using AJAX.
- **Sorted City Selector**: Searchable sorted dropdown for consistent data entry.
- **Robust Backend**: Optimized MySQL 8.0 schema with InnoDB and B-Tree indexing.
- **Mobile First**: Fully responsive layout designed for both desktop and mobile efficiency.

## 🛠 Tech Stack

- **Backend**: PHP 7.4 (Custom Betterstack's Framework)
- **Database**: MySQL 8.0
- **Frontend**: Petite Vue (6KB Reactive Core), Browser Tailwind CSS 4.0
- **Icons**: PrimeIcons
- **Infrastructure**: Docker / Apache

## 📐 Project Structure

```sh
app/
├── assets/         # JS and CSS assets
├── config/         # Configuration files
├── core/           # Framework core (Untouched)
├── controllers/api # Standalone AJAX endpoints (CREATE, SEARCH)
├── database/       # SQL schema and seeds
├── models/         # Data Models
├── views/          # Layouts and pages
├── .htaccess       # Gateway
├── favicon.ico     # Favicon (Untouched)
├── router.php      # Handle API routing as per JSON API standards
└── index.php       # Entry point (Untouched)
.setup/             # Docker configuration
docs/               # Documentation
env.example         # Environment variables
```
## 📝 Detailed Choices

For a deep dive into the technical decisions regarding Database, Security, and Frontend, please refer to:

👉 **[docs/solutions.md](docs/solutions.md)**