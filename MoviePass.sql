create database MoviePass;
use MoviePass;

create table genres (id_genre int NOT NULL,
					 name_genre varchar (50) NOT NULL,
                     constraint pk_idGenre primary key (id_genre),
                     constraint unq_nameGenre unique (name_genre));
                     
create table movies (id_movie int NOT NULL,
					 title_movie varchar (100) NOT NULL,
                     overview_movie text,
                     img_movie varchar (100),
                     language_movie varchar (2) NOT NULL,
                     releaseDate_movie date NOT NULL,
                     duration_movie int,
                     constraint pk_idMovie primary key (id_movie));

create table movies_genres(id_movie int not null,
                    id_genre int not null,
                    constraint unq_moviesGenres unique (id_movie, id_genre));
                    
create table rols (id_rol int auto_increment,
				    description_rol text NOT NULL,
                    constraint pk_idRol primary key (id_rol));
                     
create table users (id_user int auto_increment,
					email_user varchar (50) NOT NULL,
                    password_user varchar (50) NOT NULL,
                    idRol_user int NOT NULL,
                    constraint pk_idUser primary key (id_user),
                    constraint fk_idRol foreign key (idRol_user) references rols (id_rol),
                    constraint unq_emailUser unique (email_user));

create table userProfiles (idUser_userprofile int NOT NULL,
                    firstName_userprofile varchar (50),
                    lastName_userprofile varchar (50),                    
                    dni_userprofile varchar (10),
                    constraint unq_idUser unique (idUser_userprofile),
                    constraint fk_idIser foreign key (idUser_userprofile) references users(id_user)
                    );
                    

create table cinemas (id_cinema int auto_increment,
					 name_cinema varchar (50) NOT NULL,
                     address_cinema varchar (50),
                     city_cinema varchar (50),
                     province_cinema varchar (50),
                     zip_cinema varchar (10),
                     constraint pk_idCinema primary key (id_cinema),
                     constraint unq_cinema unique (name_cinema, address_cinema));
                     
create table rooms (id_room int auto_increment,
                    name_room varchar(50) not null,
                    price_room float not null,
                    capacity_room int not null,
                    idCinema_room int not null,
                    constraint pk_idRoom primary key(id_room),
                    constraint fk_idCinema foreign key(idCinema_room) references cinemas(id_cinema));


create table shows (id_show int auto_increment,
                    idMovie_show int not null,
                    datetime_show datetime not null,
                    idRoom_show int not null,
                    constraint pk_idShow primary key(id_show),
                    constraint fk_idMovie foreign key(idMovie_show) references movies(id_movie),
                    constraint fk_idRoom foreign key(idRoom_show) references rooms(id_room)
                    );
                    
create table purchases (id_purchase int auto_increment,
						idUser_purchase int not null,
                        idShow_purchase int not null,
                        date_purchase datetime not null,
                        ticketsQuantity_purchase int not null,
                        discount_purchase float not null,
                        total_purchase int not null,
                        constraint pk_idPurchase primary key (id_purchase),
                        constraint fk_idUserPurchase foreign key (idUser_purchase) references users (id_user),
                        constraint fk_idShowPurchase foreign key (idShow_purchase) references shows (id_show)
                        );
                        
create table tickets (id_ticket int auto_increment,
					  idPurchase_ticket int not null,
                      ticketNumber_ticket int not null,
                      qr_ticket varchar (200),
                      constraint pk_idTicket primary key (id_ticket),
                      constraint fk_idPurchaseTicket foreign key (idPurchase_ticket) references purchases (id_purchase)
					  );


INSERT INTO rols (id_rol, description_rol) VALUES (1, 'admin');
INSERT INTO rols (id_rol, description_rol) VALUES (2, 'user');

INSERT INTO users (email_user, password_user, idRol_user) values ('admin@moviepass.com', '1234', 1);
INSERT INTO userprofiles (idUser_userprofile) values (1);


INSERT INTO cinemas (name_cinema, address_cinema, city_cinema, province_cinema, zip_cinema) VALUES ('Cine Ambassador','Diagonal Centro 1673','Mar del Plata','Buenos Aires','7600');
INSERT INTO cinemas (name_cinema, address_cinema, city_cinema, province_cinema, zip_cinema) VALUES ('Cines Paseo Aldrey','Sarmiento 2685','Mar del Plata','Buenos Aires','7600');
INSERT INTO cinemas (name_cinema, address_cinema, city_cinema, province_cinema, zip_cinema) VALUES ('Cinemark Hoyts','Av. Alicia Moreau de Justo 1920','CABA','Buenos Aires','C1107');

INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala A', 500, 100, 1);
INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala B', 750, 120, 1);
INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala C', 600, 115, 1);

INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala A', 400, 90, 2);
INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala B', 650, 110, 2);
INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala C', 450, 105, 2);

INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala A', 440, 110, 3);
INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala B', 670, 105, 3);
INSERT INTO rooms (name_room, price_room ,capacity_room, idCInema_room) VALUES ('Sala C', 430, 128, 3);

INSERT INTO `shows` VALUES (29,337401,'2020-10-27 14:11:00',1),(30,337401,'2020-10-27 14:20:00',1),(31,337401,'2020-10-28 14:25:00',4),(32,4291,'2020-10-22 10:49:00',2),(33,78450,'2020-10-31 14:56:00',3),(34,213,'2020-10-29 14:20:00',5),(35,468,'2020-10-30 14:20:00',5),(36,4291,'2020-10-31 14:20:00',6),(37,86835,'2020-11-01 14:20:00',3),(38,340102,'2020-11-02 14:20:00',1),(39,11194,'2020-11-04 14:20:00',2),(40,11423,'2020-11-05 14:20:00',3),(41,446893,'2020-11-03 14:20:00',4),(42,446893,'2020-11-03 14:20:00',4),(43,669770,'2020-11-03 14:20:00',3),(44,669665,'2020-11-03 14:20:00',2),(45,678999,'2020-11-03 14:20:00',1),(46,684308,'2020-11-03 14:20:00',4),(47,539529,'2020-11-03 14:20:00',2),(48,541305,'2020-11-03 14:20:00',8),(49,550652,'2020-11-03 14:20:00',7);

SELECT s.id_show, s.datetime_show, m.*, r.*, c.name_cinema FROM shows s
INNER JOIN movies m ON s.idMovie_show = m.id_movie
INNER JOIN rooms r ON s.idRoom_show = r.id_room
INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
WHERE m.id_movie = 213
ORDER BY s.datetime_show;

SELECT SUM(p.ticketsQuantity_purchase) AS sold
FROM purchases p
WHERE p.idShow_purchase = 71;

SELECT * from purchases;
drop table tickets;
drop table purchases;

insert into purchases (idUser_purchase, IdShow_purchase, date_purchase, ticketsQuantity_purchase, discount_purchase, total_purchase) values (1,71,now(),20,0,12000);
insert into purchases (idUser_purchase, IdShow_purchase, date_purchase, ticketsQuantity_purchase, discount_purchase, total_purchase) values (1,71,now(),15,0,9000);
insert into purchases (idUser_purchase, IdShow_purchase, date_purchase, ticketsQuantity_purchase, discount_purchase, total_purchase) values (1,71,now(),80,0,48000);

insert into tickets (idPurchase_ticket, ticketNumber_ticket, qr_ticket) values (1,20,'qr');





ALTER TABLE users
MODIFY password_user varchar(50);
