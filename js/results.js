//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/api/index.php";
//var rootURL = "http://localhost/api/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";


var allIngredients = getIngredients();

$(document).ready(function(){
  load();
});

function colorThumbs(t){
  if($(t).hasClass("saved")){
    $(t).css("color", "#8aa1ab");
  }
  else{
    $(t).css("color", "black");
  }

}

function getFilters(){


  var send = new Object();

  var selected = $("#filters input:checked");
  var i = 0;

  $.each(selected, function(){
    var key = $(this).attr("name");
    var value = $(this).val();

    console.log(key, value);
    send[i] = {};
    send[i][key] = value;
    i++;
  });
  console.log(i);

  localStorage.setItem("filters", JSON.stringify(send));


  send[i] = { "calories": $( "#slider-calories" ).slider( "value" )};
  send[i+1] = { "time": $( "#slider-time" ).slider( "value" )};

  return send;

}


function getResults(){


  $(".resultDiv").remove();
  console.log("show table");

  var send = getFilters();
  console.log(send);

    $.ajax({
        type: "GET",
        url: rootURL+"/getResult",
        dataType: "json",
        data: send,
        success: function (result) {
            console.log(result);
            $("table .resultRow").remove();
            if(result.length === 0){
              console.log("no results");
               $("#resultListDiv").append("<div class='resultDiv'><p id='no-result'>sorry, no results<p></div>");
            }

            if(result != 0){
              console.log(result.length);
             
              for (var i = 0; i < result.length ; i++) {
                var percent = Math.floor(result[i].rankingPoints*100);
                var add = "<div id='recipe"+i+"' class='resultDiv'><ul class='resultList'><h5 class='name'>"+result[i].recipeName+"</h5><li class='rating'>"+result[i].rating+" likes</li><li class='time'>"+result[i].time+" minutes</li><li class='percent'>"+percent+"% ingredient match</li></ul></div>";
                
                $("#resultListDiv").append(add);

                if((localStorage.getItem("username") != null) && (localStorage.getItem("username") != "null")){   
                  if($("#recipe"+i).has("i").length === 0){
                    $("#recipe"+i +" >ul").append("<li class='thumb-col'><i class='thumb fa fa-star-o fa-2x'></i>  </li>");
                    console.log(result[i].saved);
                    if (result[i].saved === "true") {
                      console.log("changing "+i);
                      $("#recipe"+i + " ul i").addClass("saved");
                      $("#recipe"+i + " ul i").css("color", "#8aa1ab");
                    }

                   }
                }

                addListeners();
              }

              var divs = $(".resultDiv");
              var maxHeight = 0;

              $.each(divs, function(){
                var thisHeight = $(this).height();
                //console.log(thisHeight);
                if ( thisHeight > maxHeight){
                  maxHeight = thisHeight;
                  //console.log("changing to " + thisHeight);
                }
              });

              $.each(divs, function(){
                $(this).height(maxHeight);
              });
            }
          },
        error: function(jqXHR, textStatus, errorThrown){
          $("#resultListDiv").append("<div class='resultDiv'><p id='no-result'>sorry, no results</p></div>");
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
  $(".resultDiv").hover(function(){
    $(this).css("background-color","grey");
  }, function(){
    $(this).css("background-color","white");

  });

  $("#filters input").off("change").on("change", function() {
    console.log("change filters");
    getResults();
  });

  $(".resultDiv").off("click").on("click", function(){
    console.log($(this));

    if($(this).hasClass("thumb") === false){
      console.log($(this).find("h5").html());
      recipe = $(this).find("h5").html();
      localStorage.setItem("selectedRecipe", recipe);
      window.location.href = "recipe.html";
    }
}).on("click", "i", function(e){
  console.log("thumb blicked");
  thumbClick(this);
  e.stopPropagation();
});

  $("i .thumb").off("click").on("click", function(){
    console.log($(this));
    thumbClick(this);
  });
  $(".thumb").hover(function(){
    $(this).addClass("thumbHover");
    $(this).css("color", "white");

  }, function(){
    $(this).removeClass("thumbHover");
    colorThumbs(this);

  });

  $("#back").button().off("click").on("click",function(){
    window.location = "index.html";
  });
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
          dataType: "json",
          success: function (result) {
              console.log(result);
              $(t).css("color", "#8aa1ab");
              var newRating = parseInt($(t).parent().parent().find(".rating").text()) + 1;
              $(t).parent().parent().find(".rating").text(newRating + " likes");
            }, 
           error: function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR, textStatus, errorThrown);
        }});
  }
}

function load(){

  $( ".filterTitle" ).tooltip({
  });

    $( "#slider-calories" ).slider({
      range: "min",
      value: 1000,
      min: 100,
      max: 2000,
      slide: function( event, ui ) {
        $( "#calories" ).val( ui.value + " Calories" );
      },
      stop: function( event, ui ) {
        getResults();
      }
    });
    $( "#calories" ).val($( "#slider-calories" ).slider( "value" ) + " Calories");

    $( "#slider-time" ).slider({
      range: "min",
      value: 120,
      min: 5,
      max: 360,
      slide: function( event, ui ) {
        $( "#time" ).val( ui.value + " minutes" );
      },
      stop: function( event, ui ) {
        getResults();
      }
    });
    $( "#time" ).val($( "#slider-time" ).slider( "value" ) + " minutes");
    $('#time').css("color", "white");
    $("#calories").css("color", "white");

    addListeners();
    showIngredients();
    getResults();
}

function showIngredients(){

  var ing = localStorage.getItem("ingredients");
  var noning = localStorage.getItem("noningredients");
  console.log(ing);
  console.log(noning);
  ing = ing.split(",");

  $('#noningList > li .ingI').remove();
  $.each(ing, function(key, value){
    $("#ingList").append('<li class="ingI"><input type="checkbox" class="ing" checked name="ing" value="'+value+'" id="ing'+key+'"><label for="ing'+key+'">'+value+'</label></li>');
  });

  if(noning != null){
    noning = noning.split(",");
    $.each(noning, function(key, value){
      $("#noningList").append('<li class="ingI"><input type="checkbox" class="noning" checked name="noning" value="'+value+'" id="noning'+key+'"><label for="noning'+key+'">'+value+'</label></li>');
    });
}

  if(localStorage.filters === undefined){
    localStorage.filters = JSON.stringify({});
  }
  console.log(localStorage.filters);
    var filters = JSON.parse(localStorage.filters);

    $.each(filters, function(n,o){
      $.each(o, function(k,v){
        if(k === "method"){
          $("#"+v).prop("checked",true);
        }
        if(k === "restriction"){
          $("#"+v).prop("checked",true);
        }
      });
    });
}

function getIngredients(){

  console.log("in get Ingredients");
  console.log(rootURL+"/getIngredient");
  var availableTags = [];
  $.ajax({
        type: "GET",
        url: rootURL+"/getIngredient",
        dataType: "json",
        success: function (result) {
            console.log(result);
            for (var i = 0; i < result.length - 1; i++) {
              availableTags[i] = result[i][0];
            };
            $( "#addIngredient" ).autocomplete({
              source: availableTags,
              select: function(event, ui){
                var num = $(".ing").length;
                $("#ingList").append('<li><input type="checkbox" class="ing" checked name="ing" value="'+ui.item.value+'" id="ing'+num+'"><label for="ing'+num+'">'+ui.item.value+'</label></li>');
                $(this).val('');

                var query = [];
                var fields = $(".ing");
                $.each(fields, function(i, v) {
                  query.push(v.value);
                }); 

                if (query.length > 0) {
                    localStorage.setItem("ingredients", query);
                    getResults();
                  }
                else {
                    console.log("cancel search - no ingredients");
                }

                return false;
              }
            });

            $( "#excludeIngredient" ).autocomplete({
              source: availableTags,
              select: function(event, ui){
                var num = $(".noning").length;
                $("#noningList").append('<li><input type="checkbox" class="noning" checked name="noning" value="'+ui.item.value+'" id="noning'+num+'"><label for="noning'+num+'">'+ui.item.value+'</label></li>');
                $(this).val('');

                console.log()
                var queryNon = [];
                var fields = $(".noning");
                $.each(fields, function(i, v) {
                  queryNon.push(v.value);
                  console.log(queryNon);
                }); 

                if (queryNon.length > 0) {
                    localStorage.setItem("noningredients", queryNon);
                    getResults();
                  }
                else {
                    console.log("cancel search - no ingredients");
                }

                return false;
              }
            });
          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});
}