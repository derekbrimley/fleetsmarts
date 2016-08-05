<div>
	<div>
		Thanks for clocking in! The email was sent at <?= date('g:i A',$email_sent_datetime)?>, and you responded at <?= date('g:i A',$datetime)?>. It took you <?= $difference_text ?> to respond.
	</div>
	<div>
		<?php if($difference < 10): ?>
			Thanks for verifying!
		<?php elseif($difference >= 10): ?>
			Unfortunately, it took too long to respond, and you have been logged out of the system. Click <a href="<?= base_url('index.php/time_clock') ?>" >here</a> to log back in.
		<?php endif ?>
	</div>
</div>