//XAMPP
var rootURL = "http://localhost/DB-GUI-Fall2014/api/index.php";
//MAMP
// var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";
// var rootURL = "http://localhost/api/index.php";
 // var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";
//var rootURL = "http://localhost/api/index.php";


var allIngredients = getIngredients();

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
                $(ui.item).val("");
                console.log("clearing");
              }
            });
          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});
}
        



console.log("in action");

$('#addIngredient').click(function(){
  clickAdd($('#textIngredient').val());
});


function clickAdd(inputted){
  console.log(inputted);
  $('#textIngredient').val(""); 

  if(inputted != ""){
    $('#ingredientList').append("<li class='ingredient'>"+ inputted + "</li>");
  }
  console.log($($('#textIngredient')[0]));
  $($('#textIngredient')[0]).val("");
}

$("#search").click(function(){
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
      .click(function( event ) {
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

$(function() {
    var tooltips = $( "[title]" ).tooltip({
      position: {
        my: "left top",
        at: "right+5 top-5"
      }
})});




