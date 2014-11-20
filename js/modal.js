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
      console.log("addUser");
      var valid = true;
      allFields.removeClass( "ui-state-error" );
 
      valid = valid && checkLength( name, "username", 3, 16 );
      valid = valid && checkLength( email, "email", 6, 80 );
      valid = valid && checkLength( password, "password", 5, 16 );
 
      valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
      valid = valid && checkRegexp( email, emailRegex, "eg. ui@jquery.com" );
      valid = valid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
 
      var send = new Object();

      send.name = name.val();
      send.username = email.val();
      send.pw = password.val();

      console.log(send);

      if ( valid ) {
        dialog.dialog( "close" );
        console.log("running ajax 64");

        $.ajax({
          type: "POST",
          url: rootURL+"/register",
          data: send,
          dataType: "json",
          success: function (result) {
              console.log(result);
            },
          error: function(jqXHR, textStatus, errorThrown){
             console.log(jqXHR, textStatus, errorThrown);
        }});

      }
      return valid;
    }
 
    dialog = $( "#register-form" ).dialog({
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


      dialogLogin = $( "#login-form" ).dialog({
        autoOpen: false,
        height: 300,
        width: 380,
        modal: true,
        buttons: {
          "Login!": login,
          Cancel: function() {
            dialogLogin.dialog( "close" );
          }
        },
        close: function() {
          form[ 0 ].reset();
          allFields.removeClass( "ui-state-error" );
        }
    });
 
    form = dialogLogin.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      login();
    });
 
    $( "#login" ).button().on( "click", function() {

      dialogLogin.dialog( "open" );
    });


    function login(){
      console.log("loggin in");

      var valid = true;
      allFields.removeClass( "ui-state-error" );
 
      var send = new Object();

      send.name = $("#nameLogin").val();
      send.pw = $("#passwordLogin").val();

      console.log(send);

      if ( valid ) {
        dialogLogin.dialog( "close" );

        $.ajax({
          type: "POST",
          url: rootURL+"/login",
          data: send,
          dataType: "json",
          success: function (result) {
              console.log(result);
              if(result[0] === "Invalid login"){
                alert("Invalid Login! Try again!");
              }
              else{
                console.log("logged in to " + result[0]);
                localStorage.setItem("username", result[0]);
                location.reload();
              }

            },
          error: function(jqXHR, textStatus, errorThrown){
             console.log(jqXHR, textStatus, errorThrown);
        }});

      }
      return valid;

    }


    /***ANALYTICS*****/


      dialogAnalytics = $( "#analytics-form" ).dialog({
        autoOpen: false,
        height: 500,
        width: 580,
        modal: true,
        buttons: {
          Cancel: function() {
            dialogAnalytics.dialog( "close" );
          }
        },
        close: function() {
          form[ 0 ].reset();
          allFields.removeClass( "ui-state-error" );
        }
    });
 
    form = dialogLogin.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });
 
    $( "#analytics" ).button().on( "click", function() {
      console.log("should show analytics");
      console.log(dialogAnalytics);
      dialogAnalytics.dialog( "open" );
      analytics();
    });


    function analytics(){
      console.log("analytics");

        $.ajax({
          type: "GET",
          url: rootURL+"/getAnalytics",
          dataType: "json",
          success: function (result) {
              console.log(result);
            },
          error: function(jqXHR, textStatus, errorThrown){
             console.log(jqXHR, textStatus, errorThrown);
        }});

      }
  });

$("#logout").on("click", function(){
  console.log("logging out");

  $.ajax({
    type: "POST",
    url: rootURL+"/logout",
    success: function (result) {
      console.log(result);
      localStorage.setItem("username", null);
      location.reload();
        },
    error: function(jqXHR, textStatus, errorThrown){
      console.log(jqXHR, textStatus, errorThrown);
    }});
});




  if(!localStorage.getItem("username")){
    $("#favorites").hide();
    $("#welcome").hide();
    $("#logout").hide();
  }
  else{
    $("#login").hide();
    $("#register").hide();
  }