<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<div style="width:750px;padding:25px;margin:0 auto;">
		<div style="margin:0 auto;">
			<img src="<?=base_url('images/lobos-logo-small.png')?>"/>
		</div>
		<h1>Lobos Summary Contract</h1>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver desires for Lobos to assist in obtaining ownership interest in trucking company</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver desires for Lobos to provide compliance consulting</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver grants Lobos all required authorities to perform agreed to services</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver understands that he is making a 6 week commitment to drive and remain in the truck</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver understands that breaking this commitment will result in an early termination fee (ETF) of $10,000</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver understands that 14 days notice is required to stop an auto-renewal of the agreement</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver understands that United Motor Carrier Cooperative will handle all settlements</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Circle one: Driver is probate driver Driver is student</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - If Driver is student, driver understands that weekly living stipends will be distributed on the settlement schedule. If Driver is probate driver, driver understands that for a maximum of 6 weeks, a finder's fee no greater than 50% of driver's truck profit will be paid to Lobos.</p>
		<p><span style="padding: 5px 15px;border: 4px solid blue;"></span> - Driver understands that his first FULL settlement will be on <?=$client['first_full_settlement_date']?></p>
		<p>I have read and agree to the terms of the Lobos Service Agreement. I have given special attention and consideration to the details outlined above.</p>
		<p>Driver Signature <span style="padding: 5px 200px;border: 4px solid red;"></span></p>
		<p>Date - <span style="text-decoration:underline;"><?=$date?></span></p>
		<p>Driver License # - <span style="text-decoration:underline;"><?=$client['license_number']?></span></p>
		<p>Social Security - <span style="text-decoration:underline;"><?=$driver['ssn']?></span></p>
		<p>Phone Number - <span style="text-decoration:underline;"><?=$driver['phone_number']?></span></p>
		<p>Emergency Contact Name - <span style="text-decoration:underline;"><?=$driver['emergency_contact_name']?></span></p>
		<p>Emergency Contact Phone # - <span style="text-decoration:underline;"><?=$driver['emergency_contact_phone']?></span></p>
		<p>Home Address - <span style="text-decoration:underline;"><?=$driver['home_address']?></span></p>
		<p>Email Address - <span style="text-decoration:underline;"><?=$driver['email']?></span></p>
	</div>
	<p style="page-break-before:always"></p>
</body>
</html>