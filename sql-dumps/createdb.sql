SET FOREIGN_KEY_CHECKS = 0;
ALTER DATABASE example_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP TABLE IF EXISTS user;
CREATE TABLE user
(
    id         int          NOT NULL AUTO_INCREMENT,
    firstname  varchar(255) not null,
    lastname   varchar(255) not null,
    email      varchar(255) not null UNIQUE,
    password   varchar(255) not null,
    roles      varchar(255) not null,
    isVerified tinyint,
    signedUpOn datetime    not null,
    token      varchar(255) UNIQUE,
    PRIMARY KEY (id)
);

-- Création de la table event
CREATE TABLE IF NOT EXISTS event
(
    id          int           NOT NULL auto_increment,
    name        varchar(255)  NOT NULL,
    description varchar(2000) NOT NULL,
    startDate   datetime      NOT NULL,
    endDate     datetime      NOT NULL,
    tag         varchar(255)  NOT NULL,
    capacity    int           NOT NULL,
    fileName    varchar(100),
    fileSize    double,
    owner_id    int           NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT event_owner FOREIGN KEY (owner_id) REFERENCES user (id) on delete cascade on update cascade
);

-- Création de la table interested
DROP TABLE IF EXISTS interested;
CREATE TABLE interested
(
    id       int not null auto_increment,
    event_id int not null,
    user_id  int not null,
    PRIMARY KEY (id),
    CONSTRAINT interested_event FOREIGN KEY (event_id) REFERENCES event (id) on delete cascade on update cascade,
    CONSTRAINT interested_user FOREIGN KEY (user_id) REFERENCES user (id) on update cascade on delete cascade
);

-- Création de la table participant
DROP TABLE IF EXISTS participant;
CREATE TABLE participant
(
    id       int not null auto_increment,
    event_id int not null,
    user_id  int not null,
    PRIMARY KEY (id),
    CONSTRAINT participant_event FOREIGN KEY (event_id) REFERENCES event (id) on delete cascade on update cascade,
    CONSTRAINT participant_user FOREIGN KEY (user_id) REFERENCES user (id) on update cascade on delete cascade
);

-- Insertion des utilisateurs
INSERT INTO user (firstname, lastname, email, password, roles, isVerified, signedUpOn, token) VALUES
('Alice', 'Durand', 'alice.durand@example.com', 'hashed_password1', 'ROLE_USER', 1, NOW(), 'token123'),
('Bob', 'Martin', 'bob.martin@example.com', 'hashed_password2', 'ROLE_ADMIN', 1, NOW(), 'token456'),
('Charlie', 'Dupont', 'charlie.dupont@example.com', 'hashed_password3', 'ROLE_USER', 0, NOW(), NULL);

-- Insertion des événements
INSERT INTO event (name, description, startDate, endDate, tag, capacity, fileName, fileSize, owner_id) VALUES
('Soiree Étudiante', 'Une grande soirée pour tous les étudiants.', '2025-03-10 20:00:00', '2025-03-11 02:00:00', 'Fête', 100, 'party.jpg', 2.5, 1),
('Hackathon', 'Un concours de programmation pour les passionnés.', '2025-04-05 09:00:00', '2025-04-06 18:00:00', 'Technologie', 50, "test.jpg", 2.5, 2),
('Tournoi de Football', 'Un tournoi pour les amateurs de football.', '2025-05-20 10:00:00', '2025-05-20 18:00:00', 'Sport', 20, 'football.png', 3.2, 3);

-- Insertion des utilisateurs intéressés par les événements
INSERT INTO interested (event_id, user_id) VALUES
(1, 2), -- Bob est intéressé par la Soirée Étudiante
(1, 3), -- Charlie est intéressé par la Soirée Étudiante
(2, 1), -- Alice est intéressée par le Hackathon
(3, 2); -- Bob est intéressé par le Tournoi de Football

-- Insertion des participants aux événements
INSERT INTO participant (event_id, user_id) VALUES
(1, 1), -- Alice participe à la Soirée Étudiante
(2, 2), -- Bob participe au Hackathon
(3, 3); -- Charlie participe au Tournoi de Football

-- Réactivation des contraintes de clé étrangère
SET FOREIGN_KEY_CHECKS = 1;
