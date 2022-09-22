# Pet-Shop API

> ### Pet-Shop Laravel Application.

This repo is functionality complete — PRs and issues welcome!

----------

# Getting started

## Installation

Clone the repository

    git clone git@github.com:Atunje/pet-shop.git

Switch to the repo folder

    cd pet-shop

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000/api/v1

**TL;DR command list**

    git clone git@github.com:Atunje/pet-shop.git
    cd pet-shop
    composer install
    cp .env.example .env
    php artisan key:generate

**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve

## Database seeding

**Populate 50 orders for 10 different users with a random amount of products, these orders will get randomly assigned a order status, if the order status is paid or shipped it is assigned a payment method.**

Run the database seeder and you're done

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh

# Code overview

## Dependencies

- [lcobucci/jwt](https://github.com/lcobucci/jwt) - For authentication using JSON Web Tokens

## Dev Dependencies

- [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) 
- [nunomaduro/larastan](https://github.com/nunomaduro/larastan)
- [nunomaduro/phpinsights](https://github.com/nunomaduro/phpinsights)

## Folders

- `app` - Contains all the Eloquent models
- `app/DTOs` - Contains the Data Transfer Object classes
- `app/Extensions` - Contains the JWT Guard implementation
- `app/Http/Controllers` - Contains all the controllers
- `app/Http/Middleware` - Contains the middlewares
- `app/Http/Requests` - Contains all the api form requests
- `app/Http/Resources` - Contains all the api resource files
- `app/Http/Services` - Contains the services
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `database/seeds` - Contains the database seeder
- `routes` - Contains all the api routes defined in api_v1.php file
- `tests` - Contains all the application tests
- `tests/Feature` - Contains all the api tests

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------

## Documentation

The api documentation can be accessed at [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation).

After seeding the database, the default admin credentials are:
- Email - test_admin@example.com
- Password - admin

----------
