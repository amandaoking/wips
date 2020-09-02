(function ($) {
    $(document).ready(function ($) {
        $.getJSON('https://api.twitch.tv/kraken/streams/' + baitStreamSettings.channelname + '?callback=?', function (a) {
            if (a.stream) {
				//alert('live');			
				$('#taAlertMe').append(' \
				<div id="talightbox"> \
					<div id="taInfo"> \
						<div id="taTwitchLogo"></div> \
						<h3>Our Twitch.TV stream is currently Live!</h3> \
							<p><a onClick="document.getElementById(\'talightbox\').remove();" href="http://www.twitch.tv/'+baitStreamSettings.channelname+'" target="_blank">Click here</a> to watch it now!</p> \
							<div id="taAlertClose" onClick="document.getElementById(\'talightbox\').remove();"></div> \
					</div></div>');
            }
        });
    });
})(jQuery);