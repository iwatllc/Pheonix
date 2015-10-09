<?php
class CreateReportModel extends CI_Model
{
    /*
    Function : getReportHistory()
    Desc :  This function is used to get all data of a report.
    Inputs : None.
    Return : Array fetched data.
    */
    function getReportHistory($search = NULL)
    {
        $this->db->select(array('pkHistoryID','ReportName','SqlQuery','DateCreated'));

        if($search != NULL)
            $this->db->where("ReportName LIKE ".$this->db->escape($search));

        $records = $this->db->get('reporthistory');
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
    /*
    Function : getTableColumn()
    Desc :  This function is used to get all column names of a table.
    Inputs : Table name.
    Return : Array of all columns.
    */
    function getTableColumn($tableName)
    {
        $columns = "";
        if(!empty($tableName))
        {
            $records = $this->db->query
            (
                "SELECT ".
                "COLUMN_NAME ".
                "FROM information_schema.columns ".
                "WHERE ".
                "table_name = '". $tableName. "' ".
                "AND TABLE_SCHEMA = '". $this->db->database. "'"
            );

            if ($records->num_rows()>0)
            {
                foreach ($records->result_array() as $row)
                {
                    $columns .= "<option value='`". $tableName. "`.`". $row['COLUMN_NAME']. "`'>`". $tableName. "`.`". $row['COLUMN_NAME']. "`</option>";
                }

                $records->free_result();
            }
        }
        return $columns;
    }
    /*
    Function : getAllColumns()
    Desc :  This function is used to get all column names of a table.
    Inputs : Table name.
    Return : Array of all columns.
    */
    public function getAllColumns($tableName)
    {
        $columns = array();
        if(is_array($tableName)) {
            foreach($tableName AS $table) {
                $records = $this->db->query
                (
                    "SELECT ".
                    "COLUMN_NAME ".
                    "FROM information_schema.columns ".
                    "WHERE ".
                    "table_name = '". $table. "' ".
                    "AND TABLE_SCHEMA = '". $this->db->database. "'"
                );

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
    /*
    Function : insertInToHistory()
    Desc :  This function is used to add data to history table.
    Inputs : Data to insert.
    Return : None.
    */
    function insertInToHistory($data)
    {
        $this->db->insert('reporthistory',$data);
        return true;
    }
    /*
    Function : editInToHistory()
    Desc :  This function is used to edit data in history table.
    Inputs : Data to insert, id of the report.
    Return : None.
    */
    function editInToHistory($data,$id)
    {
        $this->db->update('reporthistory',$data,array('pkHistoryID'=>$id));
        return true;
    }
    /*
    Function : saveAllReportsFields()
    Desc :  This function is used to edit data in history table.
    Inputs : Data to insert, id of the report.
    Return : None.
    */
    function saveAllReportsFields($insertId,$mode)
    {
        if($insertId){
            $arrTables = array();
            $allTables = $this->config->item('database_reports_tables');
            foreach($allTables AS $key=>$value){
                $arrTables[] = $key;
            }
            //echo '<pre>';echo print_r($arrTables);echo '</pre>';exit;
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
            //echo '<pre>';echo print_r($arrColumns);echo '</pre>';exit;
            if($mode == "Add"){
                $arrColumns['fkReportHistoryID'] = $insertId;
                $this->db->insert('historyreportsfields',$arrColumns);
            }else{
                $this->db->update('historyreportsfields',$arrColumns,array('fkReportHistoryID' => $insertId));
            }
            return true;
        }
    }
    /*
    Function : getAllFormsFieldsOfHistoryReport()
    Desc :  This function is used to get data of a report.
    Inputs : id of the report.
    Return : Array of data.
    */
    function getAllFormsFieldsOfHistoryReport($reportID)
    {
        $arrData = array();
        $newDataArr = array();
        $this->db->select("*");
        $this->db->from('historyreportsfields');
        $this->db->join('reporthistory','fkReportHistoryID = pkHistoryID');
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
        //echo '<pre>';echo print_r($arrData);echo '</pre>';exit;
        //echo $this->db->last_query(); die;
        //All Tables
        $tables = array_diff(explode(",",$arrData['AllTablesName']),explode(",",$arrData['SelectedTablesName']));
        //echo '<pre>';echo print_r($tables);echo '</pre>';exit;
        if(!empty($tables)){
            foreach($tables AS $key=>$value){
                $arrData['database_reports_tables'][$value] = ucfirst($value);
            }
        }
        else{
            $arrData['database_reports_tables'] = array();
        }

        $arrData['SelectedTablesName'] = explode(",",$arrData['SelectedTablesName']);
        foreach($arrData['SelectedTablesName'] AS $key=>$value){
            $arrData['SelectedTables'][$value] = $value;
        }

        $arrData['AllColumns'] = $this->getAllColumns($arrData['SelectedTables']);
        //echo '<pre>';echo print_r($arrData['AllColumns']);echo '</pre>';exit;
        $arrData['SelectedColumns'] = explode(",",$arrData['SelectedColumns']);
        $arrData['UnselectedColumns'] = array_diff($arrData['AllColumns'], $arrData['SelectedColumns']);

        return $arrData;
    }
    /*
    Function : getCustomQueryResult()
    Desc :  This function is used to get report result data.
    Inputs : None.
    Return : Array of data.
    */
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
    /*
    Function : getRecPerPage()
    Desc :  This function is used to get record per page data.
    Inputs : report history id.
    Return : Array of data.
    */
    function getRecPerPage($historyID)
    {
        $this->db->select('*');
        $this->db->from('historyreportsfields');
        $this->db->where('fkReportHistoryID', $historyID);
        $query = $this->db->get();
        $result = $query->row();
        return $result->RecordsPerPage;
    }
    /*
    Function : getCustomQueryResultByHistoryId()
    Desc :  This function is used to get report result.
    Inputs : report history id.
    Return : Array of data.
    */
    function getCustomQueryResultByHistoryId($historyID)
    {
        $tables = "";
        $columns = "";
        $where = "1 ";
        $sortBy = "";
        $limit = "";
        $arrResult = array();

        $this->db->select('*');
        $this->db->from('historyreportsfields');
        $this->db->where('fkReportHistoryID', $historyID);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            $result = array();
            $result = $query->result_array();
            $SelectedTablesName = explode(',', $result[0]['SelectedTablesName']);
            //echo '<pre>';echo print_r($SelectedTablesName);echo '</pre>';exit;
            $tablesArr = $SelectedTablesName;
            //Set selected columns
            if($result[0]['SelectedColumns'] != ''){
                $SelectedColumns = explode(',', $result[0]['SelectedColumns']);
                $count=0;
                foreach ($SelectedColumns as $value) {
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
            if($result[0]['AllConditions'])
            {
                $where .= $result[0]['AllConditions'];
            }
            //Set sorting order column
            if($result[0]['SelectedSortByColumnName'] != 0)
            {
                $sortBy .= " ORDER BY ".$result[0]['SelectedSortByColumnName']." ".$result[0]['SelectedSortOrder'];
            }
            $query = "SELECT ".$columns." FROM ".$tables." WHERE ".$where." ".$sortBy;
            $records = $this->db->query($query);
            if ($records->num_rows()>0)
            {
                foreach ($records->result_array() as $row)
                {
                    $arrResult[] = $row;
                }
                $records->free_result();
            }
            //echo '<pre>';echo print_r($arrResult);echo '</pre>';exit;
            return $arrResult;
        }
    }
    /*
    Function : getQueryResult()
    Desc :  This function is used to get result from a query.
    Inputs : Query.
    Return : Array of data.
    */
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
    /*
    Function : getHistoryQueryById()
    Desc :  This function is used to get history details.
    Inputs : report history id.
    Return : Array of data.
    */
    function getHistoryQueryById($historyID)
    {
        $result = '';
        $this->db->where(array('pkHistoryID'=>$historyID));
        $records = $this->db->get('reporthistory');
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
    /*
    Function : createHistoryTable()
    Desc :  This function is used to create reporthistory table.
    Inputs : None.
    Return : None.
    */
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
