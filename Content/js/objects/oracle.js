var Oracle = {

	count_songs: 0,

	List: {
		generateInstagram: function(name, hashtag){
			if(name != null && name != undefined && name != ''){

			}
		},

		copy: function(){
			
		}
	},

	Player : {
		init: function(){
	        $("#play").on('click',      function(){ jwplayer().play(); });
	        $("#stop").on('click',      function(){ jwplayer().pause(); });
	        $("#back").on('click',      function(){ jwplayer().playlistPrev(); });
	        $("#next").on('click',      function(){ jwplayer().playlistNext(); });
	        $("#next-list").on('click', function(){ Oracle.pullList(); });
			$("#up-vol").on('click',    function(){ Oracle.Player.changeVolume(2); });
            $("#down-vol").on('click',  function(){ Oracle.Player.changeVolume(1); });
            $("#mute").on('click',      function(){ Oracle.Player.setMute(); });
            window.setInterval('Oracle.Player.updateTime()', 500);
		},

		setVolume: function(vol){
			if(vol >= 0 && vol <= 100)
				jwplayer().setVolume(vol);
			$("#volume").empty().html('Volume: ' + vol);
		},

		setMute: function(){
			var isMute = !jwplayer().getMute();
			jwplayer().setMute(isMute);
			if(isMute){
				$("#volume").empty().html('Mudo');
				$("#mute").empty().html('Remover Mudo');
			}
			else{
				$("#volume").empty().html('Volume: ' + jwplayer().getVolume());
				$("#mute").empty().html('Mudo');
			}
		},

		changeVolume: function(action) {
			var vol = jwplayer().getVolume();
			if ((action == 1 && vol == 10) || (action == 2 && vol == 100))
				return;
			jwplayer().setVolume(action == 1 ? vol - 10 : vol + 10);
			$("#volume").empty().html('Volume: ' + (action == 1 ? vol - 10 : vol + 10));
		},

		updateTime: function(){
			var duration = jwplayer().getDuration();
			var elapsed  = jwplayer().getPosition();
			var percent  = ((100*elapsed)/duration);
			$("#music-duration").empty().html(Oracle.Player.formatTime(duration));
			$("#music-elapsed").empty().html(Oracle.Player.formatTime(elapsed));
			$("#progress").attr("value", percent);
			$("#progress").empty().html(percent + "%");
		},

		formatTime: function(seconds){
			var sec_num = parseInt(seconds, 10);
		    var hours   = Math.floor(sec_num / 3600);
		    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
		    var seconds = sec_num - (hours * 3600) - (minutes * 60);

		    if (hours   < 10) {hours   = "0"+hours;}
		    if (minutes < 10) {minutes = "0"+minutes;}
		    if (seconds < 10) {seconds = "0"+seconds;}
		    var time    = minutes+':'+seconds;
		    return time;
		}
	},

	init: function(){
		$("#loading").fadeIn();
		$.ajax({
			type: "POST",
			url: 'musics.php',
			success: function (data) {
				jwplayer("player").setup({
			        'flashplayer': './Content/swf/player.swf',
				    'id': 'playerID',
				    'width': '560',
				    'height': '24',
				    'controlbar': 'bottom',
			        'playlist': JSON.parse(data),
			        repeat: 'list',
					shuffle: 'true',
					events: {
			 			onPlay: function(event){
			 				$("#now").empty().html(jwplayer().getPlaylistItem().title).hide().fadeIn();
			 			},
			 			onComplete: function(event){
							Oracle.pullList();
			 			}
			 		}
		    	});
		    	Oracle.count_songs = JSON.parse(data).length;
		    	Oracle.Player.init();
				$.ajax({
					type: "POST",
					url: 'scan-twitter.php',
					success: function (data) {
						$.ajax({
							type: "POST",
							url: 'scan-instagram.php',
							success: function (data) {
								window.setInterval('Oracle.scanTwitter()', time_reload_tw);
								window.setInterval('Oracle.scanInstagram()', time_reload_in);
								Oracle.pullList();					
							}
						});
					}
				});
			}
		});
	},

	scanTwitter: function(){
		$.ajax({
			type: "POST",
			url: 'scan-twitter.php'
		});
	},

	scanInstagram: function(){
		$.ajax({
			type: "POST",
			url: 'scan-instagram.php'
		});
	},

	pullList: function(){
		$("#who").empty().fadeOut();
		$.ajax({
			type: "POST",
			url: 'pull-list.php',
			success: function (data) {
				if(data != ""){
					var aux_data      = data.split("#");
					var playlist_item = aux_data[0];
					var who           = aux_data[1];
					var profile       = aux_data[2];
					var img_in        = aux_data[3];
					var type          = aux_data[4];
					type = type.replace(/(\r\n|\n|\r)/gm,"");
					type = type.replace(/\s+/g,"");
					jwplayer().playlistItem(playlist_item);
					$("#who").empty().html('Música sugerida por: ' + who).hide().fadeIn();
					if(type == 'IN'){
						$("#profile").empty().html('<img src="' + profile + '"/>').hide().fadeIn();
						$("#img-in").empty().html('<img src="' + img_in + '"/>').hide().fadeIn();
						$("#social").empty().html('Instagram').hide().fadeIn();
					}
					else{
						$("#profile").empty().html('<img src="' + profile + '"/>').hide().fadeIn();
						$("#img-in").fadeOut();
						$("#social").empty().html('Twitter').hide().fadeIn();
					}
				}
				else{
					var rand = Math.floor((Math.random() * Oracle.count_songs) + 1);
					jwplayer().playlistItem(rand);
					$("#who").empty().html('Escolhemos uma música para você ;)').hide().fadeIn();
					$("#profile").fadeOut();
					$("#img-in").fadeOut();
					$("#social").fadeOut();
				}
				$("#loading").fadeOut();
				$("#controls").fadeIn();
				Oracle.Player.setVolume(100);
			},
		});
	}
}

$(document).ready(function(){
	Oracle.init();
});