<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class User extends CI_Controller{

		private $validateToken;

		public function __construct(){
			parent::__construct();
			$this->load->model('user_model', 'User');

			// validateToken();
		}

		public function index(){
			
		}

		public function login(){
			if(isset($_POST)){
				$data = json_decode(file_get_contents("php://input"));

				if(!empty($data)){
					$response = $this->User->auth($data->user, $data->password);
					$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode(array(
						'status' => true,
						'data' => $response
					)));
				}
			}else{
				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode(array(
					'status' => false,
					'msg' => 'for this is endpoint only post'
				)));
			}
		}

	
	}

?>