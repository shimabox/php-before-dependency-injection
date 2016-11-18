# php-before-dependency-injection

-  https://blog.shimabox.net/2016/11/18/before_writing_phpunit_dbunit/
-  上記ブログのテストコード

## 準備

#### 1. cloneするかzipファイルをダウンロードしたら、以下コマンドを実行します

```
$ cd php-before-dependency-injection
$ composer install
```

#### 2. 以下ddl文を mysql のテスト用スキーマに流してテーブルを作ります

```sql
CREATE TABLE IF NOT EXISTS `sample` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `name` TEXT NOT NULL ,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
※ data/test_sample_ddl.sql をmysqlコマンドで流してもいいです

## 設定ファイルの修正

#### 1. .env ファイルを src/config/ に作ります

```
$ cp ./src/config/.env.example ./src/config/.env
```

#### 2. .env にDBの接続設定を書きます

```
$ vim ./src/config/.env
DB_DSN=mysql:host=localhost:3306;dbname=test;charset=utf8; # Your DB DSN
DB_USER=root  # Your DB USER
DB_PASS=  # Your DB PASSWORD
```

#### 3. phpunit.xml を修正します (**DB_DSN**, **DB_USER**, **DB_PASS**)

```
$ vim ./phpunit.xml
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
         ・・・
         stopOnFailure="false">
    <php>
        <!-- Your DB Setting -->
        <var name="DB_DSN" value="mysql:host=localhost:3306;dbname=test;charset=utf8;" />
        <var name="DB_USER" value="root" />
        <var name="DB_PASS" value="" />
        <!-- Your DB Setting -->
    </php>
    ・・・
</phpunit>
```

## 実行

```
$ ./vendor/bin/phpunit --group sample
```

## License
- MIT License
