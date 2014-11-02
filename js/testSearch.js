jQuery(document).ready(function() {

	//Search Activities
	$('#searchbar').submit(function (e) {
		e.preventDefault();
		var searchString = $("#searchString").val();
		$.ajax({
			type: "GET",
		    url: 'api/index.php/searchActivities',
		    data: console.log($(this)),
		    success: function(data) {
		    	console.log(this);
		    	var obj = $.parseJSON(data);
				var resultDiv = "<div >"+"Search Results:"+"</div>";
		 		$(resultDiv).appendTo("#searchResults");
		 		if(data==500)
		 			$("<div >"+"No results found"+"</div>").appendTo("#searchResults");
		 		else {
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

