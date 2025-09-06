# PayZone - Enterprise Payroll Management System

[![CI/CD Pipeline](https://github.com/your-username/payzone/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/your-username/payzone/actions/workflows/ci-cd.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive, enterprise-grade payroll management system built with PHP 8.2, featuring modern CI/CD pipelines, containerization, and automated deployment.

## 🚀 Features

- **Employee Management**: Complete CRUD operations for employee data
- **Payroll Processing**: Automated payroll calculations and runs
- **Leave Management**: Request, approve, and track employee leave
- **Reporting & Analytics**: Comprehensive reports and data visualization
- **Multi-tenant Architecture**: Company-based data isolation
- **JWT Authentication**: Secure token-based authentication
- **Modern UI**: Bootstrap 5 with custom styling and dark/light themes
- **RESTful API**: Well-documented API endpoints
- **Docker Support**: Complete containerization setup

## 🏗️ Architecture

```
PayZone/
├── backend/                 # PHP API Backend
│   ├── src/                # Source code
│   │   ├── Controllers/    # API Controllers
│   │   ├── Services/       # Business logic
│   │   ├── Repositories/   # Data access layer
│   │   ├── Models/         # Data models
│   │   └── Config/         # Configuration
│   ├── public/             # Public web root
│   ├── tests/              # Unit tests
│   ├── Dockerfile          # Backend container
│   └── composer.json       # PHP dependencies
├── frontend/               # Web Frontend
│   ├── views/              # PHP templates
│   ├── assets/             # CSS, JS, Images
│   ├── includes/           # Common components
│   └── Dockerfile          # Frontend container
├── nginx/                  # Reverse proxy config
├── .github/workflows/      # CI/CD pipelines
└── docker-compose.yml      # Local development
```

## 🛠️ Tech Stack

### Backend
- **PHP 8.2** with FPM
- **Slim Framework** (PSR-7/15/17 compliant)
- **MySQL 8.0** database
- **JWT Authentication**
- **Composer** for dependency management

### Frontend
- **PHP** server-side rendering
- **Bootstrap 5** UI framework
- **Vanilla JavaScript** with modern ES6+
- **ApexCharts** for data visualization
- **Font Awesome** icons

### DevOps & CI/CD
- **GitHub Actions** for CI/CD
- **Docker** containerization
- **Nginx** reverse proxy
- **PHPStan** static analysis
- **PHPUnit** testing
- **Trivy** security scanning

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Git
- GitHub account (for CI/CD)

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/payzone.git
   cd payzone
   ```

2. **Start the application**
   ```bash
   docker-compose up -d
   ```

3. **Access the application**
   - Frontend: http://localhost
   - API: http://localhost/api/
   - Database Admin: http://localhost:8080

4. **Default login credentials**
   - Username: `admin`
   - Password: `admin123`

### Manual Setup (without Docker)

1. **Install PHP dependencies**
   ```bash
   cd backend
   composer install
   ```

2. **Setup database**
   ```sql
   CREATE DATABASE payroll_db;
   -- Import backend/schema.sql
   -- Import backend/seeds/seed.sql
   ```

3. **Configure environment**
   ```bash
   cp backend/.env.example backend/.env
   # Edit .env with your database credentials
   ```

4. **Start development servers**
   ```bash
   # Backend API
   cd backend/public && php -S localhost:8000

   # Frontend
   cd frontend && php -S localhost:3000
   ```

## 🔄 CI/CD Pipeline

### Automated Workflows

The CI/CD pipeline includes:

1. **Continuous Integration**
   - PHP syntax checking
   - Dependency installation
   - Unit testing with PHPUnit
   - Code quality analysis (PHPStan)
   - Security scanning (Trivy)

2. **Continuous Deployment**
   - Docker image building
   - Multi-stage deployment (staging/production)
   - Automated testing in staging
   - Production deployment on main branch

### GitHub Actions Setup

1. **Add repository secrets** in GitHub:
   ```
   DOCKER_USERNAME=your-dockerhub-username
   DOCKER_PASSWORD=your-dockerhub-password
   ```

2. **Push to main branch** to trigger production deployment
3. **Push to develop branch** to trigger staging deployment

### Manual Deployment

```bash
# Build and deploy
export DOCKER_USERNAME=your-username
export DOCKER_PASSWORD=your-password
./deploy.sh production latest
```

## 🧪 Testing

### Running Tests

```bash
cd backend
composer test
# or
vendor/bin/phpunit tests/
```

### Code Quality

```bash
# Static analysis
vendor/bin/phpstan analyse src

# Code style checking
vendor/bin/phpcs src
```

## 📊 API Documentation

### Authentication
```bash
POST /api/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

### Key Endpoints
- `GET /api/employees` - List employees
- `POST /api/payroll/run` - Run payroll
- `GET /api/reports/payroll` - Get payroll reports
- `GET /api/dashboard/kpi` - Dashboard KPIs

## 🐳 Docker Commands

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Rebuild images
docker-compose build --no-cache

# Access containers
docker-compose exec backend bash
docker-compose exec db mysql -u payroll_user -p payroll_db
```

## 🔒 Security Features

- JWT token-based authentication
- Password hashing with Argon2
- SQL injection prevention (PDO prepared statements)
- XSS protection headers
- CSRF protection
- Rate limiting
- Security scanning with Trivy

## 📈 Monitoring & Logging

- Application logs in `backend/logs/`
- Nginx access/error logs
- Database query logging
- Health check endpoints
- Performance monitoring

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write unit tests for new features
- Update documentation
- Ensure all CI checks pass

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Issues**: [GitHub Issues](https://github.com/your-username/payzone/issues)
- **Documentation**: [Wiki](https://github.com/your-username/payzone/wiki)
- **Discussions**: [GitHub Discussions](https://github.com/your-username/payzone/discussions)

## 🎯 Roadmap

- [ ] Mobile app development
- [ ] Advanced reporting with PDF export
- [ ] Multi-language support
- [ ] API rate limiting
- [ ] Real-time notifications
- [ ] Integration with third-party payroll systems

---

**Built with ❤️ for modern payroll management**