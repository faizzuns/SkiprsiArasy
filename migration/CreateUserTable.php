<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 9:45 AM
 */

require "core/config.php";

$conn  = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("CREATE TABLE user (
                                  id INT AUTO_INCREMENT PRIMARY KEY, 
                                  name VARCHAR(30), 
                                  age INT,
                                  profession VARCHAR(20),
                                  education VARCHAR(20),
                                  phone VARCHAR(15),
                                  province VARCHAR(30),
                                  regency VARCHAR(30),
                                  village VARCHAR(15))");

$stmt->execute();

$stmt = $conn->prepare("CREATE TABLE answersheet (
                                  id INT AUTO_INCREMENT PRIMARY KEY,
                                  user_id INT,
                                  tendency_1 INT,
                                  tendecy_2 INT,
                                  id_news INT,
                                  termo_1 INT,
                                  termo_2 INT,
                                  dbrief_1 INT,
                                  dbrief_2 INT,
                                  distract INT,
                                  correction INT,
                                  first_pick VARCHAR(15))");
$stmt->execute();

$stmt = $conn->prepare("CREATE TABLE question
                                  (id INT AUTO_INCREMENT PRIMARY KEY,
                                  user_id INT,
                                  question_number INT,
                                  question_answer INT
                                  )");
$stmt->execute();