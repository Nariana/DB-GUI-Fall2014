<?php

session_start();

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$app = new \Slim\Slim(); //using the slim API

$app->get('/getIngredient', 'getIngredient'); //B public 
$app->get('/getResult', 'getResult'); 
$app->get('/getRecipe', 'getRecipe');

$app->get('/saveRecipe', 'saveRecipe');
$app->get('/getAnalytics', 'getAnalytics');
$app->get('/updateRating', 'updateRating');
//$app->get('/showFavorite', 'showFavorite');


$app->post('/login', 'login');
$app->post('/register', 'register');
$app->post('/logout', 'logout');


$app->run();

session_destroy();

function getConnection() {
    $dbConnection = new mysqli('localhost', 'root', 'root', 'PantryQuest'); //put in your password
    // Check mysqli connection
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
  }
    return $dbConnection;
}

function logout()
{
    session_destroy();
}

function getAnalytics() {
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $foodNames = array();
    $mostSaved = array ();
    $mostClicked = array();
    $return = array();
    $counter = 0;
    try
    {
        $con = getConnection();
        
    //show the 5 most searched for ingredients 
        //don't need to make injection safe because the user is not inputed query 
        $stmt = "select foodName from ingredient order by timesSearched desc";
        $result= $con->query($stmt);
        if (!$result)
        {
            throw new Exception(mysqli_error($con));
        }
    
        if (mysqli_num_rows($result) != 0)
        {
        //store information in results
   	        while($counter < 6) 
   	        {
                $foodNames[] = $mysqli_fetch_assoc($result);
                $counter = $counter + 1 ;
            } 
        }
    //show the 5 most clicked recipes 
        $counter = 0;
        //don't need to make injection safe because the user is not inputed query 
        $stmt = "select recipeName from recipe order by timesClicked desc";
        $result1= $con->query($stmt);
        if (!$result1)
        {
            throw new Exception(mysqli_error($con));
        }
    
        if (mysqli_num_rows($result1) != 0)
        {
        //store information in results
   	        while($counter < 6) 
   	        {
                $mostClicked[] = $mysqli_fetch_assoc($result1);
                $counter = $counter + 1 ;
            } 
        }
    //show the 5 most saved recipes 
        $counter = 0;
        //don't need to make injection safe because the user is not inputed query 
        $stmt = "select recipeName from recipe order by timesSaved desc";
        $result2= $con->query($stmt);
        if (!$result2)
        {
            throw new Exception(mysqli_error($con));
        }
    
        if (mysqli_num_rows($result2) != 0)
        {
        //store information in results
   	        while($counter < 6) 
   	        {
                $mostSaved[] = $mysqli_fetch_assoc($result2);
                $counter = $counter + 1 ;
            } 
        }
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
    $return[] = $foodNames;
    $return[] = $mostClicked;
    $return[] = $mostSaved;
    echo json_encode($return);
    
}



function saveRecipe()
{
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $recipeName = $_GET['recipeName']; 
    $result = array();
    try
    {
        $con = getConnection();
        $recipeName = $_GET['recipeName']; 
        
        if ($_SESSION['id'] == 1)
        {
        //get the recipe ID
        $stmt =$con->prepare("select recipeID from recipe where recipeName = ? ");
        $stmt->bind_param('s', $recipeName);
        $result1= $stmt->execute();
        if (!$result1)
        {   
            throw new Exception(mysqli_error($con));
        }
        $row = mysqli_fetch_row($result1);
        $id = $row[0]; 
        
        //prepare statement 
            $sql = $con->prepare("INSERT INTO savedRecipes(username, id) values (?, ?)");    
            $sql->bind_param('ss', $_SESSION['username'], $id);
            $sql->execute();
            
        //increment number of times that recipe has been saved 
        $stmt = $con->prepare("select timesSaved from recipe where recipeName = ?");
        $stmt->bind_param('s', $recipeName);
        $result2 = $stmt->execute();
                             
        if (!$result2)
        {   
            throw new Exception(mysqli_error($con));
        }
        
        $row = mysqli_fetch_row($result2);
        $timesSaved = $row[0]; //save the ranking points
        $timesSaved= $timesSaved + 1;
                             
                             
        $sql2 = $con->prepare("UPDATE recipe SET timesSaved = ? where recipeName = ? ");
        $sql2->bind_param('is', $timesSaved, $recipeName);
        $sql2->execute();                
        
        }
        else
        {
            //REDIRECT TO SOMEWHERE ELSE 
            //you are not logged in so you cannot save a recipe 
        }
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
}

function updateRating()
{
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    //save recipename 
    $recipeName = $_GET['recipeName']; 
    try
    {
        $con = getConnection();
        //get current rating 
        $stmt = $con->prepare("select rating from recipe where recipeName = ? ");
        $stmt->bind_param('s', $recipeName);
        $result2= $stmt->execute();
        if (!$result2)
        {   
            throw new Exception(mysqli_error($con));
        }
        
        $row = mysqli_fetch_row($result2);
        $rating = $row[0]; //save the ranking
        $rating= $rating + 1; //update ranking 
        $sql2 = $con->prepare("UPDATE recipe SET rating = ? where recipeName = ? ");
        $sql2->bind_param('is', $rating, $recipeName);
        $sql2->execute(); 
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
}
function login()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();
    
    $query = $con->prepare("select * from users where id = ? and pw = ? ");
    $pwmd5 = md5($_POST['pw']);
    $query->bind_param('ss', $_POST['username'], $pwmd5);
    $result = $query->execute();
  
    if (!$result)
    {
        throw new Exception(mysqli_error($con));
    }
    if (mysql_num_rows($result) == 0)
    {
        $_SESSION['id'] = false;
        //INVALID LOGIN
    }
    else
    {
        $_SESSION['id'] = true;
        $_SESSION['username'] = $_POST['username'];
        //return name as well 
        $information[] = $_POST['username'];
        $information[] = mysqli_fetch_assoc($result);
    }
    echo json_encode($information);
}

function register()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();
    
    $userExists = FALSE;
    
    $sql = $con->prepare("select * from users where username = ?");
    $sql->bind_param('s', $_POST['username']);
    $sql->execute();
    
    //check if it is empty
    $resultCheck = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($accountCheck)) {
            $userExists = TRUE;
    }
    
    if($userExists == FALSE)
    {
        $stmt = $con->prepare("INSERT into users (username, name, pw) value (?,?,?)");
        $pwmd5 = md5($_POST['pw']);
        $stmt->bindParam('sss', $_POST['username'], $_POST['name'], $pwmd5);
        $stmt->execute();
    }
}


function getRecipe()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    try
    {
    $results = array();
    $rows = array();

    //replace + with space 
    $recipeName = $_GET['recipeName']; 
    
    $sql = $con->prepare("select recipeName, instruction, time, rating, ingredients, picture, calories from recipe natural join filter where recipeName = ?");
    $sql->bind_param('s', $recipeName);
        
    $result = $sql->execute();
    
    if (mysqli_num_rows($result) != 0)
    {
        $results = mysqli_fetch_assoc($result);
    }
    
     //increment the nuber of times that recipe has been selected 
        $stmt = $con->prepare("select timesClicked from recipe where recipeName = ? ");
        $stmt->bind_param('s', $recipeName);
        $result2= $stmt->execute();
        if (!$result2)
        {   
            throw new Exception(mysqli_error($con));
        }
        
        $row = mysqli_fetch_row($result2);
        $timesClicked = $row[0]; //save the ranking
        $timesClicked= $timesClicked + 1; //update ranking 
        $sql2 = $con->prepare("UPDATE recipe SET timesClicked = ? where recipeName = ? ");
        $sql2->bind_param('is', $timesClicked, $recipeName);
        $sql2->execute(); 
        
    }// end try block 
    catch (Exception $e)
    {
        $e->getMessage();
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
    
    try
    {
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
    
    //echo print_r($_GET);

    //store all information from json, input from user 
    foreach ($_GET as $part)
    {
        if(array_key_exists("ing", $part ))
        {   
            $ingredients[] = $part['ing'];
            
            
            //increment the nuber of times that ingredient is searched for
        $stmt = $con->prepare("select timesSearched from ingredient where foodName = ? ");
        $stmt->bind_param('s', $part['ing']);
        $result1= $stmt->execute();
        if (!$result2)
        {   
            throw new Exception(mysqli_error($con));
        }
        
        $row = mysqli_fetch_row($result1);
        $timesSearched = $row[0]; //save the ranking points
        $timesSearched = $timesSearched + 1;
        $sql2 = $con->prepare("UPDATE ingredient SET timesSearched = ? where foodName = ? ");
        $sql2->bind_param('is', $timesSearched, $part['ing']);
        $sql2->execute(); 

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

    $result= $con->query("select recipeName, time, recipe.rating, rankingPoints, calories from recipe inner join  results on results.recipeID =  recipe.recipeID inner join filter on results.recipeID = filter.recipeID order by rankingPoints desc"); //execute query 
    
    if (!$result)
    {
        throw new Exception(mysqli_error($con));
    }
    
    if (mysqli_num_rows($result) != 0)
    {
            //store information in results
   	    while($r = mysqli_fetch_assoc($result)) 
   	    {
            $results[] = $r;
            //$points [] = $
        } 
    }
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
    //send back a json
    echo json_encode($results);
    mysqli_close($con);
}

//HOW TO MAKE   this safe 
//function that creates a query 
function searchDB($filters, $ingredients, $methods, $time, $calories)
{
    $counter = 0;
    $counter1 = 0;
    
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
        if(empty($ingredients) && empty($filters))
        {
            $sql = $sql." method = '";
            $sql = $sql.$method."'";
            continue;
        }
            
        if($counter1 == 0)
        {
            $sql = $sql." and method = '";
            $sql = $sql.$method."'";
        }
        else 
        {
            $sql = $sql." or method = '";
            $sql = $sql.$method."'";
        }
        $counter1 = $counter1 + 1;
    }

    if(!empty($time))
    {
        if(empty($ingredients) && empty($filters) && empty($methods))
        {
        $sql = $sql." time < ";
        $sql = $sql.$time[0];
        }
        else 
        {
        $sql = $sql." and time < ";
        $sql = $sql.$time[0];
        }
    }
    if(!empty($calories))
    {
        if(empty($ingredients) && empty($filters) && empty($methods))
        {
        $sql = $sql." calories < ";
        $sql = $sql.$calories[0];
        }
        else
        {
        $sql = $sql." and calories < ";
        $sql = $sql.$calories[0];
        }
    }
   // echo $sql;
    SearchInsert($sql, $ingredients); //call search and insert 
}

//serach the table and 
function searchInsert($sql, $ingredients)
{
    $con = getConnection();
    try
    {
        $result= $con->query($sql);
        $sql = $con->prepare("INSERT INTO results(recipeID, rankingPoints) values (?,?)");

     if (!$result)
    {
        throw new Exception(mysqli_error($con));
    }

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
}//end try block 
    catch(Exception $e)
    {
        echo $e->getMessage();
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