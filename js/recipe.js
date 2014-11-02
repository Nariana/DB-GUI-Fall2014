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
            //console.log(result.ingredients.split("\n"));
            $("#recipeName").append(result["recipeName"]);
            $("#recipeImg").attr("src", result["url"]);
            var instructions = result.instruction.split("\n");
            $.each(instructions, function(i,text){
              console.log(text);
              $("#instr").append("<li>"+text+"</li>");
            })

            var ingredients = result.ingredients.split("\n");
            $.each(ingredients, function(i,text){
              $("#ingr").append("<li>"+text+"</li>");
              console.log(text);
            })
            $("#rate").append(result["rating"]);
            $("#time").prepend(result["time"]);
            $("#cals").prepend(result["calories"]);
          },
        error: function(jqXHR, textStatus, errorThrown){
          alert("No recipe found! Let's go back!");
          window.location.href = "results.html";
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