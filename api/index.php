<?php

//session_start();

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$app = new \Slim\Slim(); //using the slim API

$app->get('/getIngredient', 'getIngredient'); //B public 
$app->get('/getResult', 'getResult'); 
$app->get('/getRecipe', 'getRecipe');

$app->post('/login', 'login');
$app->post('/register', 'register');


$app->run();

//session_destroy();

function getConnection() {
    $dbConnection = new mysqli('localhost', 'root', 'root', 'PantryQuest'); //put in your password
    // Check mysqli connection
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
  }
    return $dbConnection;
}

/*
function login()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();
    
    $query = "select * from users where id = '";
    $query = $query.$_POST['username']."' and pw = '".$_POST['pw']."'";
    $result = $con->query($sql);
  
    if (mysql_num_rows($result) == 0)
    {
        $_SESSION['id'] = false;
        //INVALID LOGIN
    }
    else
    {
        $_SESSION['id'] = true;
        $_SESSION['username'] = $_POST['username'];
        $information[] = $_POST['username'];
    }
    echo json_encode($information);
}

function addUser()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();
    
    $userExists = FALSE;
    
    $sql = "select * from users where username = '".$_POST['username']."'";
    
    $stmt = $con->prepare("INSERT into table users value (?,?,?)");
    $stmt->bindParam('sss', $_POST['username'], $_POST['email'], $_POST['pw']);
}
*/
function getRecipe()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $results = array();
    $rows = array();

    //replace + with space 
    $recipeName = $_GET['recipeName']; 
    
    $sql = "select recipeName, instruction, time, rating, ingredients, picture, calories from recipe natural join filter where recipeName = '".$recipeName."'"; 
    $result = $con->query($sql);
    
    if (mysqli_num_rows($result) != 0)
    {
        $results = mysqli_fetch_assoc($result);
    }
    echo json_encode($results);

}

function getIngredient() {
    $con = getConnection();
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();

    $ingredient_list = array();
    $result = $con->query("SELECT * FROM ingredient");
    while ($rows = mysqli_fetch_row($result)) 
    {
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
    $calories = array();
    $counter = 0;
    $rows = array();
    $results = array();

    //store all information from json, input from user 
    foreach ($_GET as $part)
    {
        if(array_key_exists("ing", $part ))
        {   
            $ingredients[] = $part['ing'];
            
            
            //increment the nuber of times that ingredient is searched for
            $stmt = "select timesSearched from ingredient where foodName = '".$part['name']."'";
            $result1= $con->query($stmt);
            $row = mysqli_fetch_row($result1);
            $timesSearched = $row[0]; //save the ranking points
            $timesSearched = $timesSearched + 1;
            $sql2 = "UPDATE ingredient SET timesSearched = ".$timesSearched." where foodName = '".$part['name']."'";
            $con->query($sql2);
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
        }
            if(array_key_exists("noingredient", $part ))
        {
            $noIngredients[] = $part['noingredient'];
        }  
            if(array_key_exists("calories", $part ))
        {
            $calories[] = (int)$part['calories'];
        } 
    }
    //create all possible subsets of the ingredients 
    $subset = createSubSet($ingredients);
    
    //insert and search for all subsets 
    foreach ($subset as $part)
    {
        searchDB($filters, $part, $methods, $time, $calories);
    }

    $result= $con->query("select recipeName, time, recipe.rating, rankingPoints from recipe inner join  results on results.recipeID =  recipe.recipeID order by rankingPoints asc"); //execute query 
    
    if (mysqli_num_rows($result) != 0)
    {
            //store information in results
   	    while($r = mysqli_fetch_assoc($result)) 
   	    {
            $results[] = $r;
            //$points [] = $
        } 
    }

    //send back a json
    echo json_encode($results);
    mysqli_close($con);
}

//function that creates a query 
function searchDB($filters, $ingredients, $methods, $time, $calories)
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
        $sql = $sql.$time[0];
    }
        if(!empty($calories))
    {
        $sql = $sql." and calories < ";
        $sql = $sql.$calories[0];
    }

    SearchInsert($sql, $ingredients); //call search and insert 

}

//serach the table and 
function searchInsert($sql, $ingredients)
{
    $con = getConnection();
    $result= $con->query($sql);
    $sql = $con->prepare("INSERT INTO results(recipeID, rankingPoints) values (?,?)");

    if (mysqli_num_rows($result) > 0) 
   	{
        while($r = mysqli_fetch_array($result)) 
   	    {   
            $recipeID = $r[0]; //get the id from the result
            $ingredientPoints = 0;
           // echo $recipeID;
            //calculate the rating points for that recipe 
            $stmt = "select sum(value) from recipeConnection where recipeID = ".$recipeID;
            $result2= $con->query($stmt);
            $row = mysqli_fetch_row($result2);
            $totalPoints = $row[0]; //save the ranking points
            
            
            
            foreach ($ingredients as $ingredient)
            {
                $stmt = "select value from recipeConnection where recipeID = ".$recipeID." and foodName = '".$ingredient."'";
            $result1= $con->query($stmt);
            $row = mysqli_fetch_row($result1);
            $ingredientPoints = ($ingredientPoints + $row[0]);
            }
           
            $ranking = $ingredientPoints / $totalPoints;
            
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