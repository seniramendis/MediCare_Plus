-- ============================================================
--  MediCare Plus — Full Database Schema + Sample Data
--  Run this in phpMyAdmin or: mysql -u root medicare_databs < medicare_databs.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `medicare_databs` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `medicare_databs`;

-- -------------------------------------------------------
-- USERS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `first_name`    VARCHAR(100)    NOT NULL,
  `last_name`     VARCHAR(100)    NOT NULL,
  `email`         VARCHAR(255)    NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)    NOT NULL,
  `role`          ENUM('patient','doctor','admin') NOT NULL DEFAULT 'patient',
  `status`        ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- PATIENTS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `patients` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`       INT UNSIGNED    NOT NULL UNIQUE,
  `date_of_birth` DATE            DEFAULT NULL,
  `gender`        ENUM('male','female','other') DEFAULT NULL,
  `phone`         VARCHAR(20)     DEFAULT NULL,
  `address`       TEXT            DEFAULT NULL,
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- DOCTORS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `doctors` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`          INT UNSIGNED  NOT NULL UNIQUE,
  `specialization`   VARCHAR(150)  NOT NULL,
  `consultation_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `availability`     VARCHAR(255)  DEFAULT 'Mon-Fri 9am-5pm',
  `rating`           DECIMAL(3,1)  NOT NULL DEFAULT 0.0,
  `profile_image`    VARCHAR(255)  DEFAULT 'default-doc.jpg',
  `bio`              TEXT          DEFAULT NULL,
  `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- SERVICES  ← THIS WAS THE MISSING TABLE
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(200)    NOT NULL,
  `category`    VARCHAR(100)    NOT NULL DEFAULT 'General',
  `description` TEXT            DEFAULT NULL,
  `price`       DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `icon`        VARCHAR(100)    DEFAULT 'fa-notes-medical',
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- APPOINTMENTS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `appointments` (
  `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `patient_id`       INT UNSIGNED    NOT NULL,
  `doctor_id`        INT UNSIGNED    NOT NULL,
  `appointment_date` DATETIME        NOT NULL,
  `status`           ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes`            TEXT            DEFAULT NULL,
  `created_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`)  REFERENCES `doctors`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- MESSAGES
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `sender_id`    INT UNSIGNED    NOT NULL,
  `recipient_id` INT UNSIGNED    NOT NULL,
  `subject`      VARCHAR(255)    DEFAULT NULL,
  `body`         TEXT            NOT NULL,
  `is_read`      TINYINT(1)      NOT NULL DEFAULT 0,
  `sent_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`sender_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipient_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- MEDICAL REPORTS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `medical_reports` (
  `id`                 INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `patient_id`         INT UNSIGNED    NOT NULL,
  `doctor_id`          INT UNSIGNED    NOT NULL,
  `report_title`       VARCHAR(255)    NOT NULL,
  `report_description` TEXT            DEFAULT NULL,
  `file_path`          VARCHAR(500)    DEFAULT NULL,
  `created_at`         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`)  REFERENCES `doctors`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- FEEDBACK
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedback` (
  `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `appointment_id` INT UNSIGNED    DEFAULT NULL,
  `doctor_id`      INT UNSIGNED    NOT NULL,
  `patient_id`     INT UNSIGNED    NOT NULL,
  `rating`         TINYINT(1)      NOT NULL DEFAULT 5,
  `comment`        TEXT            DEFAULT NULL,
  `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`)  REFERENCES `doctors`(`id`)  ON DELETE CASCADE,
  FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- BLOG POSTS
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(255)    NOT NULL,
  `excerpt`      TEXT            DEFAULT NULL,
  `body`         LONGTEXT        NOT NULL,
  `author`       VARCHAR(200)    NOT NULL DEFAULT 'MediCare Plus',
  `cover_image`  VARCHAR(255)    DEFAULT NULL,
  `status`       ENUM('draft','published') NOT NULL DEFAULT 'published',
  `published_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ============================================================
--  SAMPLE DATA
-- ============================================================

-- Admin user  (password: Admin@1234)
INSERT IGNORE INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`role`,`status`) VALUES
(1,'Admin','User','admin@medicareplus.lk','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','active');

-- Doctor user  (password: Doctor@1234)
INSERT IGNORE INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`role`,`status`) VALUES
(2,'Kasun','Perera','kasun@medicareplus.lk','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','doctor','active'),
(3,'Nimasha','Silva','nimasha@medicareplus.lk','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','doctor','active'),
(4,'Rohana','Fernando','rohana@medicareplus.lk','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','doctor','active');

-- Patient user (password: Patient@1234)
INSERT IGNORE INTO `users` (`id`,`first_name`,`last_name`,`email`,`password_hash`,`role`,`status`) VALUES
(5,'Senira','Mendis','patient@medicareplus.lk','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','patient','active');

INSERT IGNORE INTO `doctors` (`user_id`,`specialization`,`consultation_fee`,`availability`,`rating`,`bio`) VALUES
(2,'Cardiology',2500.00,'Mon, Wed, Fri 9am-1pm',4.8,'Experienced cardiologist with 15 years of practice.'),
(3,'Neurology',3000.00,'Tue, Thu 10am-4pm',4.7,'Specialist in neurological disorders and brain health.'),
(4,'Paediatrics',1800.00,'Mon-Fri 8am-12pm',4.9,'Dedicated paediatrician caring for children of all ages.');

INSERT IGNORE INTO `patients` (`user_id`) VALUES (5);

-- Services data (the missing table that caused the crash)
INSERT IGNORE INTO `services` (`name`,`category`,`description`,`price`,`icon`) VALUES
('General Consultation','General','Comprehensive medical consultation with our experienced general practitioners.',1500.00,'fa-stethoscope'),
('Cardiology','Specialist','Advanced heart care including ECG, echo, and stress testing.',2500.00,'fa-heart-pulse'),
('Neurology','Specialist','Diagnosis and treatment of brain, spine and nervous system disorders.',3000.00,'fa-brain'),
('Paediatrics','Specialist','Child healthcare from newborns through adolescence.',1800.00,'fa-baby'),
('Laboratory Tests','Diagnostics','Full blood panels, urine analysis, microbiology and more.',800.00,'fa-flask'),
('Radiology & Imaging','Diagnostics','X-Ray, Ultrasound, MRI and CT Scan services.',3500.00,'fa-x-ray'),
('Pharmacy','Pharmacy','In-house pharmacy stocking a comprehensive range of medications.',0.00,'fa-pills'),
('Emergency Care','Emergency','24/7 emergency medical care and trauma services.',5000.00,'fa-truck-medical'),
('Gynaecology','Specialist','Women\'s health, prenatal care and gynaecological procedures.',2200.00,'fa-venus'),
('Orthopaedics','Specialist','Bone, joint and muscle care including surgical and non-surgical treatment.',2800.00,'fa-bone');

INSERT IGNORE INTO `blog_posts` (`title`,`excerpt`,`body`,`author`,`status`) VALUES
('10 Tips for a Healthier Heart','Simple lifestyle changes that can transform your cardiovascular health.','<p>Maintaining a healthy heart is one of the most important things you can do for your overall well-being. Here are ten evidence-based tips to keep your heart strong...</p><p>1. Exercise regularly — aim for at least 150 minutes of moderate aerobic activity per week.<br>2. Eat a heart-healthy diet rich in fruits, vegetables, and whole grains.<br>3. Quit smoking — it is the single most impactful change you can make.<br>4. Manage stress through meditation, yoga, or deep-breathing exercises.<br>5. Monitor your blood pressure regularly.<br>6. Keep cholesterol levels in check with diet and medication if required.<br>7. Maintain a healthy weight.<br>8. Limit alcohol consumption.<br>9. Get regular medical check-ups.<br>10. Sleep 7–9 hours per night.</p>','Dr. Kasun Perera','published'),
('Understanding Childhood Vaccinations','Everything parents need to know about keeping their children protected.','<p>Vaccinations are one of the greatest public health achievements of modern medicine. They protect children from serious diseases before those diseases have a chance to make them sick. Here is what every parent should know...</p><p>The National Immunisation Programme in Sri Lanka covers diseases such as tuberculosis, polio, diphtheria, tetanus, whooping cough, hepatitis B, measles, mumps, and rubella. All vaccines are given at specific ages to match the development of the immune system.</p><p>Common concerns such as mild fever or soreness at the injection site are normal and temporary. Serious side-effects are extremely rare. Talk to your paediatrician if you have any concerns.</p>','Dr. Rohana Fernando','published'),
('Managing Stress in Modern Life','Practical strategies to reduce stress and improve mental well-being.','<p>Stress is an unavoidable part of modern life, but chronic stress can have serious health consequences. Learning to manage it effectively is a crucial life skill.</p><p>Practical techniques include regular physical exercise, mindfulness meditation, adequate sleep, limiting screen time, and maintaining social connections. If stress becomes overwhelming, speaking with a mental health professional is always a wise step.</p>','MediCare Plus Editorial Team','published');
