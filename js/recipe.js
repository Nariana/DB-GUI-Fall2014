console.log(localStorage.getItem("selectedRecipe"));
//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/api/index.php";
//MAMP
 var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";
// var rootURL = "http://localhost/api/index.php";


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
            $("#recipeName").append(result["recipeName"]);
            $("#recipeImg").attr("src", result["url"]);
            var instructions = result.instruction.split("\n");
           
            if(result.instruction != undefined){
              $.each(instructions, function(i,text){
               // console.log(text);
                $("#instr").append("<li>"+text+"</li>");
              })
            }
            else{
              //console.log(result.instruction);
            }

            if(result.ingredients != undefined){
              var ingredients = result.ingredients.split("\n");
              $.each(ingredients, function(i,text){
                $("#ingr").append("<li>"+text+"</li>");
                //console.log(text);
              })
            }
            else{
              //console.log(result.ingredients);
            }

            $("#rate").append(result["rating"]);
            $("#time").prepend(result["time"]);
            $("#cals").prepend(result["calories"]);
            $("#recipeImg").attr("src",result["picture"]);
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