//this will move selected items from source list to destination list
function move_list_items(sourceid, destinationid)
{
	$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);
	$('')
}

//this will move all selected items from source list to destination list
function move_list_items_all(sourceid, destinationid)
{
	$("#"+sourceid+" option").appendTo("#"+destinationid);
}
function validateFields(theform)
{
	String.prototype.fulltrim=function(){return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');};
	if(theform.to_tables.value == '')
	{
		alert('Please select atleast one table.');
		theform.from_tables.focus();
		return false;
	}
	if(theform.save_report.value == "Yes" && theform.report_name.value.fulltrim() == ""){
		alert('Please enter report name.');
		theform.report_name.value = '';
		theform.report_name.focus();
		return false;
	}

	return true;
}

function getColumnOfTable(tableName)
{
	if(this.value!=''){
		$.post(SITE_URL+'getTableColumn',{tableName:tableName},function(data){
			$('#from_select_list').html(data);
		});
	}
}

function getColumnsForMultiselect()
{
	var columns = "";
	var tables = "<option value=''>-Select Option-</option>";
	loader();
	$("#to_tables option:selected").each(function () {
		$.post(SITE_URL+'getTableColumn',{tableName:$(this).attr('value')}, function(data){
			columns +=data;
		});
		tables += "<option value='"+$(this).attr('value')+"'>"+$(this).attr('value')+"</option>";
	});
	//alert('columns:-'+columns);
	setTimeout(function(){
		$('#from_columns').html(columns);
		$('#conditionField').html("<option value=''>-Select Option-</option>"+columns);
		$('#conditionFieldRight').html("<option value=''>-Select Option-</option>"+columns);
		$('#sort_by').html("<option value=''>-Select Option-</option>"+columns);
		$('#to_columns').html('');
		$('#query_condition').val('')
		$(".loader-holder").hide();
	},2000);
	
}

function getQueryCondition()
{
	var condition = '';
	if($('#conditionType').val()=='' || $('#conditionField').val() == '' || $('#condition').val() == '' || ( ($('#inputValueType').val() == 'input' && $('#inputValue').val() == '') || ($('#inputValueType').val() == 'colmn' && $('#conditionFieldRight').val() == ''))){
		alert('Please select the mandatory fields');
		return false;
	}else{
		condition += $('#conditionType').val()+' ';
		condition += $('#conditionField').val()+' ';
		condition += $('#condition').val()+' ';
		if($('#inputValueType').val()=='input'){
			condition += "'"+$('#inputValue').val()+"'";
		}else{
			condition += $('#conditionFieldRight').val();
		}
		$('#query_condition').val($('#query_condition').val()+'\n'+condition);
		$('#conditionType').val('');
		$('#conditionField').val('');
		$('#conditionFieldRight').val('');
		$('#condition').val('');
		$('#inputValue').val('');
	}
}

function remove_history(recordID)
{
	$.post(SITE_URL+'removehistoryrecord',{recordID:recordID},function(data){
		window.location = SITE_URL;
	});
}

function setMasterTable()
{
	$('#query_condition').val('');
}

function getInternetExplorerVersion()
{
var rv = -1; // Return value assumes failure.
if (navigator.appName == 'Microsoft Internet Explorer')
{
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
       rv = parseFloat( RegExp.$1 );
}
return rv;
}


function loader() {
	var browser=navigator.appName;
	if (browser != "Microsoft Internet Explorer") {
		$(".loader-holder").show();
	} else {
		$(document).ready(function() {
			var IEVersion = getInternetExplorerVersion();
			if(IEVersion>  7.0) {
				$(".loader-holder").show();
			}
		});
	}
}

function changeValueType(val)
{
	if(val == 'input'){
		$('#inputType').show();
		$('#fieldType').hide();
	}else{
		$('#inputType').hide();
		$('#fieldType').show();
	}
}

function redirect(url)
{
	window.location = url;
}
