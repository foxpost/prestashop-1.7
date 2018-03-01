<?php

/*
################################
# Foxpost Parcels              #
# Copyright ToHr               #
# 2015.09.18                   #
# Foxpost parcels delivery core#
################################
*/

if (isset($_GET['shop'])) {

	//$fp = fopen('/var/www/ps1727.txt', 'a+');fwrite($fp, 'ajax.get');fclose($fp);
    require_once(realpath(dirname(__FILE__).'/../../config/config.inc.php'));
    require_once(realpath(dirname(__FILE__).'/../../init.php')); 
    global $cart;
   
    if((int)substr($_GET['shop'], 0, 5) > 0) {
        $context = Context::getContext();
        
        $json=file_get_contents("https://www.foxpost.hu/foxpost_terminals/foxpost_terminals.php");
        $js=json_decode($json);
   
        foreach ($js as $j) {

            if ($j->place_id == $_GET['shop']) {

                $context->cookie->__set("foxpost_automata_".$cart->id, $j->name." - (".$_GET['shop'].")");
            }else{
                //$fp = fopen('/var/www/ps1727.txt', 'a+');fwrite($fp, 'get shop nem egyenlo place_id, shop: '. $_GET['shop'].'----placeid:'.$j->place_id."\n");fclose($fp);
            }
            
        }
        
        

    }else{
        //$fp = fopen('/var/www/ps1727.txt', 'a+');fwrite($fp, 'get shop < 1 : '."\n");fclose($fp);
		//echo 89;
	}
    echo json_encode($js);
    
}




?>
