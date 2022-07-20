## Introduction
DevDocker was developed to help me and other developers build and test their applications,  
as I noticed that I constantly repeat same steps in my development process.
With DevDocker, you can easily build and test your application in a Docker container, 
without worries about the environment.
This is the first version of DevDocker, and it is still in development.
It was built to be customizable, so you can easily change the Docker images and docker-compose file, 
for your specific needs. 

#### Inspiration
DevDocker wes greatly inspired by [Laravel Sail](https://github.com/laravel/sail). I highly recommend it! 
I used Sail package quite a bit, and I wanted to make something similar to it, but catering for my personal wishes and requirements.

## Installation

1. Install DevDocker with Composer:

```
1. composer require devdocker/devdocker --dev
```


2. Run install command and choose services you want to use:

```
php artisan devdocker:install
```

3. DevDocker will generate `./docker` directory with php and nginx configs, as well as `docker-compose.yml` file.

## Usage

1. Start the containers with:
```
docker-compose up
```
or
```
docker-compose up -d
```

2. Stop the containers with:
```
docker-compose down
```

Note: if you are using `phpmyadmin` service you need to specify the database in `.env` file, like so:
```
PMA_DB=mysql
# or 
PMA_DB=mariadb
```
By default, it is `mysql`.
