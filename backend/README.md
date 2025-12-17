<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Mental Health Consultation - Backend

RESTful API backend built with Laravel 12 for the Mental Health Consultation Web App.

## Technology Stack

- **Framework**: Laravel 12
- **Language**: PHP 8.3.28
- **Authentication**: Laravel Sanctum 4.x
- **Database**: SQLite (development), supports PostgreSQL/MySQL
- **Testing**: Pest 4.x, PHPUnit 12.x
- **Code Style**: Laravel Pint

## API Structure

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user
- `POST /api/auth/password/change` - Change password

### Resources
- `GET|POST|PUT|DELETE /api/patients` - Patient management
- `GET|POST|PUT|DELETE /api/consultations` - Consultation management
- `GET|POST|PUT|DELETE /api/admin/users` - User management (admin only)
- `GET /api/dashboard` - Dashboard statistics

### Key Features

- **RESTful API**: Standard HTTP methods and status codes
- **JWT Authentication**: Token-based authentication via Sanctum
- **Role-Based Access**: Admin and Clinician roles with different permissions
- **Computed Fields**: API automatically includes computed fields (e.g., `patient_name`, `clinician_name`)
- **Search & Filtering**: Search by patient/clinician name, filter by status/risk
- **Pagination**: Standardized pagination metadata
- **Error Handling**: Consistent error response format with validation details
- **Audit Logging**: Comprehensive activity tracking

## API Response Format

### Success Response
```json
{
  "message": "Success message",
  "data": { /* resource data */ },
  "meta": { /* pagination or metadata */ }
}
```

### Error Response
```json
{
  "error": {
    "message": "Error message",
    "errors": {
      "field_name": ["Validation error message"]
    }
  }
}
```

See [Implementation Guide](../docs/09-implementation-guide.md) for detailed API documentation.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
