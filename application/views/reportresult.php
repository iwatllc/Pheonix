<?php $this->load->view('header');?>
	<h1>Report Manager - Resulted Report</h1>
	<div id="body" class="results">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="main-table">
			<tr>
				<?php
			if(count($results)>0)
			{
				foreach($results AS $value)
				{
					foreach($results[0] as $k=>$v){
						echo '<th class="recordHeading">'.$k.'</th>';	
					}
					break;
				}
			}else{
				echo '<td class="recordsRow" style="text-align:center;">No Records Found</td>';
			}
				?>		
			</tr>
			<?php
				foreach($results AS $value)
				{
					echo "<tr class='records'>";
					foreach($value as $k=>$v){
						echo '<td class="recordsRow" align="center">'.$v."</td>";	
					}
					echo "</tr>";
				}
			?>
		</table>
		<div class="actionBtns">
			<span>
				<?php if(count($results)>0){?>
				<input type="button" value="Export To Excel" class="btn" onclick="window.location='<?php echo base_url()."getexcelexport/".$session_query_id;?>'"/>
				<?php } ?>
				<input type="button" value="Home" class="btn" onclick="window.location = SITE_URL;"/>
			</span>
		</div>
	</div>
	<script>
		$('document').ready(function(){
			var limit = <?php echo $this->input->post('record_per_page')!='' ? $this->input->post('record_per_page') : 5;?>;
			$('#main-table').oneSimpleTablePagination({rowsPerPage: limit});
		});
	</script>
<?php $this->load->view('footer');?>
