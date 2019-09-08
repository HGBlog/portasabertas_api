<?php
	class Sale_Model extends CI_Model
	{

		public function __construct(){
			parent::__construct();
		}

		public function associate($data)
		{
			return $this->db->where('id_cartao', $data->cardNumber)->update('tb_cartoes', array(
				'descricao' => self::cleanString($data->name),
				'documento' => $data->document,
				'status' => 1
			));
		}
	

		public function checkBalance($cardId, $value)
		{

			$check = $this->db->where('id_cartao', $cardId)->get('tb_cartoes');

			if($check->num_rows() > 0){
				$result = $check->result();

				if($result[0]->creditos < $value){
					return array(
						'status' => false,
						'balance' => $result[0]->creditos,
						'msg' => 'Insufficient funds'
					);
				}else{
					return array(
						'status' => true,
						'balance' => $result[0]->creditos
					);
				}
				
			}
		}

		public function cancel($id)
		{
			$sale = $this->db->where('id_venda', $id)->get('tb_vendas');

			if($sale->num_rows() >0){
				$remove = $this->db->where('id_venda', $id)->delete('tb_vendas');

				if(!$remove == false){
					return self::increaseValue($sale->id_cartao,$sale->valor);
				}
			}else{
				return false;
			}
		}

		public function getSales($cardId){
			$sales = $this->db->query("SELECT 
			v.id_venda,
			b.nome,
			p.descricao,
			vd.login,
			v.valor
			from tb_vendas as v
			INNER JOIN tb_vendedores as vd ON v.id_vendedor = vd.id_vendedor
			INNER JOIN tb_produtos as p ON p.id_produtos = v.id_produto
			INNER JOIN tb_barracas as b ON p.barraca = b.id_barraca
			where id_cartao = ".$cardId);

			return ($sales->num_rows() > 0) ? $sales->result() : array();


		}

		public function getBalance($cardId){
			$card = $this->db->where('id_cartao', $cardId)->get('tb_cartoes');

			return ($card->num_rows() > 0) ? $card->result() : array();
		}

		public function increaseValue($cardId, $value){
			$card = $this->db->where('id_cartao', $cardId)->get('tb_cartoes')->result();
			return $this->db->where('id_cartao', $cardId)->update('tb_cartoes', array(
				'creditos' => ((float) $card[0]->creditos + $value)
			));
		}

		public function decreaseValue($cardId, $value){
			$card = $this->db->where('id_cartao', $cardId)->get('tb_cartoes')->result();
			return $this->db->where('id_cartao', $cardId)->update('tb_cartoes', array(
				'creditos' => ((float) $card[0]->creditos - $value)
			));
		}

		public function checkout($cardId, $value, $userId, $productId, $size)
		{
			return $this->db->insert('tb_vendas', array(
				'id_cartao'=> $cardId,
				'dthr' => date('Y-m-d H:i:s'),
				'valor' => $value,
				'id_vendedor' => $userId,
				'id_produto' => $productId,
				'qtd' => $size
			));
		}

		private function cleanString($text) {
		    $utf8 = array(
		        '/[áàâãªä]/u'   =>   'a',
		        '/[ÁÀÂÃÄ]/u'    =>   'A',
		        '/[ÍÌÎÏ]/u'     =>   'I',
		        '/[íìîï]/u'     =>   'i',
		        '/[éèêë]/u'     =>   'e',
		        '/[ÉÈÊË]/u'     =>   'E',
		        '/[óòôõºö]/u'   =>   'o',
		        '/[ÓÒÔÕÖ]/u'    =>   'O',
		        '/[úùûü]/u'     =>   'u',
		        '/[ÚÙÛÜ]/u'     =>   'U',
		        '/ç/'           =>   'c',
		        '/Ç/'           =>   'C',
		        '/ñ/'           =>   'n',
		        '/Ñ/'           =>   'N',
		        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
		        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
		        '/[“”«»„]/u'    =>   ' ', // Double quote
		        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
		    );
		    return preg_replace(array_keys($utf8), array_values($utf8), $text);
		}
	}

?>