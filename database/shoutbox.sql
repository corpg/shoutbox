-- Base de donnee pour la shoutbox du site de l'AIV
-- Cree par Etienne  --   etienne.glossi@iut-valence.fr

-- creation de la base de donnee
-- drop database if exists test_shoutbox;
-- create database test_shoutbox;
-- use test_shoutbox;

-- Creation de la table download
drop table if exists shoutbox;
create table shoutbox (
	`ID` int unsigned NOT NULL auto_increment,		-- cle primaire
	`pseudonyme` varchar(15) NOT NULL,				-- le nom de la personne qui a poste
	`dateMessage` timestamp NOT NULL default now(),	-- la date d'ajout du message
	`message` text NOT NULL, 						-- le message
	`ip` varchar(15) NOT NULL default "0.0.0.0",	-- l'IP au cas ou
	`sexe` enum('femme', 'homme', 'inconnu') default "inconnu",	-- le sexe du posteur
	primary key (`ID`)
) ENGINE=InnoDB default CHARSET=latin1;