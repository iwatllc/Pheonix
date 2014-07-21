<?php
class CreateReportModel extends CI_Model 
{
	function getReportHistory()
    {
    	$this->db->select(array('pkHistoryID','ReportName','SqlQuery','DateCreated'));
    	$records = $this->db->get(TABLE_REPORT_HISTORY);
    	$arrReportHistory = array();
    	if ($records->num_rows()>0)
	    { 
	        foreach ($records->result_array() as $row)
	        {
	        	$arrReportHistory[]= $row;
	        }
	        $records->free_result();
	    }

	    return $arrReportHistory;
    }

    function getTableColumn($tableName)
    {
    	$columns = "";
    	if(!empty($tableName))
    	{
    		$records = $this->db->query("SELECT COLUMN_NAME FROM   information_schema.columns WHERE  table_name = '".$tableName."' AND TABLE_SCHEMA='".$this->db->database."'");
    		if ($records->num_rows()>0)
		    { 
		        foreach ($records->result_array() as $row)
		        {
		        	$columns .= "<option value='`".$tableName."`.`".$row['COLUMN_NAME']."`'>`".$tableName."`.`".$row['COLUMN_NAME']."`</option>";
		        }
		        $records->free_result();
		    }
    	}
    	return $columns;
    }

    public function getAllColumns($tableName)
    {
    	$columns = array();
    	if(is_array($tableName)){
    		foreach($tableName AS $table){
    			$records = $this->db->query("SELECT COLUMN_NAME FROM   information_schema.columns WHERE  table_name = '".$table."' AND TABLE_SCHEMA='".$this->db->database."'");
	    		if ($records->num_rows()>0)
			    { 
			        foreach ($records->result_array() as $row)
			        {
			        	$columns[] = "`".$table."`.`".$row['COLUMN_NAME']."`";
			        }
			        $records->free_result();
			    }
    		}
    	}
    	return $columns;
    }


    function insertInToHistory($data)
    {

        $this->db->insert(TABLE_REPORT_HISTORY,$data);
        return true;
    }

    function editInToHistory($data,$id)
    {

        $this->db->update(TABLE_REPORT_HISTORY,$data,array('pkHistoryID'=>$id));
        return true;
    }

    function saveAllReportsFields($insertId,$mode)
    {
        if($insertId){
            $arrTables = array();
            $allTables = $this->config->item('database_reports_tables');
            foreach($allTables AS $key=>$value){
                $arrTables[] = $key;
            }
            $arrColumns = array(
                'AllTablesName' => implode(",",$arrTables),
                'SelectedTablesName' => isset($_POST['to_tables']) ? implode(",",$_POST['to_tables']) : '',
                'SelectedColumns' => isset($_POST['to_columns']) ? implode(",",$_POST['to_columns']) : '',
                'AllConditions' => isset($_POST['query_condition']) ? $_POST['query_condition'] : '',
                'SelectedSortByColumnName' => isset($_POST['sort_by']) ? $_POST['sort_by'] : '',
                'SelectedSortOrder' => isset($_POST['sort_order']) ? $_POST['sort_order'] : '',
                'RecordsPerPage' => isset($_POST['record_per_page']) ? $_POST['record_per_page'] : '',
                'SaveReport' => isset($_POST['save_report']) ? $_POST['save_report'] : '',
                );
            if($mode == "Add"){
                $arrColumns['fkReportHistoryID'] = $insertId;
                $this->db->insert(TABLE_HISTORY_REPORTS_FIELDS,$arrColumns);
            }else{
                $this->db->update(TABLE_HISTORY_REPORTS_FIELDS,$arrColumns,array('fkReportHistoryID' => $insertId));
            }
            return true;
        }
    }

    function getAllFormsFieldsOfHistoryReport($reportID)
    {
        $arrData = array();
        $newDataArr = array();
        $this->db->select("*");
        $this->db->from(TABLE_HISTORY_REPORTS_FIELDS);
        $this->db->join(TABLE_REPORT_HISTORY,'fkReportHistoryID = pkHistoryID');
        $this->db->where(array('fkReportHistoryID' => $reportID));
        $arrResult = $this->db->get();
        if ($arrResult->num_rows()>0)
        { 
            foreach ($arrResult->result_array() as $row)
            {
                $arrData= $row;
            }
            $arrResult->free_result();
        }
        //echo $this->db->last_query(); die;
        //All Tables
        $tables = array_diff(explode(",",$arrData['AllTablesName']),explode(",",$arrData['SelectedTablesName']));
        foreach($tables AS $key=>$value){
            $arrData['database_reports_tables'][$value] = $value;
        }
        $arrData['SelectedTablesName'] = explode(",",$arrData['SelectedTablesName']);
        $arrData['AllColumns'] = $this->getAllColumns($arrData['SelectedTablesName']);
        $arrData['SelectedColumns'] = explode(",",$arrData['SelectedColumns']);
        $arrData['UnselectedColumns'] = array_diff($arrData['AllColumns'], $arrData['SelectedColumns']);
        
        return $arrData;
    }

    function getCustomQueryResult()
    {
        $tables = "";
        $columns = "";
        $where = "1 ";
        $sortBy = "";
        $limit = "";
        $arrResult = array();
        $tablesArr = $this->input->post('to_tables');
        //Set selected columns
        if($this->input->post('to_columns')){
            $count=0;
            foreach ($this->input->post('to_columns') as $value) {
                if($count>0){
                    $columns .= ",".$value." AS ".str_replace('`.`', '_', $value); 
                }else{
                    $count++;
                    $columns .= $value." AS ".str_replace('`.`', '_', $value);
                }
            }
        }else{
            $count = 0;
            $arrColumns = $this->getAllColumns($tablesArr);
            foreach ($arrColumns as $value) {
                if($count>0){
                    $columns .= ",".$value." AS ".str_replace('`.`', '_', $value); 
                }else{
                    $count++;
                    $columns .= $value." AS ".str_replace('`.`', '_', $value);
                }
            }
        }
        
        $count = 0;
        foreach($tablesArr as $tbl){
            if($count>0){
                $tables .=",";    
            }
            $tables .="`".$tbl."`"; 
            $count++;
        }
        
        //Set where condition
        if($this->input->post('query_condition'))
        {
            $where .= $this->input->post('query_condition');
        }

        //Set sorting order column
        if($this->input->post('sort_by'))
        {
            $sortBy .= " ORDER BY ".$this->input->post('sort_by')." ".$this->input->post('sort_order');
        }

        $query = "SELECT ".$columns." FROM ".$tables." WHERE ".$where." ".$sortBy." ".$limit;
        $records = $this->db->query($query);
        if ($records->num_rows()>0)
        { 
            foreach ($records->result_array() as $row)
            {
                $arrResult[] = $row;
            }
            $records->free_result();
        }

        return $arrResult;
    }

    function getQueryResult($query)
    {
        $records = $this->db->query($query);
        $arrResult = array();
        if ($records->num_rows()>0)
        { 
            foreach ($records->result_array() as $row)
            {
                $arrResult[] = $row;
            }
            $records->free_result();
        }

        return $arrResult;
    }

    function getHistoryQueryById($historyID)
    {
        $result = '';
        $this->db->where(array('pkHistoryID'=>$historyID));
        $records = $this->db->get(TABLE_REPORT_HISTORY);
        if ($records->num_rows()>0)
        { 
            foreach ($records->result_array() as $row)
            {
                $result = $row['SqlQuery'];
            }
            $records->free_result();
        }
        return $result;
    }

    function createHistoryTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `ReportHistory` (
                `pkHistoryID` int(100) NOT NULL AUTO_INCREMENT,
                `ReportName` varchar(255) NOT NULL,
                `SqlQuery` text NOT NULL,
                `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`pkHistoryID`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        $this->db->query($query);

        $query = "CREATE TABLE IF NOT EXISTS `HistoryReportsFields` (
                  `pkHistoryReportsFieldID` int(11) NOT NULL AUTO_INCREMENT,
                  `fkReportHistoryID` int(11) NOT NULL,
                  `AllTablesName` text NOT NULL,
                  `SelectedTablesName` text NOT NULL,
                  `SelectedColumns` text NOT NULL,
                  `AllConditions` text NOT NULL,
                  `SelectedSortByColumnName` varchar(255) NOT NULL,
                  `SelectedSortOrder` varchar(255) NOT NULL,
                  `RecordsPerPage` int(11) NOT NULL,
                  `SaveReport` varchar(255) NOT NULL,
                  PRIMARY KEY (`pkHistoryReportsFieldID`)
                ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        $this->db->query($query);
    }
}
?>
