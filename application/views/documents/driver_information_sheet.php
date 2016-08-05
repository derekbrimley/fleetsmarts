<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<div style="width:750px;padding:25px;margin:0 auto;">
		<div><?=$date?></div>
		<h1>Lobos Driver Information Sheet</h1>
		<table>
			<tr>
				<td>First Name</td>
				<td><?=$driver['f_name']?></td>
			</tr>
			<tr>
				<td>Last Name</td>
				<td><?=$driver['l_name']?></td>
			</tr>
			<tr>
				<td>Home Phone</td>
				<td><?=$driver['home_phone']?></td>
			</tr>
			<tr>
				<td>Cell Phone</td>
				<td><?=$driver['phone_number']?></td>
			</tr>
			<tr>
				<td>Cell Phone Carrier</td>
				<td><?=$driver['phone_carrier']?></td>
			</tr>
			<tr>
				<td>Home Address</td>
				<td><?=$driver['home_address']?></td>
			</tr>
			<tr>
				<td>City</td>
				<td><?=$driver['home_city']?></td>
			</tr>
			<tr>
				<td>State</td>
				<td><?=$driver['home_state']?></td>
			</tr>
			<tr>
				<td>Zip</td>
				<td><?=$driver['home_zip']?></td>
			</tr>
			<tr>
				<td>License Number</td>
				<td><?=$client['license_number']?></td>
			</tr>
			<tr>
				<td>License State</td>
				<td><?=$client['license_state']?></td>
			</tr>
			<tr>
				<td>License Expiration</td>
				<td><?=date("F j, Y",strtotime($client['license_expiration']))?></td>
			</tr>
			<tr>
				<td>License Class</td>
				<td>A</td>
			</tr>
			<tr>
				<td>Social Security Number</td>
				<td><?=$driver['ssn']?></td>
			</tr>
			<tr>
				<td>Date of Birth</td>
				<td>
					<?php if(!is_null($driver['date_of_birth'])): ?>
						<?=date("F j, Y",strtotime($driver['date_of_birth']))?>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<td>Emergency Contact</td>
				<td><?=$driver['emergency_contact_name']?></td>
			</tr>
			<tr>
				<td>Emergency Contact Phone</td>
				<td><?=$driver['emergency_contact_phone']?></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><?=$driver['email']?></td>
			</tr>
			<tr>
				<td>Years of Experience</td>
				<td><?=$client['years_of_experience']?></td>
			</tr>
			<tr>
				<td>Desired Company Name</td>
				<td><?=$client['desired_company_name']?></td>
			</tr>
			<tr>
				<td>First Full Settlement Date</td>
				<td><?=$client['first_full_settlement_date']?></td>
			</tr>
		</table>
	</div>
	<p style="page-break-before: always"></p>
</body>
</html>