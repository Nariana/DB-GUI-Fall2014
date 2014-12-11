console.log(localStorage.getItem("selectedRecipe"));
//XAMPP
var rootURL = "http://localhost/DB-GUI-Fall2014/api/index.php";
//MAMP
// var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";
//var rootURL = "http://localhost/api/index.php";
// var rootURL = "api/index.php"


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

            $("#rate").prepend(result["rating"]);
            $("#rectime").prepend(result["time"]);
            $("#cals").prepend(result["calories"]);
            $("#recipeImg").attr("src",result["picture"]);

              if((localStorage.getItem("username") != null) && (localStorage.getItem("username") != "null")){   
                  if($("#recipeInfo").has("i").length === 0){
                    $("#recipeInfo").append("<li class='thumb-col'><i class='thumb fa fa-star-o fa-2x'></i>  </li>");
                    console.log(result.saved);
                    if (result.saved === "true") {
                      console.log("changing "+i);
                      $("#recipeInfo i").addClass("saved");
                      $("#recipeInfo i").css("color", "#8aa1ab");
                    }

                    $(".thumb").hover(function(){
                      $(this).addClass("thumbHover");
                      $(this).css("color", "black");
                    });

                   }
                }
          },
        error: function(jqXHR, textStatus, errorThrown){
          alert("No recipe found! Let's go back!");
          window.location.href = "results.html";
           console.log(jqXHR, textStatus, errorThrown);
      }});

}

/* go back to results */

$("#backButton").button().off("click").on("click", function(){
  window.location.href = "results.html";
});

/* go to front page (clear localstorage also?) */
$("#newButton").button().off("click").on("click", function(){
  localStorage.removeItem("selectedRecipe");
  window.location.href = "index.html";
});

function colorThumbs(t){
  if($(t).hasClass("saved")){
    $(t).css("color", "");
  }
  else{
    $(t).css("color", "black");
  }

}

function thumbClick(t){
    console.log(t);
    var recipe = $(t).parent().parent().find("h5").html();
    var send = {"recipeName": recipe};
    console.log(send);
    $(t).toggleClass("saved");

    if($(t).hasClass("saved")===false){
        $.ajax({
          type: "GET",
          url: rootURL+"/deleteFavorites",
          data: send,
          success: function (result) {
              //console.log(result);
              $(t).css("color", "black");
              var newRating = parseInt($(t).parent().parent().find(".rating").text()) -1;
              $(t).parent().parent().find(".rating").text(newRating + " likes");
            },
          error: function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR, textStatus, errorThrown);
        }});
    }
    else{
      $.ajax({
          type: "GET",
          url: rootURL+"/saveRecipe",
          data: send,
          success: function (result) {
              //console.log(t);
              $(t).css("color", "#8aa1ab");
              var newRating = parseInt($(t).parent().parent().find(".rating").text()) + 1;
              $(t).parent().parent().find(".rating").text(newRating + " likes");
            }, 
           error: function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR, textStatus, errorThrown);
        }});
  }
}