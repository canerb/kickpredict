# KickPredict

A Laravel application built with modern web technologies for football prediction AI features.

## Features

- **Laravel 12** - Latest Laravel framework
- **Tailwind CSS v4** - Modern utility-first CSS framework
- **Livewire 3** - Full-stack framework for Laravel
- **PostgreSQL** - Robust database system
- **Docker** - Containerized development environment

## Prerequisites

- Docker and Docker Compose
- Node.js (for local development)
- Composer (for local development)

## Quick Start with Docker

1. Clone the repository and navigate to the project directory:
   ```bash
   cd kickpredict
   ```

2. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

3. The application will be available at: http://localhost:8000

## Local Development Setup

1. Install PHP dependencies:
   ```bash
   composer install
   ```

2. Install Node.js dependencies:
   ```bash
   npm install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

4. Configure your database settings in `.env`

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Build assets:
   ```bash
   npm run build
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

## Docker Services

- **App**: Laravel application (PHP 8.3 + FPM)
- **Webserver**: Nginx web server
- **Database**: PostgreSQL 15

## Project Structure

```
kickpredict/
├── app/
│   └── Livewire/          # Livewire components
├── resources/
│   ├── views/
│   │   ├── layouts/       # Blade layouts
│   │   └── livewire/      # Livewire component views
│   └── css/
│       └── app.css        # Tailwind CSS entry point
├── docker-compose.yml     # Docker services configuration
├── Dockerfile            # PHP application container
├── nginx/                # Nginx configuration
└── php/                  # PHP configuration
```

## Available Commands

- `docker-compose up -d` - Start all services
- `docker-compose down` - Stop all services
- `docker-compose logs -f` - View logs
- `docker-compose exec app php artisan migrate` - Run migrations
- `docker-compose exec app composer install` - Install PHP dependencies

## Livewire Components

The project includes a sample Livewire component (`Welcome`) that demonstrates:
- Component structure
- Reactive properties
- Event handling
- Tailwind CSS integration

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
