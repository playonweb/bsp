# Registry of contacts
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

- **Backend**: PHP 7.4 (Custom Framework)
- **Database**: MySQL 8.0
- **Frontend**: Petite Vue (6kb Reactive Core), Tailwind CSS 4.0
- **Icons**: PrimeIcons
- **Infrastructure**: Docker / Apache

## 📐 Project Structure

```text
app/
├── api/            # Standalone AJAX endpoints (CREATE, SEARCH)
├── controllers/    # Business logic (Optional use)
├── core/           # Framework core (Untouched)
├── database/       # SQL schema and migrations
├── views/          # Apple-themed layouts and pages
└── index.php       # Entry point
.setup/             # Docker configuration
```

## 📝 Detailed Choices

For a deep dive into the technical decisions regarding Database, Security, and Frontend, please refer to:
👉 **[REASONING.md](REASONING.md)**