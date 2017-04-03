# IOweYou - server

This is the backend application which runs [IOweYou website](https://petrkubes.cz/ioweyou/) and provides an API for the [Android app](https://play.google.com/store/apps/details?id=cz.petrkubes.payuback). You can find its source code [here](https://github.com/PetrKubes97/ioweyou-android).

## Installation

1. Set up a web server with PHP and MySQL  

2. Clone the GitHub repository:
```
git clone git@github.com:PetrKubes97/ioweyou-server.git
```

3. Create a MySQL database using 'utf8mb4_bin'.

4. Create config.local.neon in the app/config folder with the following contents:

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
    permissions: [public_profile, email, user_friends]
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

### Libraries
* [Nette framework + Tracy and Latte](https://nette.org/)
* [Nextras ORM](https://nextras.org/orm/docs/2.2/)
* [Nextras Migrations](https://nextras.org/migrations/docs/3.0/)
* [Nextras Dbal](https://nextras.org/dbal/docs/2.1/)
* [Kdyby Facebook](https://github.com/Kdyby/Facebook)
* [Kdyby Console](https://github.com/Kdyby/Console)
* [Kdyby Translation](https://github.com/kdyby/translation)

### License

Apache
