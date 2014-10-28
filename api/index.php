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

function getRecipe()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $result = json_decode($request, true);
    
    $recipeName = $result['recipeName'];
    $results = array();
    //get the name they are sending us 
    
    $sql = "SELECT * FROM recipes where recipeName = '".$recipeName."'"; 
    $con->query($sql);
  
    while ($rows = mysqli_fetch_row($result)) {
        $results[] = $rows;
    }
    echo json_encode($results);

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
    //epty previous table 
    $sql = "Truncate TABLE results";
    $con->query($sql);


    //create variables to store information
    //$result = json_decode($_GET, true);
    
    $ingredients = array();
    $filters = array();
    $methods = array();
    $noIngredients = array();
    $results = array();
    $points = array();
    $time = array();
    $counter = 0;
    $points = array();

    //store all information from json, input from user 
    foreach ($_GET as $part)
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
    //create all possible subsets of the ingredients 
    $subset = createSubSet($ingredients);
    
    //insert and search for all subsets 
    foreach ($subset as $part)
    {
        searchDB($filters, $part, $methods, $time);
    }

    $result= $con->query("select recipeName, ranking, time from recipe where recipeID in (select recipeID from results)"); //execute query 
    if (mysqli_num_rows($result) == 0)
    {
        //no possible resuts 
        echo json_encode($rows);
        exit;
    }
    //store information in results
   	while($r = mysqli_fetch_assoc($result)) 
   	{
        $results[] = $r;
        //$points [] = $
   	} 
    
    //get the ranking points for each recipe 
    $result = $con->query("select rankingPoints from results");
   	while($r = mysqli_fetch_assoc($result)) 
   	{
         $points[] = $r;
   	} 
    
    $results[] = $points;
    //send back a json
    echo json_encode($results);
    mysqli_close($con);
}

//function that creates a query 
function searchDB($filters, $ingredients, $methods, $time)
{
    $counter = 0;
    //create query with all information 
    //select distinct recipeName, ranking from recipe natural join filter natural join recipeConnection where vegetarian and foodName = 'egg' order by 'ranking' asc;
    $sql = "select distinct recipeID from recipe natural join filter natural join recipeConnection where "; //check if you need ''

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
   
    //return $sql;
    SearchInsert($sql); //call search and insert 

}

//serach the table and 
function searchInsert($sql)
{
    $con = getConnection();
    $result= $con->query($sql);
    $sql = $con->prepare("INSERT INTO results(recipeID, rankingPoints) values (?,?)");

    if (mysqli_num_rows($result) > 0) 
   	{
        while($r = mysqli_fetch_array($result)) 
   	    {   
            $recipeID = $r[0]; //get the id from the result
           // echo $recipeID;
            //calculate the rating points for that recipe 
            $stmt = "select sum(value) from recipeConnection where recipeID = ".$recipeID;
            $result= $con->query($stmt);
            $row = mysqli_fetch_row($result);
            $ratio = $row[0]; //save the ranking points
            //find total number fo ingredient 
            $stmt = "select numberOfIngredients from recipe where recipeID = ".$recipeID;
            
            $result= $con->query($stmt);
            $row = mysqli_fetch_row($result);
            $totalNum = 10 * $row[0]; //save the ranking points
            
            $ranking = $ratio / $totalNum;
            
            $sql->bind_param('id', $recipeID, $ranking);
            $sql->execute();
        }
    }
    else 
    {
        //return if you have no matches 
        return;
    }
}

//create a list of subsets og a set as aelements in a set and call search for all of them 
function createSubSet($in,$minLength = 1)
{ 
   $count = count($in); 
   $members = pow(2,$count); 
   $return = array(); 
   for ($i = 0; $i < $members; $i++) { 
      $b = sprintf("%0".$count."b",$i); 
      $out = array(); 
      for ($j = 0; $j < $count; $j++) { 
         if ($b{$j} == '1') $out[] = $in[$j]; 
      } 
      if (count($out) >= $minLength) { 
         $return[] = $out; 
      } 
   } 
    return $return; 
}