# todo_symf
todo symfony app

Use with nginx or local server with todo_front app.

Needs mysql database passes in .env file.

First thing before using app is to create database and run migrations using `bin/console doctrine:database:create` and `bin/console doctrine:migrations:migrate`.

App also needs to have perrmisions to read and write in .../var/pictures directory.
