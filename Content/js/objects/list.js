var List = {

	init: function(){
		$("#close").on('click', function(){$("#box-insta").fadeOut();});
	},

	generateInstagram: function(name, hashtag){
		if(name != null && name != undefined && name != ''){
			$("#box-insta").hide().fadeIn();
			$("#generated").empty().html("#" + in_hashtag + " " + name ).hide().fadeIn();
		}
		else{
			$("#box-insta").hide().fadeIn();
			$("#generated").empty().html("#" + in_hashtag + " " + hashtag ).hide().fadeIn();
		}
	}
}

$(document).ready(function(){
	List.init();
});