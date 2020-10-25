# phpMyAdmin MySQL-Dump
# version 2.2.2
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# --------------------------------------------------------
#
# Table structure for table `poll_data`
#
CREATE TABLE votos_option (
    option_id    INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
    poll_id      MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    option_text  VARCHAR(255)          NOT NULL DEFAULT '',
    option_count SMALLINT(5) UNSIGNED  NOT NULL DEFAULT '0',
    option_color VARCHAR(25)           NOT NULL DEFAULT '',
    PRIMARY KEY (option_id),
    KEY poll_id (poll_id)
)
    ENGINE = ISAM;
# --------------------------------------------------------
#
# Table structure for table `mpn_poll_desc`
#
CREATE TABLE votos_desc (
    poll_id     MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    question    VARCHAR(255)          NOT NULL DEFAULT '',
    description TINYTEXT              NOT NULL DEFAULT '',
    user_id     INT(5) UNSIGNED       NOT NULL DEFAULT '0',
    start_time  INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    end_time    INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    votes       SMALLINT(5) UNSIGNED  NOT NULL DEFAULT '0',
    voters      SMALLINT(5) UNSIGNED  NOT NULL DEFAULT '0',
    multiple    TINYINT(1) UNSIGNED   NOT NULL DEFAULT '0',
    display     TINYINT(1) UNSIGNED   NOT NULL DEFAULT '0',
    weight      SMALLINT(5) UNSIGNED  NOT NULL DEFAULT '0',
    mail_status TINYINT(1) UNSIGNED   NOT NULL DEFAULT '0',
    PRIMARY KEY (poll_id),
    KEY end_time (end_time),
    KEY display (display)
)
    ENGINE = ISAM;
# --------------------------------------------------------
#
# Table structure for table `poll_log`
#
CREATE TABLE votos_log (
    log_id    INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
    poll_id   MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    option_id INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    ip        CHAR(15)              NOT NULL DEFAULT '',
    user_id   INT(5) UNSIGNED       NOT NULL DEFAULT '0',
    time      INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    PRIMARY KEY (log_id),
    KEY poll_id_user_id (poll_id, user_id),
    KEY poll_id_ip (poll_id, ip)
)
    ENGINE = ISAM;
# --------------------------------------------------------
INSERT INTO votos_desc
VALUES (1, 'What do you think about XOOPS?', 'A simple survey about the content management script used on this site.', 1, 1020447898, 1051983686, 0, 0, 0, 1, 0, 0);
INSERT INTO votos_option
VALUES (1, 1, 'Excellent!', 0, 'aqua.gif');
INSERT INTO votos_option
VALUES (2, 1, 'Cool', 0, 'blue.gif');
INSERT INTO votos_option
VALUES (3, 1, 'Hmm..not bad', 0, 'brown.gif');
INSERT INTO votos_option
VALUES (4, 1, 'What the hell is this?', 0, 'darkgreen.gif');
