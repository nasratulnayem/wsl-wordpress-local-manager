# WSL WordPress Local Manager

Portable local WordPress stack with:

- a PHP dashboard at `http://127.0.0.1:8080/`
- MariaDB over a local socket
- one-command site creation under `sites/<name>`

This repository is intended to keep the reusable stack only. It does not track
existing site data, database files, runtime logs, or temporary files.

## Requirements

- Ubuntu or WSL
- `php`
- `mysql`
- `mysqladmin`
- `mariadb-install-db`
- `mariadbd`
- `openssl`
- `unzip`

## Commands

```bash
cd /path/to/wordpress
./start
./create-site site1
./stop
```

## URLs

- Panel: `http://127.0.0.1:8080/`
- Example site: `http://127.0.0.1:8080/site1`

## What Git Tracks

- `bin/`
- `config/`
- `panel/`
- `source/wordpress`
- `source/wordpress.zip`
- design and support files needed by this stack

## What Git Ignores

- `sites/`
- `data/mariadb/`
- `data/*.dbpass`
- `logs/`
- `tmp/`

## Notes

- The scripts now resolve the project path dynamically, so the repo can be cloned
  anywhere, not only `/home/black/wordpress`.
- If `source/wordpress/` is missing but `source/wordpress.zip` exists, the start
  scripts will extract WordPress automatically.
