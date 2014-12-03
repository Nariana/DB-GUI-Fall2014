<?php
session_start();
//Initialize session ID
$_SESSION['id'];

//Create users for custom DB access
$_SESSION['notLoggedInUsername'] = 'unLoggedIn';
$_SESSION['loggedInUsername'] = 'loggedIn';
$_SESSION['notLoggedInPW'] = '123';
$_SESSION['loggedInPW'] = '123';
$_SESSION['timestamp'] = 0;

//Slim Framwork initialization
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$app = new \Slim\Slim(); //using the slim API


//Get requests
$app->get('/getIngredient', 'getIngredient');
$app->get('/getResult', 'getResult');
$app->get('/getRecipe', 'getRecipe');
$app->get('/saveRecipe', 'saveRecipe');
$app->get('/getAnalytics', 'getAnalytics');
$app->get('/deleteFavorites', 'deleteFavorites');
$app->get('/displayFavorites', 'displayFavorites');

//post requests 
$app->post('/login', 'login');
$app->post('/register', 'register');
$app->post('/logout', 'logout');

$app->run();

//get DB connection, default root access.
function getConnection($user = 'root', $pw = 'root', $host = 'localhost') 
{
    $dbConnection = new mysqli($host, $user, $pw, 'PantryQuest'); 
    
    // Check mysqli connection
    if (mysqli_connect_errno()) 
    {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    return $dbConnection;
}

//Funtion to resutn all the ingredients in the DB
function getIngredient() 
{
    //get DB connection with custom access 
    $con = getConnection($_SESSION['notLoggedInUsername'], $_SESSION['notLoggedInPW']);
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    
    //initialise list 
    $ingredient_list = array();
    
    //query DB 
    $result = $con->query("SELECT * FROM ingredient");
    
    while ($rows = mysqli_fetch_row($result)) 
    {
        $ingredient_list[] = $rows;
    }
    //return the result 
    echo json_encode($ingredient_list);
    $con->close();
}// end function getIngredient 

//logout user
function logout()
{
    //destroy session and reset session array 
    session_destroy();
}

//unfavorite a recipe 
function deleteFavorites()
{
        try
    {
            //get custom connection as logged in user 
        $con = getConnection($_SESSION['loggedInUsername'], $_SESSION['loggedInPW']);
        $recipeName = $_GET['recipeName'];
        
            //get the recipeId of the current recipe 
            $tempID;
            $result = $con->prepare("SELECT recipeID FROM recipe WHERE recipeName = ?");
            $result->bind_param('s', $recipeName);
            $result->execute();
            $result->bind_result($tempID);
            $recipeID;
            while ($result->fetch()) 
            {
                $recipeID = $tempID;
            }
            
            //check if the recipe is saved by that user 
            $tempCount;
            $result = $con->prepare("SELECT COUNT(*) FROM searchHistory WHERE username = ? AND id = ?");
            $result->bind_param('si', $_SESSION['username'], $recipeID);
            $result->execute();
            $result->bind_result($tempCount);
            $count;
            while ($result->fetch())
            {
                $count = $tempCount;
            }

            if ($count != 0) //check if it has actually been saved by you 
            {
                //prepare statement 
                $sql = $con->prepare("DELETE FROM searchHistory WHERE username = ? AND id = ?");    
                $sql->bind_param('si', $_SESSION['username'], $recipeID);
                $sql->execute();

                //get current number of times that recipe has been saved 
                $stmt = $con->prepare("SELECT rating FROM recipe WHERE recipeName = ?");
                $stmt->bind_param('s', $recipeName);
                $stmt->execute(); 
                $rating;
                $stmt->bind_result($rating);
                while ($stmt->fetch())
                {
                    $rating = $rating - 1;    
                }   
                //decrement ranking 
                    $sql2 = $con->prepare("UPDATE recipe SET rating = ? where recipeName = ? ");
                    $sql2->bind_param('is', $rating, $recipeName);
                    $sql2->execute();        
                }
            //close connection
            $con->close();

    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
    
}//end functon

//this function returns basic analytics from the applications 
function getAnalytics() 
{
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    //initialize arrays 
    $foodNames = array();
    $mostSaved = array ();
    $mostClicked = array();
    $return = array();
    $counter = 0;
    
    try
    {
        //get custom DB connection
        $con = getConnection($_SESSION['notLoggedInUsername'], $_SESSION['notLoggedInPW']);
        
        //show the 5 most searched for ingredients 
        //don't need to make injection safe because the user is not inputed query 
        $stmt = "select foodName, timesSearched from ingredient order by timesSearched desc";
        $result= $con->query($stmt);
        if (!$result)
        {
            throw new Exception(mysqli_error($con));
        }
    
        if (mysqli_num_rows($result) != 0)
        {
        //store information in results
   	        while($counter < 5 && $r = mysqli_fetch_assoc($result)) 
   	        {
                $foodNames[] = $r;
                $counter += 1 ;
            } 
        }
        
        //show the 5 most clicked recipes 
        $counter = 0;
        //don't need to make injection safe because the user is not inputed query 
        $stmt = "select recipeName, timesClicked from recipe order by timesClicked desc";
        $result1= $con->query($stmt);
        if (!$result1)
        {
            throw new Exception(mysqli_error($con));
        }
    
        if (mysqli_num_rows($result1) != 0)
        {
        //store information in results
   	        while($counter < 5 && $r = mysqli_fetch_assoc($result1)) 
   	        {
                $mostClicked[] = $r;
                $counter = $counter + 1 ;
            } 
        }
        //show the 5 most saved recipes 
        $counter = 0;
        //don't need to make injection safe because the user is not inputed query 
        $stmt = "select recipeName, rating from recipe order by rating desc";
        $result2= $con->query($stmt);
        if (!$result2)
        {
            throw new Exception(mysqli_error($con));
        }
    
        if (mysqli_num_rows($result2) != 0)
        {
        //store information in results
   	        while($counter < 5 && $r = mysqli_fetch_assoc($result2)) 
   	        {
                $mostSaved[] = $r;
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
    //return array to fron-end return empty array if nothing is saved and searched for
    echo json_encode($return);
    
}//end fucntion

//this function saves a recipe 
function saveRecipe()
{
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    
    try
    {
        //get custom DB connection
        $con = getConnection($_SESSION['loggedInUsername'], $_SESSION['loggedInPW']);
        $recipeName = $_GET['recipeName'];
        
            //retrive the correspoding recipeId to that the recipeName 
            $tempID;
            $result = $con->prepare("SELECT recipeID FROM recipe WHERE recipeName = ?");
            $result->bind_param('s', $recipeName);
            $result->execute();
            $result->bind_result($tempID);
            $recipeID;
            while ($result->fetch()) 
            {
                $recipeID = $tempID;
            }
            $tempCount;
            $result = $con->prepare("SELECT COUNT(*) FROM searchHistory WHERE username = ? AND id = ?");
            $result->bind_param('si', $_SESSION['username'], $recipeID);
            $result->execute();
            $result->bind_result($tempCount);
            $count;
            while ($result->fetch())
            {
                $count = $tempCount;
            }

            if ($count == 0) //ensure that each recipe can only be saved once by checking if it has already been saved by that user 
            {
                //prepare statement 
                $sql = $con->prepare("INSERT INTO searchHistory (username, id) values (?, ?)");    
                $sql->bind_param('si', $_SESSION['username'], $recipeID);
                $sql->execute();
                
                

                //retrive current rating 
                $stmt = $con->prepare("select rating from recipe where recipeName = ?");
                $stmt->bind_param('s', $recipeName);
                $stmt->execute(); 
                $rating;
                $stmt->bind_result($rating);
                //incrment rating 
                while ($stmt->fetch())
                {
                    $rating = $rating + 1;    
                }   
                    $sql2 = $con->prepare("UPDATE recipe SET rating = ? where recipeName = ? ");
                    $sql2->bind_param('is', $rating, $recipeName);
                    $sql2->execute();        
                }
        $con->close();

    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
}//end function


//this function validates a login attempt 
function login()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();

    //encode types password so that it matches encryption
    $decodedPW = base64_encode($_POST['pw']);
    
    $name = $_POST['name'];


    $query = $con->prepare("select firstname from users where username = ? and pw = ?");
     
    $query->bind_param('ss', $name, $decodedPW);
    $query->execute();
    $query->bind_result($temp);
    $firstname;
    
    //attempt to retrive first name from that user 
    while ($query->fetch())
    {
        $firstname = $temp;
    }
    
    //invalis login if firstName is not set 
    if (!isset($firstname))
    {
        $information[] = "Invalid login";
    }
    else
    {
        //valid login return username and name 
            $_SESSION['id'] = true;
            $_SESSION['username'] = $name;
            $information[] = $name;
            $information[] = $firstname;
    }
    
    //return infromation
    echo json_encode($information);
}//end funtion

//this function registers a new user 
function register()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();
    
    //default that the user doe not exist 
    $userExists = FALSE;
    
    $sql = $con->prepare("select * from users where username = ?");
    $sql->bind_param('s', $_POST['username']);
    $sql->execute();
    $sql->store_result();
    
    //chech if the username is already taken 
    if ($sql->num_rows != 0) {
        $userExists = TRUE;
    }
    
    $con->close();
    $con = getConnection();
    
    //only create a new user if it doesnt exist prior 
    if($userExists == FALSE)
    {
        $stmt = $con->prepare("INSERT into users (username, firstname, pw) values (?,?,?)");
        
        //encrypt typed password 
        $pwmd5 = base64_encode($_POST['pw']);

        $stmt->bind_param('sss', $_POST['username'], $_POST['name'], $pwmd5);
           
        $stmt->execute();
        
        //store username and name 
        $information[] = $_POST['username'];
        $information[] = $_POST['name'];
        $_SESSION['username'] = $_POST['username'];
        $con->close();
    }
    else
    {
        //if the user already exsts return that information
        $information[] = "User already exists";
    }
    
    //return information
    echo json_encode($information);
}
/*
NEED TO COMMENT FROM HERE 
*/

function getRecipe()
{

    $con = getConnection($_SESSION['loggedInUsername'], $_SESSION['loggedInPW']);
	$app = \Slim\Slim::getInstance();
    $timesClicked;
    
    try
    {
    $results = array();
    $rows = array();

    //replace + with space 
    $recipeName = $_GET['recipeName']; 
        
             //increment the nuber of times that recipe has been selected 
        $stmt = $con->prepare("select timesClicked from recipe where recipeName = ? ");
        $stmt->bind_param('s', $recipeName);
        $stmt->execute();
        $stmt->bind_result($timesClicked);
        $timesClicked;
        while ($stmt->fetch())
        {
            $timesClicked = $timesClicked + 1;
        }
            $sql2 = $con->prepare("UPDATE recipe SET timesClicked = ? where recipeName = ? ");
            $sql2->bind_param('is', $timesClicked, $recipeName);
            $sql2->execute(); 
        
   $sql = "select recipeName, instruction, time, rating, ingredients, picture, calories from recipe natural join filter where recipeName = '".$recipeName."'"; 
    $result = $con->query($sql);

    if (mysqli_num_rows($result) != 0)
    {
        $results = mysqli_fetch_assoc($result);
    }
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
    
    
    

        //echo print_r($results);
    echo json_encode($results);
}

function getResult() {
    
    
	$con = getConnection($_SESSION['loggedInUsername'], $_SESSION['loggedInPW']);
	$app = \Slim\Slim::getInstance();
    //create variables to store information

    $ingredients = array();
    $filters = array();
    $methods = array();
    $noIngredients = array();
    $results = array();
    $points = array();
    $time;
    $calories;
    $numberOfIngredients = 0;
    $counter = 0;
    //echo $counter;
    $rows = array();
    $results = array();
    $saved = array();
    $timesSearched;
    
    
    //var_dump($_GET);
    
    //epty previous table 
    try
    {
    $sql = "Truncate TABLE results";
    $con->query($sql);

    //echo print_r($_GET);

    //store all information from json, input from user 
    foreach ($_GET as $part)
    {
        //echo "begining";
        //print_r($part);
        
        if(array_key_exists("ing", $part ))
        {   
            $ingredient = $con->real_escape_string($part['ing']);
            $ingredients[] = $ingredient;
             
            //increment the nuber of times that ingredient is searched for
            
        $query = "select timesSearched from ingredient where foodName = ? ";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $ingredient);
        $stmt->execute();
        $timesSearched;
        $stmt->bind_result($timesSearched);
        while ($stmt->fetch())
            {
                $timesSearched = $timesSearched + 1;
                $timesSearched = (int)$timesSearched;
            }
            
            $q = "UPDATE ingredient SET timesSearched = ? where foodName = ? ";
            $sql = $con->prepare($q);
            $sql1 = $con->prepare($q);
            $sql1->bind_param('is', $timesSearched, $ingredient);
            $sql1->execute(); 
            //echo $ingredient;

        }
        
            if(array_key_exists("restriction", $part ))
        {
            $filter = $con->real_escape_string($part['restriction']);
            $filters[] = $filter; 
                //echo $part['filter']; 
        }
            if(array_key_exists("method", $part ))
        {
            $method = $con->real_escape_string($part['method']);
            $methods[] = $method;
               //echo $part['method'];
        }
            if(array_key_exists("time", $part ))
        {
            $time = $part['time'];
        }
            if(array_key_exists("noning", $part ))
        {
            $noIngredient = $con->real_escape_string($part['noning']);
            $noIngredients[] = $noIngredient;
        }  
            if(array_key_exists("calories", $part ))
        {
                //echo "inside cal";
                //echo $part['calories'];
            $calories = (int)$part['calories'];
        } 
            if(array_key_exists("numberOfIngredients", $part ))
        {
                //echo "inside cal";
                //echo $part['calories'];
            $numberOfIngredients = (int)$part['numberOfIngredients'];
        } 
        
    }
       //echo print_r($ingredients);
    //create all possible subsets of the ingredients 
    $subset = createSubSet($ingredients);

    //insert and search for all subsets 
    foreach ($subset as $part)
    {
        searchDB($filters, $part, $methods, $time, $calories, $noIngredients, $numberOfIngredients);
    }
    if(empty($ingredients))
    {
        //echo "inside"; 
        searchDB($filters, $ingredients, $methods, $time, $calories, $noIngredients, $numberOfIngredients);
    }
    
        
        
    if (isset($_SESSION['id']))
    {
        //echo "inside";
    //check what of the results you have favorited 
    $result1= $con->query("select distinct recipeName from recipe inner join  searchHistory on  recipe.recipeID = searchHistory.ID where username = '".$_SESSION['username']."'"); //execute query 
    
    if (!$result1)
    {
        throw new Exception(mysqli_error($con));
    }
    
    if (mysqli_num_rows($result1) != 0)
    {
            //store information in results
   	    while($r = mysqli_fetch_assoc($result1)) 
   	    {
            $saved[] = $r;
        } 
    }    
    }
      //print_r($saved);             
    $result= $con->query("select distinct recipeName, time, recipe.rating, rankingPoints, calories, picture from recipe inner join  results on results.recipeID =  recipe.recipeID inner join filter on results.recipeID = filter.recipeID order by rankingPoints desc"); //execute query 
        
        //check what of the results you have favorited 
    
    if (!$result)
    {
        throw new Exception(mysqli_error($con));
    }
    
    $issaved = FALSE;
        
    if (mysqli_num_rows($result) != 0)
    {
             //loop through saved to see if a recipe is already saved 
   	    while($r = mysqli_fetch_assoc($result)) 
   	    {
            $issaved = FALSE;
            
            if(!empty($saved))
            {
                foreach ($saved as $recipe)
                {
                    //echo $r['recipeName'];
                    //echo $recipe['recipeName'];
                    if($recipe['recipeName'] == $r['recipeName']) //if that recipe is in the saved list 
                    {
                        $issaved = true; 
                    }
                }
                if($issaved)
                {
                    $r['saved'] = 'true';
                    $results[] = $r;
                }
                else 
                {
                    $r['saved'] = 'false';
                    $results[] = $r;
                }
            }
        
            else
            {
                $r['saved'] = 'false';
                $results[] = $r;
                //$results[] = 'notSaved'; 
            }
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
function searchDB($filters, $ingredients, $methods, $time, $calories, $noIngredients, $numberOfIngredients)
{

    //echo "here";
    
    $counter = 0;
    $counter1 = 0;

    if(empty($filters) && empty($ingredients) && empty($methods) && empty($noIngredients))
    {
        if(!isset($time) && !isset($calories) )
        {
            if ($numberOfIngredients != 0)
            {
                $sql = "select distinct recipeID from recipe natural join filter natural join recipeConnection ";
            }
        }
    }
    //create query with all information 
    //select distinct recipeName, ranking from recipe natural join filter natural join recipeConnection where vegetarian and foodName = 'egg' order by 'ranking' asc;
    $sql = "select distinct recipeID from recipe natural join filter natural join recipeConnection where "; //check if you need ''
    if(!empty($methods))
    {
        $sql = $sql."( ";
    }
    foreach ($methods as $method)
    {

            
        if($counter1 == 0)
        {

            $sql = $sql." method = '";
            $sql = $sql.$method."'";
        }
        else 
        {
            
            $sql = $sql." or method = '";
            $sql = $sql.$method."'";
        }
        $counter1 = $counter1 + 1;
    }
    if(!empty($methods))
    {
        $sql = $sql.") ";
    }
    $methodCount = 0;
    foreach ($filters as $filter)
    {
        if(!empty($methods) && $methodCount == 0)
        {
        $sql = $sql." and ";
        $sql = $sql.$filter." and ";
        }
        else
        {
        $sql = $sql.$filter." and ";
        }
        
    $methodCount = $methodCount + 1;
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
        
        //echo $sql;
    }


    if(isset($time))
    {
        if(empty($ingredients))
        {
        $sql = $sql." time < ";
        $sql = $sql.$time;
        }
        else
        {
             $sql = $sql." and time < ";
            $sql = $sql.$time;
        }
    }
    if(isset($calories))
    {
        if(empty($ingredients) && !isset($time))
        {
        $sql = $sql." calories < ";
        $sql = $sql.$calories;
        }
        else
        {
            $sql = $sql." and calories < ";
            $sql = $sql.$calories;
        }
    }
    if($numberOfIngredients != 0)
    {
        if(empty($ingredients) && empty($filters) && empty($methods))
        {
            if(!isset($time) && !isset($calories) )
            {
                $sql = $sql." numberOfIngredients <= ";
                $sql = $sql.$numberOfIngredients;
            }
            else
            {
                if(empty($method))
                {
                        $sql = $sql." numberOfIngredients <= ";
                        $sql = $sql.$numberOfIngredients;
                }
                else
                {
                    $sql = $sql." and numberOfIngredients <= ";
                    $sql = $sql.$numberOfIngredients;
                }
            }
        }
        else
        {
            $sql = $sql." and numberOfIngredients <= ";
            $sql = $sql.$numberOfIngredients;
        }
    }
    $noIngCount = 0;
    if(!empty($noIngredients))
    {
        $sql = $sql." and recipeID not in (select recipeID from recipeConnection where ";
    }
    foreach ($noIngredients as $noIngredient)
    {
        if($noIngCount == 0  )
        {
            $sql = $sql."foodName = '".$noIngredient."' "; 
        }
        else
        {
            $sql = $sql."and ";
            $sql = $sql."foodName = '".$noIngredient."' ";
        }
            
    $noIngCount = $noIngCount +1 ;
    }
     if(!empty($noIngredients))
    {
        $sql = $sql." )";
    }

    SearchInsert($sql, $ingredients); //call search and insert 
}

//serach the table and 
function searchInsert($sql, $ingredients)
{
    //echo $sql;
    $con = getConnection($_SESSION['notLoggedInUsername'], $_SESSION['notLoggedInPW']);
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

function displayFavorites() 
{
    $con = getConnection($_SESSION['loggedInUsername'], $_SESSION['loggedInPW']);
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();

    $favoritesList = array();


        $username = $_GET['username'];
        
        $query = "select recipeName, time, rating, picture from recipe inner join searchHistory on recipe.recipeID = searchHistory.id where username = '".$username."'";
        //echo $query;
        
        $result = $con->query($query);
        while ($rows = mysqli_fetch_row($result)) 
        {
            $favoritesList[] = $rows;
        }
        
    echo json_encode($favoritesList);
}

