# IOweYou - server

This is the backend application which runs [IOweYou website](https://petrkubes.cz/ioweyou/) and provides an API for the [Android app](https://play.google.com/store/apps/details?id=cz.petrkubes.payuback).

## Installation

1. Clone the GitHub repository:
```
git clone git@github.com:PetrKubes97/ioweyou-server.git
```

2. Create a database using 'utf8mb4_bin'.

3. Create config.local.neon in the app/config folder with the following contents:

```
dbal:
    driver: mysqli
    host: 127.0.0.1
    database: yourdbname
    username: root
    password: root


facebook:
    appId: "your_app_id"
    appSecret: "your_secret"
    permissions: [public_profile, email, user_friends] # theese are the default read permissions, you might need to
    graphVersion: v2.8
```

5. Run migrations.
```
php www/index.php migrations:reset
```

6. Make sure that log and temp are writable.
```
chmod -R 777 temp; chmod -R 777 log;
```

7. At this point, you should be able to open the web page at ``localhost/ioweyou-server/www``.

### Testing the API

The API address is ``http://localhost/ioweyou-api/www/api/``. See apiary.apib for possible requests.

You can get the Facebook Access Token and ID at [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/). Click get token and check "user_friends" and "email".
