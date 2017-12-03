$( document ).ready(function() {

	// Tweet Darstellung
	var tweets = $(".tweet");
	
	$(tweets).each( function( t, tweet ) { 
	    var id = $(this).attr('id');

	    twttr.widgets.createTweet(
	      id, tweet, 
	      {
	        conversation : 'none',    // or all
	        cards        : 'hidden',  // or visible 
	        linkColor    : '#cc0000', // default is blue
	        theme        : 'light'    // or dark
	      });
    });


	// Delete Tweet
	$(".delete").click(function() {

		var tweet_id = $(this).closest('div').attr('id');
		tweet_id = tweet_id.replace("delete_", "");

			$.ajax({
				url: "db.php",
				type: "post",
				data: {
					funktion: "deleteTweet",
					tweet_id: tweet_id
				}
			}).done(function( msg ) {
				$("#"+tweet_id).fadeOut();
			});
	});

	// Quickfix Hide Loader
	setTimeout(function(){
		$("#loader").hide();
	}, 5000);


});	// Ready End