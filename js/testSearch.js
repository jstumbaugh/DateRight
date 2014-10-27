jQuery(document).ready(function() {

//Search Activities
$('#searchbar').submit(function (e) {
e.preventDefault();
var searchString = $("#searchString").val();
$.ajax({
	type: "POST",
    url: './api/index.php/searchActivities',
    data: $(this).serialize(),
    success: function(response) {
    	var obj = $.parseJSON(response);
		var resultDiv = "<div >"+"Search Results:"+"</div>";
 		$(resultDiv).appendTo("#searchResults");
 		if(response==500)
 			$("<div >"+"No results found"+"</div>").appendTo("#searchResults");
 		else{
			for ( var i = 0; i < obj['results'].length; i++) {
				var a = obj['results'][i];
				var div_data = "<div >"+"Name: "+a['Name']+"------- Description: "+a['Description']+"</div>";
		 		$(div_data).appendTo("#searchResults");
				
				}
			}
}
});
});
});

