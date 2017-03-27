# IOweYou - server

## Installation

1. Clone the GitHub repository
```
git@github.com:PetrKubes97/ioweyou-server.git
```

2. Create database using 'utf8mb4_bin'

3. Create file config.local.neon in the app/config folder

```
dbal:
	driver: mysqli
	host: 127.0.0.1
	database: yourdbname
	username: root
	password: root
```

4. Create the temp and dummy-data folder
```
    mkdir temp
```

5. Run migrations
```

```




