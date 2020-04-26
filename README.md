# TASKS

## Установка

1. Скачать файлы `git clone justyor/task-list`
2. Установить зависимости `composer install`
3. Create database and change configs
```php
# /config/db.php
[
    'host' => 'localhost',
    'database' => 'test_tasks',
    'user' => 'root',
    'password' => '',
]
```

config migrations
```yaml
# /phinx.yml
development:
    adapter: mysql
    host: localhost
    name: test_tasks
    user: root
    pass: ''
    port: 3306
    charset: utf8
```
4. Запустить миграции `vendor/bin/phinx migrate -e development`
