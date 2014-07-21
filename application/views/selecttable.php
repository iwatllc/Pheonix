<?php $this->load->view('header');?>
<h1>Report Manager - Create Report</h1>
<div id="body" class="selectTable">
	<?php if(isset($form_error)){
		echo "<p class='error'>".$form_error."</p>";
	} ?>
	<?php echo form_open('/selecttable',array('onsubmit'=>'return validateFields(this);','id'=>'selectTableForm','name'=>'selectTableForm'));?>
		<table cellpadding="5" cellspacing="5" border="1" width="100%">
			<tr><td colspan="6"><div class="tblHeading no-width">Select Tables</div></td></tr>
			<tr>
				<td colspan="2">
			    	<div class="tblHeading no-width">All Tables</div>
			        <select id="from_tables" multiple="multiple" name="from_tables">
			        	<?php foreach($database_reports_tables AS $key=>$value){?>
			        		<option value="<?php echo $key;?>"><?php echo '`'.$key.'`';?></option>
			        	<?php } ?>
			        </select>
			    </td>
			    <td colspan="2" style="padding-top:40px;" align="center">
			    	<input id="moveright" type="button" value=">>" onclick="move_list_items('from_tables','to_tables');getColumnsForMultiselect();" /><br/>
			    	<input id="moveleft" type="button" value="<<" onclick="move_list_items('to_tables','from_tables');getColumnsForMultiselect();" />
			    </td>
			    <td colspan="2">
			    	<div class="tblHeading no-width">Selected Tables</div>
			        <select id="to_tables" multiple="multiple" name="to_tables[]" onchange="getColumnsForMultiselect();"> 
			         	<?php if(isset($SelectedTablesName)){
			         		foreach($SelectedTablesName AS $key=>$value){
			         			?>
			         			<option value="<?php echo $value;?>"  selected><?php echo '`'.$value.'`';?></option>
			         			<?php
			         		}
			         	}?>
			        </select>
			    </td>
			</tr>
		</table>

		<table cellpadding="5" cellspacing="5" border="1" width="100%">
			<tr><td colspan="7"><div class="tblHeading no-width">Select Fields</div></td></tr>
			<tr>
				<td colspan="2">
			    	<div class="tblHeading no-width">All Columns</div>
			        <select id="from_columns" multiple="multiple" name="from_columns">
			        	<?php if(isset($UnselectedColumns)){
			         		foreach($UnselectedColumns AS $key=>$value){
			         			?>
			         			<option value="<?php echo $value;?>"><?php echo $value;?></option>
			         			<?php
			         		}
			         	}?>
			        </select>
			    </td>
			    <td colspan="3" style="padding-top:40px;text-align: center;">
			    	<input id="moveright" type="button" value=">>" onclick="move_list_items('from_columns','to_columns');" /><br/>
			    	<input id="moveleft" type="button" value="<<" onclick="move_list_items('to_columns','from_columns');" />
			    </td>
			    <td colspan="2">
			    	<div class="tblHeading no-width">Selected Columns</div>
			        <select id="to_columns" multiple="multiple" name="to_columns[]"> 
			         	<?php if(isset($SelectedColumns)){
			         		foreach($SelectedColumns AS $key=>$value){
			         			if($value != ''){
			         			?>
			         				<option value="<?php echo $value;?>" selected><?php echo $value;?></option>
			         			<?php
			         			}
			         		}
			         	}?>
			        </select>
			    </td>
			</tr>
		</table>
		<table cellpadding="5" cellspacing="5" border="1" width="100%">
			<tr><td colspan="8"><div class="tblHeading no-width">Set Conditions</div></td></tr>
			<tr>
				<td>
			    	<div class="tblHeading no-width">Type</div>
			        <select name="conditionType" id="conditionType">
			        		<option value="AND">AND</option>
			        		<option value="OR">OR</option>
			        </select>
			    </td>
				<td>
			    	<div class="tblHeading no-width">Field Name</div>
			        <select name="conditionField" id="conditionField">
			        	<option value=''>-Select Option-</option>
			        	<?php if(isset($AllColumns)){
			         		foreach($AllColumns AS $key=>$value){
			         			?>
			         			<option value="<?php echo $value;?>"><?php echo $value;?></option>
			         			<?php
			         		}
			         	}?>
			        </select>
			    </td>
			    <td colspan="2">
			    	<div class="tblHeading no-width">Condition</div>
			        <select id="condition" name="condition"> 
			        	<option value=''>-Select Option-</option>
			         	<option value="=">Equal</option>
			         	<option value="!=">Not Equal</option>
			         	<option value="<">Less Than</option>
			         	<option value=">">Greater Than</option>
			         	<option value="<=">Less Than Equals</option>
			         	<option value=">=">Greater Than Equals</option>
			        </select>
			    </td>
			    <td>
			    	<div class="tblHeading no-width">Value Type</div>
			        <select id="inputValueType" name="valueType" onchange="changeValueType(this.value)"> 
			         	<option value="input">Input Value</option>
			         	<option value="colmn">Table Column</option>
			        </select>
			    </td>
			    <td id="inputType" style="display:block">
			    	<div class="tblHeading no-width">Input</div>
			        <input type="text" name="inputValue" id="inputValue">
			    </td>
			    <td id="fieldType" style="display:none">
			    	<div class="tblHeading no-width">Field Name</div>
			        <select name="conditionFieldRight" id="conditionFieldRight">
			        	<option value=''>-Select Option-</option>
			        	<?php if(isset($AllColumns)){
			         		foreach($AllColumns AS $key=>$value){
			         			?>
			         			<option value="<?php echo $value;?>"><?php echo $value;?></option>
			         			<?php
			         		}
			         	}?>
			        </select>
			    </td>
			    <td>
			    	<div class="tblHeading no-width">&nbsp;</div>
			        <input type="button" name="Add" value="Add" onclick="getQueryCondition();">
			    </td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<div class="tblHeading no-width" style="float:left">Conditions:</div>
					<textarea name="query_condition" id="query_condition" cols="100" rows="10"><?php if(isset($AllConditions)){echo $AllConditions;}?></textarea>
				</td>
			</tr>
		</table>
		<table cellpadding="5" cellspacing="5" border="1" width="100%" class="saveSection">
			<tr>
				<td colspan="7" align="center">
					<div class="tblHeading">Report Name: <input type="text" name="report_name" id="report_name" value="<?php if(isset($ReportName)){ echo $ReportName; }?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<div class="tblHeading">Sort By: 
						<select name="sort_by" id="sort_by">
							<option value=''>-Select Option-</option>
							<?php if(isset($AllColumns)){
			         		foreach($AllColumns AS $key=>$value){
			         			if($SelectedSortByColumnName == $value){
			         				$selected = "selected";
			         			}else{
			         				$selected = "";
			         			}
			         			?>
			         			<option value="<?php echo $value;?>" <?php echo $selected;?>><?php echo $value;?></option>
			         			<?php
			         		}
			         	}?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<div class="tblHeading">Sort Order: 
						<select name="sort_order">
							<option value=''>-Select Option-</option>
							<option value="ASC" <?php if(isset($SelectedSortOrder)&&$SelectedSortOrder == 'ASC'){echo "selected";}?>>Ascending</option>
							<option value="DESC" <?php if(isset($SelectedSortOrder)&&$SelectedSortOrder == 'DESC'){echo "selected";}?>>Descending</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<div class="tblHeading">Records Per Page: 
						<select name="record_per_page">
							<option value=''>-Select Option-</option>
							<option value="5" <?php if(isset($RecordsPerPage)&&$RecordsPerPage == '5'){echo "selected";}?>>5</option>
							<option value="10" <?php if(isset($RecordsPerPage)&&$RecordsPerPage == '10'){echo "selected";}?>>10</option>
							<option value="20" <?php if(isset($RecordsPerPage)&&$RecordsPerPage == '20'){echo "selected";}?>>20</option>
							<option value="30" <?php if(isset($RecordsPerPage)&&$RecordsPerPage == '30'){echo "selected";}?>>30</option>
							<option value="50" <?php if(isset($RecordsPerPage)&&$RecordsPerPage == '50'){echo "selected";}?>>50</option>
							<option value="100" <?php if(isset($RecordsPerPage)&&$RecordsPerPage == '100'){echo "selected";}?>>100</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<div class="tblHeading">
						Save Report: 
						<select name="save_report">
							<option value=''>-Select Option-</option>
							<option value="Yes" <?php if(isset($SaveReport)&&$SaveReport == 'Yes'){echo "selected";}?>>Yes</option>
							<option value="No" <?php if(isset($SaveReport)&&$SaveReport == 'No'){echo "selected";}?>>No</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center">
					<?php if(isset($fkReportHistoryID)){ ?>
						<input type="hidden" value="<?php echo $fkReportHistoryID;?>" name="reportID">
					<?php } ?>
					<input type="submit" value="Submit" name="selectTables">
				</td>
			</tr>
		</table>
	<?php echo form_close();?>
</div>
<div class="loader-holder">
	<img src='<?php echo base_url()."public/includes/images/loader.gif";?>' alt="loader" width="80" height="80">
</div>
<?php $this->load->view('footer');?>
