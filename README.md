# Boxyback

Tool to backup web apps at Boxydev.

## Build

```
curl -LSs https://box-project.github.io/box2/installer.php | php
php -d phar.readonly=0 box.phar build
```

## Testing

```
wget https://phar.phpunit.de/phpunit-6.2.phar -O phpunit.phar
chmod +x phpunit.phar
php phpunit-6.2.phar
```