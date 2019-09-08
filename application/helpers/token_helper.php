<?php
	
	if(!function_exists('validateToken')){
		function validateToken(){
			$ci=& get_instance();

			$response = array();

            if($ci->input->request_headers()){
    			$headers = $ci->input->request_headers();

    			$validate = $ci->db->where('token',$headers['Authorization'])->get('tb_vendedores_token');


    			if($validate->num_rows() > 0){
    				$result = $validate->result();

    				if(!empty($result)){
    					$isValidToken =  timeDifference(date('Y-m-d H:i:s'), 'Y-m-d H:i:s',$result[0]->expires, 'Y-m-d H:i:s', true, 'day',true);

    					if($isValidToken > 0){
    						$response['code'] = 200;
    						$response['mensage'] =  'Token válido';
    					}else{
    						$response['code'] = 401;
    						$response['mensage'] =  'Token inválido';
    					}

    				}

    			}else{
    				$response['code'] = 401;
    				$response['mensage'] =  'Token inválido';
    			}

    			if($response['code'] > 200){
    				// return $ci->output
    				// 		->set_content_type('application/json')
    				// 		->set_status_header($response['code'])
    				// 		->set_output(json_encode(array(
    				// 			'mensage' => $response['mensage']
    				// 		)));

			     }
            }
		}
	}

	function timeDifference($date1_pm_checked, $date1_format,$date2, $date2_format, $plus_minus=false, $return='all', $parseInt=false)
    {
        $strtotime1=strtotime($date1_pm_checked);
        $strtotime2=strtotime($date2);
        $date1 = new DateTime(date($date1_format, $strtotime1));
        $date2 = new DateTime(date($date2_format, $strtotime2));
        $interval=$date1->diff($date2);

        $plus_minus=(empty($plus_minus)) ? '' : ( ($strtotime1 > $strtotime2) ? '+' : '-'); # +/-/no_sign before value 

        switch($return)
        {
            case 'y';
            case 'year';
            case 'years';
                $elapsed = $interval->format($plus_minus.'%y');
                break;

            case 'm';
            case 'month';
            case 'months';
                $elapsed = $interval->format($plus_minus.'%m');
                break;

            case 'a';
            case 'day';
            case 'days';
                $elapsed = $interval->format($plus_minus.'%a');
                break;

            case 'd';
                $elapsed = $interval->format($plus_minus.'%d');
                break;

            case 'h';       
            case 'hour';        
            case 'hours';       
                $elapsed = $interval->format($plus_minus.'%h');
                break;

            case 'i';
            case 'minute';
            case 'minutes';
                $elapsed = $interval->format($plus_minus.'%i');
                break;

            case 's';
            case 'second';
            case 'seconds';
                $elapsed = $interval->format($plus_minus.'%s');
                break;

            case 'all':
                $parseInt=false;
                $elapsed = $plus_minus.$interval->format('%y years %m months %d days %h hours %i minutes %s seconds');
                break;

            default:
                $parseInt=false;
                $elapsed = $plus_minus.$interval->format($return);
        }

        if($parseInt)
            return (int) $elapsed;
        else
            return $elapsed;

    }
	
?>