# laravel 12 with Cashier Template

Welcome to the repository for laravel 12 - Cashier App

## Features
- laravel 12
- Cashier

## Installation Guide

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js
- npm

### Installation Steps
1. Clone the repository:
    ```bash
    git clone https://github.com/Ragibavi/cashierApp.git
    ```

2. Navigate into the project directory:
    ```bash
    cd cashierApp
    ```

3. Install PHP dependencies:
    ```bash
    composer install
    ```

4. Install JavaScript dependencies:
    ```bash
    npm install && npm run dev
    ```

5. Copy the `.env.example` file and rename it to `.env`:
    ```bash
    cp .env.example .env
    ```

6. Generate application key:
    ```bash
    php artisan key:generate
    ```

7. Run database migrations:
    ```bash
    php artisan migrate
    ```

8. Start the development server:
    ```bash
    php artisan serve
    ```
## Contributors
- **Muhammad Syams Ragibavi** - [GitHub](https://github.com/ragibavi)
