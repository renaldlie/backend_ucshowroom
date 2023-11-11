CREATE DATABASE IF NOT EXISTS speaking_unud;

USE speaking_unud;

CREATE TABLE IF NOT EXISTS soal(
	id_soal INT(8) NOT NULL AUTO_INCREMENT,
    question VARCHAR(200) NOT NULL,
    /*Tambah difficulty integer*/
    difficulty INT(8) NOT NULL, 
    timer INT(2) NOT NULL,
    PRIMARY KEY(id_soal)
);

CREATE TABLE IF NOT EXISTS student(
	id_student INT(8) NOT NULL AUTO_INCREMENT, 
    name VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(255),
    PRIMARY KEY(id_student)
);

CREATE TABLE IF NOT EXISTS teacher(
	id_teacher INT(8) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
	token VARCHAR(255),
    PRIMARY KEY(id_teacher)
);

CREATE TABLE IF NOT EXISTS speaking_test(
	id_test INT(8) NOT NULL AUTO_INCREMENT,
    datetime datetime NOT NULL,
    id_student INT(8) NOT NULL,
    PRIMARY KEY(id_test),
    FOREIGN KEY(id_student) REFERENCES student(id_student)
);

CREATE TABLE IF NOT EXISTS st_detail(
	id_std INT(8) NOT NULL AUTO_INCREMENT,
    id_test INT(8) NOT NULL,
    /*  Ganti 
            id_soal -> question
        Tambah 
            difficulty
    */
    question INT(8) NOT NULL,
    difficulty INT(8) NOT NULL,
    answer VARCHAR(20),
    score INT(2),
    PRIMARY KEY(id_std),
    FOREIGN KEY(id_test) REFERENCES speaking_test(id_test),
    FOREIGN KEY(question) REFERENCES soal(question),
    FOREIGN KEY(difficulty) REFERENCES soal(difficulty)
);