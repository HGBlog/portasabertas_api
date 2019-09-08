<?php

	class Stand_Model extends CI_Model{

		public function __construct(){
			parent::__construct();
		}


		public function getAll(){
			//$stands = $this->db->get('tb_barracas');
			$stands = $this->db->order_by('nome','ASC')->get('tb_barracas');
						
			return ($stands->num_rows() > 0) ? $stands->result() : array();
		}

		public function getProducts($standId)
		{
			$products = $this->db->where('barraca', $standId);
		}


		public function getStandByUser($userId)
		{
			$query = 'SELECT b.id_barraca, b.nome FROM tb_vendedores AS v 
				INNER JOIN tb_vendedor_barraca AS vb ON v.id_vendedor = vb.id_vendedor
				INNER JOIN tb_barracas AS b ON b.id_barraca = vb.id_barraca
				WHERE v.id_vendedor = '.$userId.' ORDER BY b.nome ASC';
				
			$stands = $this->db->query($query);

			return ($stands->num_rows() > 0) ? $stands->result() : array();
		}
	}
?>