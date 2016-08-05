<head>
	<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
	<title><?php echo $title;?></title>
</head>
	<div style="padding-left:80px; font-family:arial;">
		<br>
		<br>
		<br>
		<span style="margin-left:120px; font-size:50px; font-family:arial;">
			COVER SHEET
		</span>
		<br>
		<br>
		<br>
		<span style="font-size:18px;">
			Number of pages including the cover
			<span style="padding-left:125px; padding-right:125px; font-size: 18px; border-bottom: solid 1pt;"><?=$number_of_docs?></span>
		</span>
		<br>
		<br>
		<span style="font-size:18px;">
			Factoring client company name:
			<span style="padding-left:45px; padding-right:45px; font-size: 18px; border-bottom: solid 1pt;"><?=$load["billed_under_carrier"]["company_name"]?></span>
		</span>
		<br>
		<br>
		<span style="font-size:18px; font-style:italic; font-weight:bold; border-bottom: solid 2pt;">Load information (who are we invoicing)</span>
		<br>
		<br>
		<span style="font-size:18px;">
			Broker/Shipper Company Name:
			<br>
			<span style="display:block; text-align:center; width:550px; font-size: 18px; border-bottom: solid 1pt;"><span style="margin:0 auto;"><?=$load["broker"]["customer_name"]?></span></span>
		</span>
		<br>
		<span style="font-size:18px;">
			Load number:
			<span style="padding-left:65px; padding-right:65px; font-size: 18px; border-bottom: solid 1pt;"><?=$load["customer_load_number"]?></span>
		</span>
		<br>
		<br>
		<br>
		<br>
		<span style="font-size:18px; font-style:italic; font-weight:bold; border-bottom: solid 2pt;">Funding information</span>
		<br>
		<br>
		<span style="font-size:18px;">
			How many loads are you submitting for payment today?
			<span style="padding-left:35px; padding-right:35px; font-size: 18px; border-bottom: solid 1pt;">?</span>
		</span>
		<br>
		<br>
		<span style="font-size:18px;">
			Is this load the last load being submitted or will there be more coming<br>later today? Please choose 1 answer below
		</span>
		<br>
		<br>
		<span style="font-size:18px;">
			<span style="padding-left:20px; padding-right:20px; font-size: 18px; border-bottom: solid 1pt;">X</span>
			I have more loads coming later today. Please hold my paperwork<br>and fund me for all of my loads together
		</span>
		<br>
		<br>
		<span style="font-size:18px;">
			<span style="padding-left:25px; padding-right:25px; font-size: 18px; border-bottom: solid 1pt;"></span>
			I have more loads coming in today but I need to get paid for this<br>loas asap. I understand I will have to pay multiple ComData fees
		</span>
		<br>
		<br>
		<span style="font-size:18px;">
			<span style="padding-left:25px; padding-right:25px; font-size: 18px; border-bottom: solid 1pt;"></span>
			This load is my final load for today. Please fund all my loads as<br>quickly as possible.
		</span>
		<br>
		<br>
		<span style="margin-left:65px; font-size:18px;">
			Send paperwork to one of the following for processing:
		</span>
		<br>
		<br>
		<span style="font-size:18px;">
			By email to : <span style="border-bottom: solid 1pt;">documents@factorloads.com</span> or by fax to (435) 657-1861
		</span>
		<br>
		<br>
		<br>
		<br>
	</div>
