<h1>Installation</h1>

Install dependencies:

<code>composer install</code>

Initialize the database config:

<code>vendor/bin/phinx init</code>

Configure the database connection in the file phinx.yml

Run the database migrations for the specified in phinx.yml environment:

<code>vendor/bin/phinx migrate -e development</code>

Create the file .env.php in the config directory and put application configuration settings in it, such as database connection settings ( use .env.example.php as a template )
Create the file proxies.php in the config directory and put proxies addresses in it ( use proxies.example.php as a template )
