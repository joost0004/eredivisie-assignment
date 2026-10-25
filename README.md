# Eredivisie app

A Symfony app that shows the current Eredivisie season with data from football-data.org.

Features:
- Teams overview and a team page with all matches of the season
- Register, log in and log out
- Follow teams and see their latest result on My teams
- Upcoming matches, 20 per page
- Standings, calculated from the results
- `app:import` command to fetch teams and matches, run every 15 minutes by the Symfony Scheduler

## Setup

You need DDEV.

```
ddev start
ddev composer install
```

Get a free API key at football-data.org and add it to `.env.local`:

```
FOOTBALL_DATA_API_KEY=your-key
```

Create the database tables and import the data:

```
ddev exec bin/console doctrine:migrations:migrate -n
ddev exec bin/console app:import
```

Then run `ddev restart` once, so the scheduler starts, and open the site with `ddev launch`.

## Tests

```
ddev mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS db_test; GRANT ALL ON db_test.* TO 'db'@'%';"
ddev exec bin/console --env=test doctrine:migrations:migrate -n
ddev exec bin/phpunit
```