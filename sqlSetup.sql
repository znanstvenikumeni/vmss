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