<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$app = new \Slim\Slim(); //using the slim API

$app->get('/getIngredient', 'getIngredient'); //B public 
$app->get('/getResult', 'getResult'); //end session and log out user 
// $app->get('/getRecipe', 'getRecipe');


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
    $mysqli = getConnection();
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();

    $ingredient_list = array();
    $result = mysqli_query($mysqli, "SELECT * FROM ingredient");
    while ($rows = mysqli_fetch_row($result)) {
        $ingredient_list[] = $rows;
    }

    echo json_encode($ingredient_list);
}

function getResult() {
	$con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    
    //create variables to store information
    $result = json_decode($request, true);
    $ingredients = array();
    $filters = array();
    $methods = array();
    $noIngredients = array();
    $results = array();
    $time;
    $counter = 0;

    //store all information from json, input from user 
    foreach ($result as $part)
    {

        if(array_key_exists("name", $part ))
        {
            $ingredients[] = $part['name'];
            //echo $part['name'];
        }
            if(array_key_exists("filter", $part ))
        {
            $filters[] = $part['filter']; 
                //echo $part['filter']; 
        }
            if(array_key_exists("method", $part ))
        {
            $methods[] = $part['method'];
               // echo $part['method'];
        }
            if(array_key_exists("time", $part ))
        {
            $time = (int)$part['time'];
            $hasTime = true;
            //echo $time;
        }
            if(array_key_exists("noingredient", $part ))
        {
            $noIngredients[] = $part['noingredient'];
        }  
    }


    $result= $con->query(searchDB($filters, $ingredients, $methods, $time)); //execute query 
    
    if (mysqli_num_rows($result) == 0)
    {
        //this means there are no matches, need to re-search with fever ingredients
        echo json_encode($rows);
        exit;
    }
 
   	while($r = mysqli_fetch_assoc($result)) 
   	{
         $results[] = $r;
   	} 
    echo json_encode($results);
    mysqli_close($con);
}

//function that creates a query 
function searchDB($filters, $ingredients, $methods, $time)
{
    //create query with all information 
    //select distinct recipeName, ranking from recipe natural join filter natural join recipeConnection where vegetarian and foodName = 'egg' order by 'ranking' asc;
    $sql = "select distinct recipeName, ranking from recipe natural join filter natural join recipeConnection where "; //check if you need ''

    foreach ($filters as $filter)
    {
        $sql = $sql.$filter." and ";
    }

    foreach ($ingredients as $ingredient)
    {
        if($counter == 0)
        {
            $sql = $sql."foodName = '";
            $sql = $sql.$ingredient."'";
        }
        else 
        {
            $sql = $sql." and foodName = '";
            $sql = $sql.$ingredient."'";
        }
        $counter++;
    }

    foreach ($methods as $method)
    {
        $sql = $sql." and method = '";
        $sql = $sql.$method."'";
    }

    if(!empty($time))
    {
        $sql = $sql." and time < ";
        $sql = $sql.$time;
    }

    $sql = $sql." order by 'ranking' asc";
    return $sql;
}