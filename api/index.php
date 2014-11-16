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

//$app->get('/updateRating', 'updateRating');

$app->get('/displayFavorites', 'displayFavorites');

$app->post('/login', 'login');
$app->post('/register', 'register');
$app->post('/logout', 'logout');


$app->run();

session_destroy();

function getConnection($user = 'root', $pw = 'root', $host = 'localhost') {
    $dbConnection = new mysqli($host, $user, $pw, 'PantryQuest'); //put in your password
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

function deleteFavorites()
{
    try
    {   
        if ($_SESSION['id'] == 1) //you can only do thos if you are logged in 
        {
            $user = 'loggedIn';
            $pw = '123';
            //get connection as a logged in user 
            $con = getConnection($user, $pw);
            $recipeName = $con->real_escape_string($_GET['recipeName']); 
            $recipeID;
            
            
            $stmt =$con->prepare("select recipeID from recipe where recipeName = ? ");
            $stmt->bind_param('s', $recipeName);
            $stmt->execute(); 
            $stmt->bind_result($tempID);
            $recipeID;
            while ($stmt->fetch())
            {
            $recipeID = $tempID;
            }
            
            
            
            $stmt =$con->prepare("delete from searchHistory where username = ? and id = ?");
            $stmt->bind_param('si', $_SESSION['username'], $recipeID);
            $stmt->execute(); 
            
            

            //Decrement the number and then delete from the result table 

            $stmt1 = $con->prepare("select rating from recipe where recipeName = ?");
            $stmt1->bind_param('s', $recipeName);
            $stmt1->execute(); 
            $stmt1->bind_result($rating);
            while ($stmt->fetch())
            {
            $rating = $rating - 1;
            $sql2 = $con->prepare("UPDATE recipe SET rating = ? where recipeName = ? ");
            $sql2->bind_param('is', $rating, $recipeName);
            $sql2->execute();         
            }                    
            
        }
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
    
}

function showFavorite()
{
    $favorites = array();
    try
    {
        $user = 'loggedIn';
        $pw = '123';
        //get connection as a logged in user 
        $con = getConnection($user, $pw);
        
        if ($_SESSION['id'] == 1) //you can only do thos if you are logged in 
        {
            $username = $con->real_escape_string($_SESSION['username']);
            
            $sql = "select recipeName, rating from recipe inner join searchHistory on searchHistory.id = recipe.recipeID where username =".$username."'";
            $result= $con->query($sql);
            
        if (!$result) //check if the result is valid 
        {
            throw new Exception(mysqli_error($con));
        }
        
        while($r = $result)
        {
            $favorites[] = $r; // store results in favorites 
        }
            
        }
        
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
    json_encode($favorites);
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
        $stmt = "select recipeName from recipe order by rating desc";
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
        $stmt->execute(); 
        $stmt->bind_result($tempID);
        $recipeID;
        while ($stmt->fetch())
        {
            $recipeID = $tempID;
        }
        //check if it has already been saved by that person. 
        $sgl1 = $con->prepare("select username from searchHistory where ID = ?");
        $sgl1->bind_param('i', $recipeID);
        $sgl1->execute(); 
        $sgl1->bind_result($tempName); 
        $count = $sql1->num_rows();
        if ($count == 0) //you have not saved that before 
        {
            
        //prepare statement 
        $sql = $con->prepare("INSERT INTO savedRecipes(username, id) values (?, ?)");    
        $sql->bind_param('ss', $_SESSION['username'], $id);
        $sql->execute();
            
        //increment number of times that recipe has been saved 
        $stmt = $con->prepare("select rating from recipe where recipeName = ?");
        $stmt->bind_param('s', $recipeName);
        $stmt->execute(); 
        $stmt->bind_result($rating);
        while ($stmt->fetch())
        {
            $rating = $rating + 1;
            $sql2 = $con->prepare("UPDATE recipe SET rating = ? where recipeName = ? ");
            $sql2->bind_param('is', $rating, $recipeName);
            $sql2->execute();         
        }                    
              
        }
        }
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
}
/*
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
        $stmt->execute(); 
        $stmt->bind_result($rating);
        while ($stmt->fetch())
        {
            $rating = $rating + 1;
        }

        $sql2 = $con->prepare("UPDATE recipe SET rating = ? where recipeName = ? ");
        $sql2->bind_param('is', $rating, $recipeName);
        $sql2->execute(); 
    }
    catch (Exception $e)
    {
        $e->getMessage();
    }
}*/
function login()
{
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $information = array();
    $name;
    
    $query = $con->prepare("select name from users where id = ? and pw = ? ");
    $pwmd5 = md5($_POST['pw']);
    $query->bind_param('ss', $_POST['username'], $pwmd5);
    $query->execute();
    $query->bind_result($tempName);
    while ($query->fetch())
    {
        $name = $tempName;
    }
    
    $count = $query->num_rows();

    if ($count == 0)
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
        $information[] = $name;
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
    $resultCheck = $stmt->fetch();
    if (empty($accountCheck)) {
            $userExists = TRUE;
    }
    
    if($userExists == FALSE)
    {
        $stmt = $con->prepare("INSERT into users (username, name, pw) value (?,?,?)");
        $pwmd5 = md5($_POST['pw']);
        $stmt->bindParam('sss', $_POST['username'], $_POST['name'], $pwmd5);
        $stmt->execute();
        $information[] = $_POST['username'];
        $information[] = $_POST['name'];
        $_SESSION['username'] = $_POST['username'];
    }
    json_encode($information);
}


function getRecipe()
{
    echo "inside get Recipe";
    $con = getConnection();
	$app = \Slim\Slim::getInstance();
    $timesClicked;
    try
    {
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

        //echo print_r($results);
    
     //increment the nuber of times that recipe has been selected 
        $stmt = $con->prepare("select timesClicked from recipe where recipeName = ? ");
        $stmt->bind_param('s', $recipeName);
        $stmt->execute();
        $stmt->bind_result($timesClicked);
        while ($stmt->fetch())
        {
            $timesClicked = $timesClicked + 1; 
            $sql2 = $con->prepare("UPDATE recipe SET timesClicked = ? where recipeName = ? ");
            $sql2->bind_param('is', $timesClicked, $recipeName);
            $sql2->execute(); 
        
        }


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
    
    echo "hi";
    
	$con = getConnection();
	$app = \Slim\Slim::getInstance();
    //create variables to store information

    $ingredients = array();
    $filters = array();
    $methods = array();
    $noIngredients = array();
    $results = array();
    $points = array();
    $time = array();
    $calories = array();
    $counter = 0;
    //echo $counter;
    $rows = array();
    $results = array();
    $saved = array();
    $timesSearched;
    //epty previous table 
    
    try
    {
    $sql = "Truncate TABLE results";
    $con->query($sql);

    //echo print_r($_GET);

    //store all information from json, input from user 
    foreach ($_GET as $part)
    {
        if(array_key_exists("ing", $part ))
        {   $ingredient = $con->real_escape_string($part['ing']);
            $ingredients[] = $ingredient;
             
            //increment the nuber of times that ingredient is searched for
            
        $query = 'select timesSearched from ingredient where foodName = ? ';
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $part['ing']);
        $stmt->execute();
        $stmt->bind_result($timesSearched);
        while ($stmt->fetch())
        {
            $timesSearched = $timesSearched + 1;
            $sql2 = $con->prepare("UPDATE ingredient SET timesSearched = ? where foodName = ? ");
            $sql2->bind_param('is', $timesSearched, $part['ing']);
            $sql2->execute(); 
        }


        }
            if(array_key_exists("filter", $part ))
        {
            $filter = $con->real_escape_string($part['filter']);
            $filters[] = $filter; 
                //echo $part['filter']; 
        }
            if(array_key_exists("method", $part ))
        {
            $method = $con->real_escape_string($part['method']);
            $methods[] = $method;
               // echo $part['method'];
        }
            if(array_key_exists("time", $part ))
        {
            $time = (int)$part['time'];
            $hasTime = true;
        }
            if(array_key_exists("noingredient", $part ))
        {
            $noIngredient = $con->real_escape_string($part['noingredient']);
            $noIngredients[] = $noIngredient;
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

    //check what of the results you have favorited 
    $result1= $con->query("select recipeName from recipe inner join  results on results.recipeID =  recipe.recipeID inner join filter on results.recipeID = filter.recipeID inner join searchHistory on results.recipeID = searchHistory.ID order by rankingPoints desc"); //execute query 
    
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
        
                   
    $result= $con->query("select recipeName, time, recipe.rating, rankingPoints, calories, picture from recipe inner join  results on results.recipeID =  recipe.recipeID inner join filter on results.recipeID = filter.recipeID order by rankingPoints desc"); //execute query 
        
        //check what of the results you have favorited 
    
    if (!$result)
    {
        throw new Exception(mysqli_error($con));
    }
    
    if (mysqli_num_rows($result) != 0)
    {
            //loop through saved to see if a recipe is already saved 
   	    while($r = mysqli_fetch_assoc($result)) 
   	    {
            foreach ($saved as $recipe)
            {
                if($recipe == $r[0]) //if that recipe is in the saved list 
                {
                    $results[] = $r;
                    $results[] = 'saved'; 
                }
                else
                {
                    $results[] = $r;
                    $results[] = 'notSaved'; 
                }
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
        
        //echo $sql;
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
    //echo $sql;
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

function displayFavorites() 
{
    $con = getConnection();
    $app = \Slim\Slim::getInstance();
    $request = $app->request()->getBody();
    $_SESSION['username'] = $_POST['username'];

    $favoritesList = array();
    $query = "select recipeName, time, rating, picture from recipe inner join searchHistory on recipe.recipeID = searchHistory.id where username =".$_SESSION['username']."'";
    $result = $con->query($query);
    while ($rows = mysqli_fetch_row($result)) 
    {
        $favoritesList[] = $rows;
    }

    echo json_encode($favoritesList);

}