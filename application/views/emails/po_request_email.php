<html>
	<style>
		.jq_button
		{
			height:40px;
			width:200px;
			background-color:red;
		}
		
		.heading
		{
			color:#DD4B39;
			font-family:arial;
			font-weight:bold;
		}
	</style>
	<body style="font-family:arial;">
		<?=$po["issuer"]["full_name"]?> has created a PO for your approval. Click the button below to view the details.
		<br><br>
		<a style="text-decoration:none; padding:20px; height:100px; line-height:85px; background:#6295FC; border-radius:2px; color:white; font-weight:bold; margin-right:100px;" href="<?=base_url("index.php/purchase_orders/quick_po_approval/".$po["id"])?>" >View PO Details</a>
	</body>
</html>
