console.log(localStorage.getItem("selectedRecipe"));
//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";

/* dummy objec for testing, remember to delete */
var dummy = {
            "name": "recipe name",
            "url": "img/Logo2.png",
            "instructions": "instructions:",
            "rating": "rating: 5",
            "ingredients": "ingredients:",
            "calories": "calories: 2000",
            "time": "time: 3 hours",
            }

$(document).ready(function(){
  load();
});


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
            $("#recipeName").text(result["name"]);
            $("#recipeImg").attr("src", result["url"]);
            $("#instr").text(result["instructions"]);
            $("#ingr").text(result["ingredients"]);
            $("#rate").text(result["rating"]);
            $("#time").text(result["time"]);
            $("#cals").text(result["calories"]);
          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});

}

/* go back to results */
$("#backButton").click(function(){
  localStorage.selectedRecipe.clear();
  window.location.href = "results.html";
});

/* go to front page (clear localstorage also?) */
$("#newButton").click(function(){
  localStorage.clear();
  window.location.href = "index.html";
});