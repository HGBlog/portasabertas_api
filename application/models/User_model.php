<?php

	class User_Model extends CI_Model{

		public function __construct(){
			parent::__construct();
			$this->load->model('stand_model', 'Stand');
		}

		public function auth($user, $password){
			$user = $this->db->where(array(
				'login' => $user,
				'senha' => md5($password)
			))->get('tb_vendedores');

			if($user->num_rows() > 0){
				$token = random_string('alnum', 255);

				$result = $user->result();

				$persistToken = $this->db->insert('tb_vendedores_token',array(
					'token' => $token,
					'id_vendedor' => $result[0]->id_vendedor,
					'expires' => date('Y-m-d H:i:s', strtotime("+1 day"))
				));


				$stand = $this->Stand->getStandByUser($result[0]->id_vendedor);

				return array(
					'token' => $token,
					'profile' => $result[0]->nivel,
					'stand' => (!empty($stand)) ? $stand : array()
				);
			}

			return false;
		}

		public function getUserToken($token){

			$user = $this->db->query("SELECT * FROM `tb_vendedores_token` as tk JOIN `tb_vendedores` as v ON v.id_vendedor = tk.id_vendedor WHERE `token`= '${token}'"); 
			
			return ($user->num_rows() > 0) ? $user->result() : array();
		}
	}
?>