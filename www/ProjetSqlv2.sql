
CREATE TYPE e_poste AS ENUM ('Caissier','Sécurité','Technicien','Entretien');

CREATE TYPE capteur_type AS ENUM (
    'Barrière infrarouge',
    'Capteur laser',
    'Détecteur de mouvement',
    'Caméra de vision',
    'Détection RFID',
    'Ultrasons',
    'Plaque de pression'
);

CREATE TYPE area_theme AS ENUM (
    'Aventure',
    'Fantaisie',
    'Science-fiction',
    'Horreur',
    'Historique',
    'Tropical',
    'Western',
    'Sous-marin',
    'Espace',
    'Pirates'
);

CREATE TYPE attrac_type AS ENUM ('Rotors',
    'Carrousels',
    'Chaises volantes',
    'Music Express',
    'Tasses rotatives',
    'Pieuvres',
    'Speed/Booster',
    'Grandes roues',
    'Pendulaires',
    'Crazy bus',
    'Bateau à bascule',
    'Top Spin',
    'Tapis volants',
    'Mad House',
	'Montagnes russes');


CREATE TABLE employees (
	e_id char(8) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (e_id,1,2)='e_') ,
	e_nom varchar(16) NOT NULL CHECK ((LENGTH (e_nom) >= 2 ) AND ( e_nom ~ '^[A-Za-z ]+$')) ,
	e_prenom varchar(16)  NOT NULL CHECK ((LENGTH (e_prenom) >= 2 ) AND ( e_prenom ~ '^[A-Za-z ]+$')) ,
	e_poste  e_poste NOT NULL,
	e_age INT NOT NULL CHECK (e_age >= 16),
	e_salaire FLOAT NOT NULL CHECK (e_salaire >= 0)
);

CREATE TABLE parcs(
 	parc_id char(8) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (parc_id,1,5)='parc_') ,
	parc_nom varchar(16) UNIQUE NOT NULL CHECK ((LENGTH (parc_nom) >= 2 ) AND (parc_nom ~ '^[A-Za-z ]+$')) ,
	parc_surface INT NOT NULL CHECK (parc_surface > 0),
	parc_ouverture TIME WITH TIME ZONE NOT NULL,
	parc_fermeture TIME WITH TIME ZONE NOT NULL,
	parc_prix_entree FLOAT NOT NULL CHECK (parc_prix_entree > 0),
	parc_adresse varchar(48) NOT NULL,
	parc_numero char(10) NOT NULL CHECK ( parc_numero ~ '^[0-9]+$' )	
);


CREATE TABLE areas(
 	area_id char(8) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (area_id,1,5)='area_') ,
	area_nom varchar(16) UNIQUE NOT NULL CHECK ((LENGTH (area_nom) >= 2 ) AND (area_nom ~ '^[A-Za-z ]+$')) ,
	area_surface INT NOT NULL CHECK (area_surface > 0),
	area_theme area_theme NOT NULL,  
	parc_id char(8) NOT NULL,
	CONSTRAINT fk_parc_id FOREIGN KEY (parc_id) REFERENCES parcs(parc_id)
);

CREATE TABLE installations (
	instal_id char(10) UNIQUE PRIMARY KEY NOT NULL CHECK ((SUBSTRING (instal_id,1,6)='attrac') OR (SUBSTRING (instal_id,1,6)='sanita') OR (SUBSTRING (instal_id,1,6)='magasi')),
	instal_nom varchar(32) UNIQUE NOT NULL CHECK ((LENGTH (instal_nom) >= 2 ) AND (instal_nom ~ '^[A-Za-z ]+$')),
	ouvert BOOLEAN NOT NULL,
	instal_fast_pass BOOLEAN NOT NULL,
	instal_waiting INT,
	instal_surface INT NOT NULL CHECK (instal_surface>0),
	instal_ouverture TIME WITH TIME ZONE NOT NULL,
	instal_fermeture TIME WITH TIME ZONE NOT NULL,
	instal_description varchar(256) ,
	area_id char(8) NOT NULL,
	centrale_id char(10) UNIQUE NOT NULL CHECK (SUBSTRING (centrale_id,1,8)='centrale'),
	
	CONSTRAINT fk_id_area FOREIGN KEY (area_id) REFERENCES areas(area_id)
	);

CREATE TABLE attractions (
	attrac_id char(10) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (attrac_id,1,6)='attrac'),
	attrac_type attrac_type NOT NULL,
	attrac_taille INT  NOT NULL CHECK (attrac_taille >= 0),
	attrac_poid_max INT NOT NULL,
	attrac_poid_min INT NOT NULL, 
	CONSTRAINT fk_attrac_id FOREIGN KEY (attrac_id) REFERENCES installations (instal_id)
	);

CREATE TABLE sanitaires (
	sanit_id char(10) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (sanit_id,1,6)='sanita'),
	sanit_nb_homme INT CHECK (sanit_nb_homme >= 0),
	sanit_nb_femme INT CHECK (sanit_nb_femme >= 0),
	CONSTRAINT fk_sanit_id FOREIGN KEY (sanit_id) REFERENCES installations (instal_id)
	);

CREATE TABLE magasins (
	magasin_id char(10) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (magasin_id,1,6)='magasi'),
	magasin_nb_caisse INT CHECK (magasin_nb_caisse >= 0),
	CONSTRAINT fk_magasin_id FOREIGN KEY (magasin_id) REFERENCES installations (instal_id)
);

CREATE TABLE fournisseurs(
	fournisseur_id char(8) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (fournisseur_id,1,6)='fourni'),
	fournisseur_nom varchar(32) UNIQUE NOT NULL,
	fournisseur_adresse varchar(32) NOT NULL,
	fournisseur_numero char(10) CHECK ( fournisseur_numero ~ '^[0-9]+$' ),
	fournisseur_email varchar(32) NOT NULL CHECK (fournisseur_email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$')
);

CREATE TABLE capteurs (
	capteur_id char(10) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (capteur_id,1,6)='captor'),
	capteur_type capteur_type NOT NULL,
	capteur_instalation DATE NOT NULL,
	capteur_last_upkeep DATE,
	capteur_HS BOOLEAN NOT NULL,
	centrale_id char(10) NOT NULL,
	fournisseur_id char(10) NOT NULL,
	CONSTRAINT fk_centrale_id FOREIGN KEY (centrale_id) REFERENCES installations(centrale_id),
	CONSTRAINT fk_fournisseur_id FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(fournisseur_id)	
);

CREATE TABLE passages(
	passage_id char(12) UNIQUE PRIMARY KEY NOT NULL CHECK (SUBSTRING (passage_id,1,6)='passag'),
	passage_date TIMESTAMP WITH TIME ZONE NOT NULL,
	passage_duree INT NOT NULL CHECK (passage_duree > 0),
	capteur_id char(12) NOT NULL,
	CONSTRAINT fk_capteur_id FOREIGN KEY (capteur_id) REFERENCES capteurs (capteur_id)
	);

CREATE TABLE affectations(
	e_id char(8) NOT NULL,
	instal_id char(10) NOT NULL,
	PRIMARY KEY (e_id,instal_id),
	CONSTRAINT fk_e_id FOREIGN KEY (e_id) REFERENCES employees(e_id),
	CONSTRAINT fk_instal_id FOREIGN KEY (instal_id) REFERENCES installations(instal_id)
);

INSERT INTO employees (e_id, e_nom, e_prenom, e_poste, e_age, e_salaire) 
VALUES 
    ('e_000001', 'Dupont', 'Alice', 'Caissier', 25, 2000.50),
    ('e_000002', 'Martin', 'Bob', 'Sécurité', 30, 2500.00),
    ('e_000003', 'Durand', 'Claire', 'Technicien', 35, 3000.00),
    ('e_000004', 'Petit', 'David', 'Entretien', 28, 1800.75);

INSERT INTO parcs (parc_id, parc_nom, parc_surface, parc_ouverture, parc_fermeture, parc_prix_entree, parc_adresse, parc_numero)
VALUES 
    ('parc_001', 'AquaPark', 50000, '09:00+01', '20:00+01', 25.00, '123 Rue des Lacs, Ville', '0123456789'),
    ('parc_002', 'FunLand', 80000, '10:00+01', '22:00+01', 30.00, '456 Avenue du Soleil, Ville', '0987654321');

INSERT INTO areas (area_id, area_nom, area_surface, area_theme, parc_id)
VALUES 
    ('area_001', 'Zone Aventure', 10000, 'Aventure', 'parc_001'),
    ('area_002', 'Zone Fantaisie', 15000, 'Fantaisie', 'parc_001'),
    ('area_003', 'Zone Pirate', 12000, 'Pirates', 'parc_001');

INSERT INTO installations (instal_id, instal_nom, ouvert, instal_fast_pass, instal_waiting, instal_surface, instal_ouverture, instal_fermeture, instal_description, area_id,centrale_id)
VALUES
    ('attrac001', 'Grande Roue', TRUE, TRUE, 15, 500, '09:30+01', '19:30+01', 'Vue panoramique incroyable.', 'area_001','centrale01'),
    ('attrac002', 'Montagnes russes', TRUE, TRUE, 45, 1200, '10:00+01', '20:00+01', 'Sensations fortes garanties.', 'area_002','centrale02'),
    ('attrac003', 'Train Fantome', TRUE, FALSE, 25, 800, '11:00+01', '21:00+01', 'Parcours terrifiant.', 'area_002','centrale03'),
    ('sanita001', 'Toilettes Zone Aventure', TRUE, FALSE, NULL, 100, '09:00+01', '20:00+01', 'Propreté garantie.', 'area_001','centrale04'),
    ('sanita002', 'Toilettes Zone Pirate', TRUE, FALSE, NULL, 120, '09:00+01', '20:00+01', 'Facilité d accès.', 'area_003','centrale05'),
    ('magasi001', 'Boutique Aventure', TRUE, FALSE, NULL, 200, '09:00+01', '20:00+01', 'Souvenirs et goodies.', 'area_001','centrale06'),
    ('magasi002', 'Boutique Fantaisie', TRUE, FALSE, NULL, 250, '09:30+01', '21:00+01', 'Accessoires magiques.', 'area_002','centrale07'),
    ('attrac004', 'Bateau Pirate', TRUE, TRUE, 30, 900, '10:30+01', '20:30+01', 'Une expérience inoubliable pour les petits et les grands.', 'area_003','centrale08'),
    ('attrac005', 'Manege Carrousel', TRUE, FALSE, 20, 600, '10:00+01', '18:00+01', 'Manège classique pour enfants.', 'area_001','centrale09'),
    ('sanita003', 'Toilettes Zone Fantaisie', TRUE, FALSE, NULL, 150, '09:00+01', '20:00+01', 'Accessible et propre.', 'area_002','centrale10');


INSERT INTO attractions (attrac_id, attrac_type, attrac_taille, attrac_poid_max, attrac_poid_min)
VALUES 
    ('attrac001', 'Grandes roues', 100, 120, 40),
	('attrac002', 'Montagnes russes',140, 120, 40),
	('attrac003', 'Mad House', 25, 100, 30),
	('attrac004','Bateau à bascule', 80, 120, 30),
	('attrac005', 'Carrousels', 25, 40, 30);

INSERT INTO sanitaires (sanit_id, sanit_nb_homme, sanit_nb_femme)
VALUES 
    ('sanita001', 5, 5),
	('sanita002', 3, 2),
	('sanita003', 4, 4);

INSERT INTO magasins (magasin_id, magasin_nb_caisse)
VALUES 
    ('magasi001', 3),
	('magasi002',2);

INSERT INTO fournisseurs (fournisseur_id, fournisseur_nom, fournisseur_adresse, fournisseur_numero, fournisseur_email)
VALUES 
    ('fourni01', 'TechSensors', '789 Rue des Inventions, Ville', '0123456789', 'support@techsensors.com'),
    ('fourni02', 'CleanPark', '321 Allée des Services, Ville', '0987654321', 'contact@cleanpark.com');
	
INSERT INTO capteurs (capteur_id, capteur_type, capteur_instalation, capteur_last_upkeep, capteur_HS, centrale_id, fournisseur_id)
VALUES 
    ('captor0001', 'Barrière infrarouge', '2024-01-01', '2024-11-01', FALSE, 'centrale01', 'fourni01');

INSERT INTO passages (passage_id, passage_date, passage_duree, capteur_id)
VALUES 
    ('passag0001', '2024-11-28 10:15:00+01', 5, 'captor0001');

INSERT INTO affectations (e_id, instal_id)
VALUES 
    ('e_000001', 'magasi001'),
    ('e_000002', 'sanita001'),
    ('e_000003', 'attrac001');

SELECT * FROM installations;

/*DROP TABLE affectations;
DROP TABLE passages;
DROP TABLE capteurs;
DROP TABLE fournisseurs;
DROP TABLE sanitaires;
DROP TABLE magasins;
DROP TABLE attractions;
DROP TABLE installations;
DROP TABLE employees;
DROP TABLE areas;
DROP TABLE parcs;*/


