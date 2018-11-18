<?php

$GLOBALS['dbHost'] = 'host';
$GLOBALS['dbUser'] = 'user';
$GLOBALS['dbPass'] = 'pass';
$GLOBALS['dbDatabase'] = 'dbname';
$GLOBALS['adminKey'] ='Some random phrase you define for small security';
$GLOBALS['debug'] = FALSE;

    
/* Needs this table:
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`), UNIQUE (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*/
?>