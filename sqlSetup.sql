-- Create syntax for TABLE 'allowedClients'
CREATE TABLE `allowedClients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `publicKey` varchar(512) NOT NULL DEFAULT '',
  `secretKey` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'nonces'
CREATE TABLE `nonces` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app` varchar(64) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `nonce` varchar(512) DEFAULT NULL,
  `used` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

create table videos ( id bigint(20) primary key auto_increment, vmssID varchar(512) unique, originalUploadFile varchar(512), data longtext, files longtext, clientKey varchar(256) ) character set = utf8mb4, auto_increment=41100; 

create table queue ( id bigint(20) primary key auto_increment, vmssID varchar(512), action varchar(256), status int(3) ) character set = utf8mb4, auto_increment=99910; 