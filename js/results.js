//XAMPP
//var rootURL = "http://localhost/DB-GUI-Fall2014/index.php";
//MAMP
var rootURL = "http://localhost:8888/DB-GUI-Fall2014/api/index.php";


getResults();


function getResults(){

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
            console.log(result);
            for (var i = 0; i < result.length - 1; i++) {
              var add = "<tr id='recipe"+i+"'><td></td></tr>";
              $("#resultTable").append(add);
            };
            //alert("done!"+ csvData.getAllResponseHeaders())
          },
        error: function(jqXHR, textStatus, errorThrown){
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

$("#filters input").on("change", function()) {
getResults();
} 
