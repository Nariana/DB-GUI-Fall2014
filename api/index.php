<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$app = new \Slim\Slim(); //using the slim API

$app->get('/getIngredient', 'getIngredient'); //B public 
$app->get('/getResult', 'getResult'); //end session and log out user 
$app->get('/getRecipe', 'getRecipe');


$app->run();

function getConnection() {
	$dbConnection = new mysqli("localhost", "root", "root", "PantryQuest"); //put in your password
  // Check mysqli connection
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }
  return $dbConnection;
}

function getIngredient() {
	$con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();

    $query = "select * from ingredient";
    $ingredient_list = $con->query($query);
    echo json_encode($ingredient_list);
}