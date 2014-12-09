//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/api/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";
//var rootURL = "http://localhost/api/index.php";
//var rootURL = "api/index.php"

localStorage.removeItem("filters");
localStorage.removeItem("noningredients");
var allIngredients = getIngredients();
showRecipes();

$( ".textIngredient" ).on( "autocompleteselect", function( event, ui ) {
  $('#textIngredient').val("");
  console.log($(this));
} );

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
            //alert("done!"+ csvData.getAllResponseHeaders())
            $( "#textIngredient" ).autocomplete({
              source: availableTags,
              select: function(event, ui){
                //console.log();
                clickAdd(ui.item.value);
                console.log("clearing");
                $(this).val(''); 
                return false;
              }
            });
          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});
}
        
console.log("in action");

$('#addIngredient').off("click").on("click", function(){
  clickAdd($('#textIngredient').val());
});


function clickAdd(inputted){
  console.log(inputted);
  $('#textIngredient').val(""); 

  var query = [];
  var fields = $(".ingredient");
  $.each(fields, function(i, v) {
    query.push(v.textContent);
  });
  var isCopy = false;
  for (i = 0; i < query.length; i++) {
    if (inputted == query[i]) {isCopy = true;}
  }
  if(inputted != "" && !isCopy){
    $('#ingredientList').append("<li class='ingredient'><i class='fa-times fa remove_ing'></i>"+ inputted + "</li>");
    $('.remove_ing').off("click").on("click", function(){
      $(this).parent().remove();
    });
    $('.remove_ing').hover(function(){
      $(this).css("color","white");
    }, function(){
      $(this).css("color","black");
    });
  }
  
  console.log($($('#textIngredient')[0]));
  $($('#textIngredient')[0]).val("");
}

$("#search").off("click").on("click",function(){
	console.log("go to results");
  var query = [];
  var fields = $(".ingredient");
  $.each(fields, function(i, v){
    query.push(v.textContent);

  });
  //console.log(query);
  if (query.length > 0) {
    localStorage.setItem("ingredients", query);
    window.location.href = "results.html";
  }
  else {
    console.log("cancel search - no ingredients");
  }
  //console.log(localStorage.query);

});

$(function() {
    $( "input[type=submit], a, button" ) 
      .button()
      .on("click",function( event ) {
        event.preventDefault();
      });
});

$("#search").hover(
	function(){
		$(this).css("color","#fae59d");
		//$(this).css("color","#8aa1ab");
},  function(){
		$(this).css("color","white");
});

$("#ingredientList").on("contentChange", function(){
  console.log("content changed");
})

$(function() {
    var tooltips = $( "[title]" ).tooltip({
      position: {
        my: "left top",
        at: "right+5 top-5"
      }
})});

function showRecipes(){
  console.log("in show Recipes");
  console.log(rootURL+"/displayRecipes");
  $.ajax({
        type: "GET",
        url: rootURL+"/displayRecipes",
        dataType: "json",
        success: function (result) {
            console.log(result);
            var url="";
            var name = "";
            for (var i = 0; i < result.length ; i++) {
              url = result[i].url;
              name = result[i].name;
              $("#displayRecipes").append('<img src="'+url+'" alt="'+name+'" id="'+name+'" class="scrollableRecipe"/>');
            }


            $("#displayRecipes").smoothDivScroll({
                mousewheelScrolling: "allDirections",
                manualContinuousScrolling: true,
                autoScrollingMode: "onStart"
              });

            $(".scrollableRecipe").off("click").on("click", function(){
              localStorage.setItem("selectedRecipe", $(this).attr("alt"));
              window.location.href = "recipe.html";
            });

          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});
}




