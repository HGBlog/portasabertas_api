<?php

	class Stand extends CI_Controller{

		public function __construct(){
			parent::__construct();

			validateToken();
			$this->load->model('stand_model', 'Stand');
			$this->load->model('product_model', 'Product');

		}

		public function getAll(){

			$response = $this->Stand->getAll();

			if(!empty($response)){
				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($response));
			}
		}

		public function getProductsByStand($standId){
			if(!empty($standId)){
				$products = $this->Product->getAll($standId);

				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode(
					array('products' => $products)
				));

			}else{	
				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode(array(
					'status' => false,
					'msg' => 'The standId is required!'
				)));
			}
		}

	}
?>