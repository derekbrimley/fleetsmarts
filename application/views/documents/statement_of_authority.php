<div style="width:750px;padding:25px;margin:0 auto;">
	<h1>STATEMENT OF AUTHORITY OF
	<?=$company['company_name']?></h1>
	<p>A LIMITED LIABILITY COMPANY</p>
	<p>Pursuant to Section 48-3a-302 of the Utah Revised Limited Liability Company Act, Utah Code	Annotated (the "Act"), <?=$company['company_name']?> (the "Company") hereby states the following:</p>
	<p>ARTICLE I</p>
	<p>REGISTERED OFFICE AND AGENT</p>
	<p>The Company's Registered Agent is Lobos Interstate Services, located at 1922 W 200 N, Lindon, UT 84042.</p>
	<p>ARTICLE II</p>
	<p>PERSONS WITH AUTHORITY TO ACT ON BEHALF OF THE COMPANY</p>
	<p>The person with general authority to act on behalf of the Company is the Company's Manager, <?=$fullname?>.</p>
	<p>The Manager has or will delegate certain authorities to individuals and/or entities to provide certain services on behalf of the Company customary in the trucking industry.</p>
	<p>ARTICLE III</p>
	<p>AUTHORITY AND LIMITATIONS OF MANAGER</p>
	<p>The authority of the Manager, <?=$fullname?>, and the limitations on his authority are:</p>
	<p>To execute any instrument transferring real property held in the name of the Company; and</p>
	<p>To enter into any other transaction on behalf of, or otherwise act for and bind, the Company.</p>
	<p>The foregoing authority is without limitation affecting any third persons other than the Company and its members or holders of transferable interests.</p>
	<p>Dated the <?=date('jS',strtotime($date))?> of <?=date('F',strtotime($date))?>, <?=date('Y',strtotime($date))?></p>
	<div style="text-decoration:underline"><?=$company['company_name']?></div>
	<p>(NAME OF COMPANY)</p>
	<p>Signature: <div style="width:500px;height:25px;padding:10px;border:5px solid red;"></div></p>
	
	<p>Name: <?=$fullname?></p>
	<p>Its: Manager</p>
</div>
<p style="page-break-before:always">