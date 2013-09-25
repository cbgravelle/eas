<form method="post" action="/settings/email" class="form-ajax mcsubscribe form-inline">
	<div class="formels">
		<p>Join our mailing list!</p>
		<input type="email" name="mailinglist" placeholder="Enter your email address">
		<input class="btn" type="submit" value="Join!">
	</div>
	<script type="text/javascript">
		$('.mcsubscribe').bind('dataReceived', function(data) {
			doAction(data.action);
			console.log(data.error);
			$this = $(this);
			$this.children('.formels').hide();
			$this.children('.mcthanks').show();
			$this.children
		});
	</script>
	<div class="mcthanks">Thank you for joining the mailing list!</div>
</form>