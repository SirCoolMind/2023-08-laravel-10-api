# 2023-08-laravel-10-api
Reference:
this video https://www.youtube.com/watch?v=N9RKm7LJUjo&amp;t=25s

----------

# 🚀 Getting Started

## 📦 Installation

1. **Clone the repository**
  ```bash
  git clone https://github.com/SirCoolMind/2023-08-laravel-10-api.git
  cd 2023-08-laravel-10-api
  ```
2. **Install all the dependencies using composer**
  ```bash
  composer install
  ```
3. **Copy the example env file and make the required configuration changes in the .env file**
  ```bash
  cp .env.example .env
  ```
4. **Generate a new application key**
  ```bash
  php artisan key:generate
  ```
5. **Run the database migrations** (Set the database connection in .env before migrating)
  ```bash
  php artisan migrate
  ```
6. **Generate Laravel Passport Oauth**
  ```bash
    php artisan passport:install
  ```
  
and enjoy your coding!
