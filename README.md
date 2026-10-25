# Eredivisie app

A Symfony app that shows the current Eredivisie season with data from football-data.org.

Features:
- Teams overview and a team page with all matches of the season
- Register, log in and log out
- Follow teams and see their latest result on My teams
- Upcoming matches, 20 per page
- Standings, calculated from the results
- `app:import` command to fetch teams and matches, run every 15 minutes by the Symfony Scheduler

## Files

- `src/Command/ImportCommand.php`: the `app:import` command
- `src/FootballData/`: API client for football-data.org and the importer that creates or updates teams and matches by their API id
- `src/Schedule.php`: runs `app:import` every 15 minutes with the Symfony Scheduler. `.ddev/config.scheduler.yaml` starts the worker
- `src/Entity/`: `Team`, `Game` and `User` entities
- `src/Enum/`: match status and result (win, draw, loss)
- `src/Standings/`: calculates the standings from finished matches
- `src/Controller/`: teams, matches, standings, my teams, login and registration
- `src/Form/RegistrationFormType.php`: registration form, validated with constraints on the form and on `User`
- `templates/`: Twig templates styled with bootstrap
- `tests/`: tests for the importer, standings and match results

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