CREATE TABLE IF NOT EXISTS `sample` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `name` TEXT NOT NULL ,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;