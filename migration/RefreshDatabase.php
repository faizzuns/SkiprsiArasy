<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/15/2019
 * Time: 11:37 AM
 */
require "core/config.php";

$conn  = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("DELETE FROM question;");
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM answersheet;");
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM `user`;");
$stmt->execute();