//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";

$(document).ready(function(){
  load();
});


function getResults(){
  $("table .resultRow").remove();
  console.log("show table");

  var ing = localStorage.getItem("ingredients");
  ing = ing.split(",");

  var send = new Object();

  for(var i = 0; i< ing.length; i++){
    send[i] = {"name":ing[i]};
  }

  console.log(send);

    $.ajax({
        type: "GET",
        url: rootURL+"/getResult",
        dataType: "json",
        data: send,
        success: function (result) {
            console.log(result.length);
            if(result.length === 0){
               $("#resultTable").append("<tr><td>sorry, no results</td></tr>");
            }

            for (var i = 0; i < result.length ; i++) {
              console.log(result[i]);
              var add = "<tr id='recipe"+i+"' class='resultRow'><td class='name'>"+result[i].recipeName+"</td><td>"+result[i].rating+"<i id='back' class='fa fa-info'></i></td><td>"+result[i].time+"</td></tr>";
              $("#resultTable").append(add);

              addListeners();
              $(".resultRow").click(function(){
                recipe = $(this).children(".name").html();
                localStorage.setItem("selectedRecipe", recipe);
                window.location.href = "recipe.html";
              });
            };
            //alert("done!"+ csvData.getAllResponseHeaders())
          },
        error: function(jqXHR, textStatus, errorThrown){
          $("#resultTable").append("<tr><td>sorry, no results</td></tr>");
           console.log(jqXHR, textStatus, errorThrown);
      }});

}

$(function() {
    $( "#pic-modal" ).dialog({
      modal: true,
      buttons: {
        Close: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  });


function addListeners(){
  $(".resultRow").hover(function(){
    $(this).css("background-color","grey");
    $(this).css("border","solid black 1px");
  }, function(){
    $(this).css("background-color","white");
    $(this).css("border","none");

  });

  $("#back").click(function(){
    window.location.href = "index.html";

  });

  $("#filters *").on("change", function() {
    getResults();
  });

}


function load(){
    $( "#slider-calories" ).slider({
      range: "min",
      value: 200,
      min: 100,
      max: 2000,
      slide: function( event, ui ) {
        $( "#calories" ).val( ui.value + " Calories" );
      }
    });
    $( "#calories" ).val($( "#slider-calories" ).slider( "value" ) + " Calories");

    $( "#slider-time" ).slider({
      range: "min",
      value: 30,
      min: 5,
      max: 120,
      slide: function( event, ui ) {
        $( "#time" ).val( ui.value + " minutes" );
      }
    });
    $( "#time" ).val($( "#slider-time" ).slider( "value" ) + " minutes");

    getResults();
    addListeners();
    showIngredients();

}

function showIngredients(){

  var ing = localStorage.getItem("ingredients");
  ing = ing.split(",");
  $.each(ing, function(key, value){
    $("#ingList").append('<input type="checkbox" name="ing" value="'+value+'" id="ing'+key+'"><label for="ing'+key+'">'+value+'</label>');
  });
}


