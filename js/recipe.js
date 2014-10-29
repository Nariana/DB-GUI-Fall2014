console.log(localStorage.getItem("selectedRecipe"));
//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";

var dummy = {
            "recipeName": "dummyName",
            "recipeImage": "img/Logo2.png",
            "recipeDirections": "dummyDirections"
            }

load();

function load(){

	var recipeName = localStorage.getItem("selectedRecipe");

	var send = {"recipeName": recipeName};

	console.log(send);

	$.ajax({
        type: "GET",
        url: rootURL+"/getRecipe",
        dataType: "json",
        data: send,
        success: function (result) {
            console.log(result);
          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});


  /* modify page with data from get request */
  $("#recipeTitle").text(dummy["recipeName"]);
  $("#recipeImage").attr("src", dummy["recipeImage"])
  $("#recipeDirections").text(dummy["recipeDirections"]);
}

/* go back to results */
$("#backButton").click(function(){
  window.location.href = "results.html";
});

/* go to front page (clear localstorage also?) */
$("#newButton").click(function(){
  window.location.href = "index.html";
});