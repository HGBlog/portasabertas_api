<?php
	class Sales extends CI_Controller{

		public function __construct(){
			parent::__construct();
			$this->load->model('sale_model', 'Sale');
			$this->load->model('user_model', 'User');
		}

		public function associate(){
			if(isset($_POST)){
				$data = json_decode(file_get_contents("php://input"));
				$response = [];

				if(!empty($data)){
					if(!isset($data->name) || !isset($data->document)){
						$response['status'] = false;
					}else{
						$response['status'] = $this->Sale->associate($data);
					}
					$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($response));
				}
			}
		}

		public function consult($cardId){
			$consult = $this->Sale->getBalance($cardId);

			if(!empty($consult)){
				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($consult[0]));
			}
		}

		public function consultSale($cardId){
			$consult = $this->Sale->getSales($cardId);

			if(!empty($consult)){
				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($consult));
			}
		}

		public function cancelSale($saleId){
			$sale = $this->Sale->cancel($saleId);

			if(!empty($sale)){
				$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($sale));
			}
		}

		public function sell(){
			if(isset($_POST)){
				$data = json_decode(file_get_contents("php://input"));
				$response = [];

				if(!empty($data)){
					$checkBalance = $this->Sale->checkBalance($data->cardId, $data->itens->total);

					if($checkBalance['status'] == true)
					{
						$headers = $this->input->request_headers();
						$user = $this->User->getUserToken($headers['Authorization']);

						if(!empty($user))
						{
							$sale = false;
							foreach($data->itens->items as $item)
							{
								$insert = $this->Sale->checkout($data->cardId,$item->subtotal,$user[0]->id_vendedor,$item->id_produtos, $item->qtd);

								if($insert == true){
									$sale = true;
								}else{
									break;
								}
							}

							if($sale == false){
								$response['status'] = false;
								
							}else{
								$response['status'] = true;
							}
							$response['balance'] = $this->Sale->getBalance($data->cardId);
						}else{
							$response['status'] = false;
						}
					}else{
						$response['status'] = false;
						$response['result'] = $checkBalance;
					}

					$this->output->set_content_type('application/json')->set_status_header(200)->set_output(json_encode($response));
				}
			}
		}

	}

?>