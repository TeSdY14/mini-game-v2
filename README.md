# mini-game-v2
Minigame en POO avec héritages

CREATE TABLE IF NOT EXISTS `personnages_v2` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `degats` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `timeEndormi` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` enum('magicien','guerrier') NOT NULL,
  `atout` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
