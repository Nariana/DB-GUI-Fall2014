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

  var send = new Object();

  var selected = $("#filters input:checked");
  var i = 0;

  $.each(selected, function(){
    //console.log($(this).attr("class"));
    var key = $(this).attr("class");
    var value = $(this).val();

    console.log(key, value);
    send[i] = {};
    send[i][key] = value;
    i++;
  });
  //console.log(selected);

  console.log(send);

    $.ajax({
        type: "GET",
        url: rootURL+"/getResult",
        dataType: "json",
        data: send,
        success: function (result) {
          $("table .resultRow").remove();
            console.log(result.length);
            if(result.length === 0){
               $("#resultTable").append("<tr><td>sorry, no results</td></tr>");
            }

            if(result != 0){
              console.log("not 0");
              for (var i = 0; i < result.length ; i++) {
                console.log(result[i]);
                var add = "<tr id='recipe"+i+"' class='resultRow'><td class='name'>"+result[i].recipeName+"</td><td>"+result[i].rating+"</td><td>"+result[i].time+"</td></tr>";
                $("#resultTable").append(add);

                addListeners();
                $(".resultRow").click(function(){
                  recipe = $(this).children(".name").html();
                  localStorage.setItem("selectedRecipe", recipe);
                  window.location.href = "recipe.html";
                });
              }
            }
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

  $("#filters input").on("change", function() {
    console.log("change filters");
    getResults();
  });

}


function load(){
    $( "#slider-calories" ).slider({
      range: "min",
      value: 1000,
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
    addListeners();
    showIngredients();
    getResults();

}

function showIngredients(){

  var ing = localStorage.getItem("ingredients");
  ing = ing.split(",");
  $.each(ing, function(key, value){
    $("#ingList").append('<li><input type="checkbox" class="ing" checked name="ing" value="'+value+'" id="ing'+key+'"><label for="ing'+key+'">'+value+'</label></li>');
  });
}

/* hover color change for the back button */
$("#back").hover(
  function(){
    $(this).css("color","#fae59d");
    //$(this).css("color","#8aa1ab");
},  function(){
    $(this).css("color","white");
});