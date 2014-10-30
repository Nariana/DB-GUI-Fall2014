//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";

var allIngredients = getIngredients();

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
                $('#textIngredient').val(ui.item.value);
                //console.log();
                clickAdd();
                $('#textIngredient').val("");
              }
            });
          },
        error: function(jqXHR, textStatus, errorThrown){
           console.log(jqXHR, textStatus, errorThrown);
      }});
}
        



console.log("in action");

$('#addIngredient').click(function(){
  clickAdd();
});

$("li .ui-menu-item").click(function(){
  console.log("clicked "+ this);
  clickAdd();
});

function clickAdd(){
  var inputted = $('#textIngredient').val();
  console.log(inputted);
  $('#textIngredient').val(""); 

  if(inputted != ""){
    $('#ingredientList').append("<li class='ingredient'>"+ inputted + "</li>");
  }
}

$("#search").click(function(){
	console.log("go to results");
  var query = [];
  var fields = $(".ingredient");
  $.each(fields, function(i, v){
    query.push(v.textContent);

  });
  //console.log(query);
  localStorage.setItem("ingredients", query);
  //console.log(localStorage.query);

	window.location.href = "results.html";

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



