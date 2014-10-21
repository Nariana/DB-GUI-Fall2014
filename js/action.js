/*
var allIngredients = getIngredients();

function getIngredients(){

	$.ajax({
      type: 'GET',
      url: rootURL+"/getIngredients",
      dataType: "json", // data type of response
      success: function(data, textStatus, jqXHR){
         console.log(data);
         return data;
      },
      error: function(jqXHR, textStatus, errorThrown){
      	//alert("Burger not deleted");
         console.log(jqXHR, textStatus, errorThrown);
         window.location.reload();
      }
   });
}
*/
console.log("in action");



$('#addIngredient').click(function(){
	var inputted = $('#textIngredient').val();
	console.log(inputted);
	$('#textIngredient').val("");	

	if(inputted != ""){
		$('#ingredientList').append("<li class='ingredient'>"+ inputted + "</li>");
	}
});

$("#search").click(function(){
	alert("clicked");
	console.log("go to results");
	window.location.href = "results.html"

});