<?php $this->load->view('header');?>
	<h1>Report Manager - Create/View Report</h1>
	<span class="createReportLink"><?php echo anchor('selecttable', 'Create Report');?></span>
	<div id="body">
		<div class="innerBoxLeft">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr><td class="tblHeading" colspan="3">Reports History</td></tr>
				<?php if(!empty($reportHistory)){?>
					<?php foreach($reportHistory AS $value){?>
						<tr>
							<td class="tblRecords"><span><?php echo anchor('selecttable/'.$value['pkHistoryID'], $value['ReportName']);?></span></td>
							<td class="tblRecords"><span><img src="<?php echo base_url()."public/includes/images/edit-icon.gif"?>" onclick="redirect('<?php echo 'edit-report/'.$value['pkHistoryID'];?>');"/></span></td>
							<td class="tblRecords"><span><img class="deleteButton" src="<?php echo base_url()."public/includes/images/delete.jpeg"?>" onclick="if(confirm('Are you sure want to delete this report?')){remove_history('<?php echo $value['pkHistoryID'];?>');}"/></span></td>
						</tr>
					<?php } ?>
					<tr><td class="tblRecords" colspan="3" align="center">Showing records <?php echo count($reportHistory)." of ".count($reportHistory);?></td></tr>
				<?php }else{?>
					<tr><td class="tblRecords" colspan="3" align="center">No Records Found.</td></tr>
				<?php }?>
			</table>
		</div>
	</div>
<?php $this->load->view('footer');?>