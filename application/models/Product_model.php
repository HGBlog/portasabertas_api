<?php

	class Product_Model extends CI_Model{

		public function __construct(){
			parent::__construct();
			
		}


		public function getAll($standId){
			$products = $this->db->where('barraca', $standId)->get('tb_produtos');

			return ($products->num_rows() > 0) ? $products->result() : array();
		}

	}
?>