$('#addIngredient').click(function(){
	var inputted = $('#textIngredient').val();
	console.log(inputted);
	$('#textIngredient').val("");	

	if(inputted != ""){
		$('#ingredientList').append("<li class='ingredient'>"+ inputted + "</li>");
	}
});