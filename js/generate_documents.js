$(function(){
	$("#new_doc_dialog").dialog({
		autoOpen: false,
		resizable: false
	});
	
	$("#documentSelect").change(function(){
		var selected = $("#documentSelect option:selected").val();

		if(selected==0){
			hideDropdowns(0);
		}else if(selected==1){
			showDriverDropdown();
		}
	});
	
	$("#driverSelect").change(function(){
		var driver_id = $("#driverSelect option:selected").val();
		
		if(driver_id==0){
			hideDropdowns(1);
		}else{
			showCompanyDropdown();
		}
	});
	
	$("#companySelect").change(function(){
		var company_id = $("#companySelect option:selected").val();
		if(company_id==0){
			hideDropdowns(2);
		}else{
			showCompanyDropdown();
			showButton();
		}
		
	});
});//ready



	
function openDialog(){
	$('#new_doc_dialog' ).dialog('open');;
}

function generate_document(){
	var base_url = window.document.origin;
	var url = base_url + '/fleetsmarts/index.php/documents/generate_document'
	var data = $("#genDocForm").serialize();
	console.log(data);
	$.ajax({
		data:data,
		url:url,
		success:
			function(response){
				var win = window.open(url, '_blank');
			}
	});
}

function showDriverDropdown(){
	$("#driver_dropdown").removeClass("hidden");
}

function showCompanyDropdown(){
	$("#company_dropdown").removeClass("hidden");
}

function showButton(){
	$("#generateDocBtn").removeClass("hidden");
}

function hideDropdowns(id){
	if(id==0){
		$("#driver_dropdown").addClass("hidden");
		$("#company_dropdown").addClass("hidden");
		$("#generateDocBtn").addClass("hidden");
		$("#driverSelect").val(0);
		$("#companySelect").val(0);
	}else if(id==1){
		$("#company_dropdown").addClass("hidden");
		$("#generateDocBtn").addClass("hidden");
		$("#companySelect").val(0);
	}else if(id==2){
		console.log("here");
		$("#generateDocBtn").addClass("hidden");
	}
}