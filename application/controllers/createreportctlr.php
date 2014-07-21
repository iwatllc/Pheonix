<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'third_party/excel/Classes/PHPExcel.php';
class CreateReportCtlr extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('createreportmodel');
		$this->load->helper('form');
		$this->createreportmodel->createHistoryTable();
	}

	//Landing Page
	public function index()
	{
		$data['reportHistory'] = $this->createreportmodel->getReportHistory();
		$data['database_reports_tables'] = $this->config->item('database_reports_tables');
		$this->load->view('createreport',$data);
		$this->session->unset_userdata('tmp_query');
	}

	//Create custom query
	public function selecttable($historyID = 0)
	{
		$data = array();
		$flag = 0;
		$data['database_reports_tables'] = $this->config->item('database_reports_tables');
		$this->session->unset_userdata('query');
		if($this->input->server('REQUEST_METHOD') === 'POST'){
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			$this->form_validation->set_rules('to_tables', 'to_tables', 'required');
			if ($this->form_validation->run() == FALSE){
				$data['form_error'] = 'Please set mandatory fields.';
			}else{
				$result = $this->createreportmodel->getCustomQueryResult();
				$data['results'] = $result;
				$key = time();
				$data['session_query_id'] = $key;
				$this->session->set_userdata('query',array($key=>$this->db->last_query()));
				if($this->input->post('save_report') == 'Yes' && $this->session->userdata('tmp_query') != $this->db->last_query())
				{
					$arrData = array();
					$this->session->set_userdata('tmp_query',$this->db->last_query());
					$arrData['ReportName'] = $this->input->post('report_name');
					$arrData['SqlQuery'] = $this->db->last_query();
					if($this->input->post('reportID') != ''){
						$this->createreportmodel->editInToHistory($arrData,$this->input->post('reportID'));	
						$this->createreportmodel->saveAllReportsFields($this->input->post('reportID'),"Edit");
					}else{
						$this->createreportmodel->insertInToHistory($arrData);	
						$insert_id = $this->db->insert_id();
						$this->createreportmodel->saveAllReportsFields($insert_id,"Add");
					}
				}
				if(!empty($data))
				{
					$flag = 1; 
					$this->load->view('reportresult',$data);
				}
			}
		}else if($historyID != 0){
			$this->session->unset_userdata('tmp_query');
			$query = $this->createreportmodel->getHistoryQueryById($historyID);
			$data['results'] = $this->createreportmodel->getQueryResult($query);
			$key = time();
			$data['session_query_id'] = $key;
			$this->session->set_userdata('query',array($key=>$this->db->last_query()));
			if(!empty($data))
			{
				$flag = 1; 
				$this->load->view('reportresult',$data);
			}
		}
		if(empty($flag))
		{
			$this->load->view('selecttable',$data);
		}
	}

	function editreport($reportID)
	{
		$data = $this->createreportmodel->getAllFormsFieldsOfHistoryReport($reportID);
		$this->load->view('selecttable',$data);
	}

	//Export to excel Actions
	function getExcelExport($query_session_id)
	{
		$query = $this->session->userdata('query');
		if(!empty($query[$query_session_id]))
		{
			$query = $query[$query_session_id];
			$result = $this->createreportmodel->getQueryResult($query);
			$data['results'] = $result;
			$this->load->view('export_to_excel',$data);
		}else{
			return false;		
		}
	}

	//Ajax Called Actions
	function getTableColumn()
	{
			echo $this->createreportmodel->getTableColumn($this->input->post('tableName'));
	}

	//Ajax delete history.
	function removehistoryrecord()
	{
		$this->db->delete(TABLE_REPORT_HISTORY,array('pkHistoryID'=>$this->input->post('recordID')));
		$this->db->delete(TABLE_HISTORY_REPORTS_FIELDS,array('fkReportHistoryID'=>$this->input->post('recordID')));
		redirect('/');
	}
}