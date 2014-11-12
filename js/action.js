//XAMPP
var rootURL = "http://localhost/db-gui-fall2014/api/index.php";
//MAMP
// var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";

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

  $(function() {
    var dialog, form,
 
      // Regex from rom http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      name = $( "#name" ),
      email = $( "#email" ),
      password = $( "#password" ),
      allFields = $( [] ).add( name ).add( email ).add( password ),
      tips = $( ".validateTips" );
 
    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
 
    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }
 
    function addUser() {
      var valid = true;
      allFields.removeClass( "ui-state-error" );
 
      valid = valid && checkLength( name, "username", 3, 16 );
      valid = valid && checkLength( email, "email", 6, 80 );
      valid = valid && checkLength( password, "password", 5, 16 );
 
      valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
      valid = valid && checkRegexp( email, emailRegex, "eg. ui@jquery.com" );
      valid = valid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
 
      var send = new Object();

      if ( valid ) {
        $( "#users tbody" ).append( "<tr>" +
          "<td>" + name.val() + "</td>" +
          "<td>" + email.val() + "</td>" +
          "<td>" + password.val() + "</td>" +
        "</tr>" );
        dialog.dialog( "close" );
      }
      return valid;
    }
 
    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 380,
      modal: true,
      buttons: {
        "Create an account": addUser,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });
 
    $( "#register" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });
  });



