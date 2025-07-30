# Najah Association - Internal Portal

This repository contains the codebase for the internal portal of Najah Association. The web application is built using Laravel and provides various features for employee management, task tracking, and departmental operations.

## Features

- User authentication and authorization
- User profile management
- Dashboard for quick overview
- Department management
- Executive plan tracking
- Task management with approval system
- Internal messaging system
- Request management
- Role-based access control
- Form builder and management
- Calendar view for tasks
- File attachments for tasks and messages

## Requirements to run locally

- Docker
- Docker Compose

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/Najah-h/employee.git
   cd employee
   ```

2. Copy the `.env.example` file to `.env` and configure your environment variables:
   ```
   cp .env.example .env
   ```

3. Start the Docker containers using Laravel Sail:
   ```
   ./vendor/bin/sail up -d
   ```

4. Install PHP dependencies:
   ```
   ./vendor/bin/sail composer install
   ```

5. Generate application key:
   ```
   ./vendor/bin/sail artisan key:generate
   ```

6. Run database migrations:
   ```
   ./vendor/bin/sail artisan migrate
   ```

7. Install and compile frontend assets:
   ```
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev

   ```

## Usage

Access the application through your web browser at `http://localhost`.

## Contributing

Please read our contributing guidelines before submitting pull requests.

## License

This project is proprietary software. All rights reserved.

