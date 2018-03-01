<?php

/*
################################
# Foxpost Parcels              #
# Copyright ToHr               #
# 2015.09.18                   #
# Foxpost parcels delivery core#
################################
*/

class AdminFoxpostController extends AdminController
{
    public function __construct()
    {
         parent::__construct();
    }

    public function initContent()
    {

        /* LÉTREHOZUNK EGY TÁBLÁT A CLEVERES RETURNOKNAK */
            $db=DB::getInstance();
             $db->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."_foxpost (id_cart int(11) DEFAULT NULL, barcode varchar(255) DEFAULT NULL, order_details varchar(255) DEFAULT NULL)");


        parent::initContent();
        $this->setTemplate('/foxpost.tpl');

        self::displayContent();


        if (is_numeric(@$_POST['order_to_send'])) {
            self::processOneOrder();
        }



        if (@$_POST['send_all']=="send_all") {
            self::processOneOrderMass();
        }

        if (@$_POST['csv']=="csv") {
            self::processMassCsv();
        }

        if (@$_POST['csv_cod']=="csv_cod") {
            self::processMassCsvCod();
        }


    }



    public function displayContent() {

      global $smarty;

         $db=DB::getInstance();
               $tablerow="";

               $vr=0;


               // Payment options
               $sel=$this->l('Szűrés:').'<select name="payment" class="send_one_order" id="payment"><option value="#" select="selected">Kérem válasszon fizetési módot</option>';

                 $pa=$db->executeS("SELECT DISTINCT payment FROM "._DB_PREFIX_."orders GROUP BY payment");
                 foreach ($pa as $pass) {

                     $sel.='<option value="1" onclick="javascript:window.location=(\'index.php?controller=AdminFoxpost&token='.$_GET['token'].'&payment='.$pass['payment'].'\');">'.$pass['payment'].'</option>';

                 }
                 $sel.="</select>";


             $car = $db->executeS("SELECT name, id_carrier, external_module_name FROM "._DB_PREFIX_."carrier WHERE name LIKE '%Foxpost%' and deleted=0");

                    foreach ($car as $carr) {




                     if (empty($_GET['payment'])) {
                           $ord_date = $db->executeS("SELECT date_upd, payment, id_customer, id_address_delivery, id_cart FROM "._DB_PREFIX_."orders WHERE id_carrier='".$carr['id_carrier']."'");
                       } else {
                          $ord_date = $db->executeS("SELECT date_upd, payment, id_customer, id_address_delivery, id_cart FROM "._DB_PREFIX_."orders WHERE id_carrier='".$carr['id_carrier']."' AND payment LIKE '".$_GET['payment']."'");
                       }
                         if (count($ord_date) != 0) {
                            foreach ($ord_date as $order_param) {

                             $currdate=@$order_param['date_upd'];
                             $currpay=@$order_param['payment'];
                             $vasarloid=@$order_param['id_customer'];
                             $curr_deliver = $order_param['id_address_delivery'];
                             $curr_cart=$order_param['id_cart'];



                            $dest=$db->executeS("SELECT tracking_number FROM "._DB_PREFIX_."order_carrier WHERE id_order='".$curr_cart."'");

                            $current_carrier=@$dest[0]['tracking_number'];

                             if (!empty($currdate)) {

                             if ($vr==0) {
                                 $vr++;
                                 $class='bgcolor="#f2f5f5"';
                             } else {
                                 $vr=0;
                                 $class='bgcolor="#ededed"';
                             }

                             // PREAPARE DATA FOR APT!
                             $customer = $db->executeS("SELECT email, firstname, lastname FROM "._DB_PREFIX_."customer WHERE id_customer='".$vasarloid."'");
                             $customer_phone = $db->executeS("SELECT phone FROM "._DB_PREFIX_."address WHERE id_address='".@$curr_deliver."'");

                             $names=$customer[0]['firstname'].' '.$customer[0]['lastname'];


                             $emails=@$customer[0]['email'];
                             $phones=@$customer_phone[0]['phone'];

                             $jsonOrderCreateTomb = array (

                                "place_id"=>$carr['external_module_name'],
                                "name" => $names,
                                "phone" => $phones,
                                "email" => $emails,

                            );
                            $jsonOrderCreateTomb=json_encode($jsonOrderCreateTomb);
                            $q2 =$db->executeS("SELECT barcode FROM "._DB_PREFIX_."order_foxpost WHERE id_cart='".@$curr_cart."'");

                            $cucc=(@$q2[0]);
                            print_r ($cucc['barcode']);
                            $tablerow.="<tr ".$class.">";



                             if (($cucc['barcode']==0) OR (empty($q2))) {
                              $tablerow.='<td><input value="1" type="checkbox" name="fox_'.@$curr_cart.'" id="fox_'.@$curr_cart.'"></td>';
                             } else {
                              $tablerow.="<td>&nbsp;</td>";
                             }
                             $tablerow.="<td>".@$order_param['id_cart']."</td>";
                             $tablerow.='<td> FOXPOST -'.@$current_carrier.'</td>';
                             $tablerow.="<td>".@$currdate."</td>";
                             $tablerow.="<td>".@$currpay."";
                             $tablerow.='<input type="hidden" name="mass'.@$curr_cart.'" value="1">'
                                     . '<input type="hidden" name="mass_order'.@$curr_cart.'" value="'.@$curr_cart.'">'
                                     . '<textarea style="display: none;" name="mass_order_data'.@$curr_cart.'">'.@$jsonOrderCreateTomb.'</textarea></td>';

                             if ($cucc['barcode']==1)  {
                             $tablerow.='<td></form><form method="POST" name="send_order_'.@$curr_cart.'" id="send_order_'.@$curr_cart.'">'
                                     . '<input type="hidden" name="order_to send" value="1">'
                                     . '<input type="hidden" name="order_to_send_barcode" value="'.@$curr_cart.'">'
                                     . '<button class="send_one_order" onclick="document.getElementById(\'send_order_'.@$curr_cart.'\').submit();" name="send_order_'.@$curr_cart.'">'.$this->l('AKTIVÁLÁS').'</button>'
                                     . '<textarea style="display: none;"  name="send_order_details">'.@$jsonOrderCreateTomb.'</textarea></form>'
                                     . '</td>';
                             } else {

                             }

                             $tablerow.='</tr>';
                           }
                         }
                       }
                    }


                $arr = array(

			'delay' => $tablerow,
                        'select' => $sel,
		);


		$smarty->assign(array(
			'foxpost_orders' => $arr,

		));

     }



     public function processOneOrder() {

            // CSATLAKOZÁSI PARAMÉTEREK LEKÉRDEZÉSE
          $db=DB::getInstance();
            $q2 =$db->Execute("UPDATE "._DB_PREFIX_."order_foxpost SET barcode='0' WHERE id_cart='".$_POST['order_to_send_barcode']."'");

            }


       public function processMassCsv() {
        $db=DB::getInstance();
        $car = $db->executeS("SELECT a.id_cart, a.id_customer, b.id_order, a.id_carrier, b.total_paid, b.id_address_delivery FROM "._DB_PREFIX_."cart a "
                . "LEFT JOIN "._DB_PREFIX_."orders b ON a.id_cart=b.id_cart");
            $filename=date("Ymd_hms");
                $ourFileName ="../modules/foxpost/csv/$filename.csv";

                $whatPutIn=array( 'Vásárló neve', 'Telefonszáma', 'Email címe', 'Cél Temrinál', 'Utánvét összege', 'Súly', 'Termékek');
                $whatPutIn = array_map("utf8_decode", $whatPutIn);
                file_put_contents($ourFileName, $whatPutIn);
                $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
                fputcsv($ourFileHandle, $whatPutIn, ';');



       foreach ($car as $orders) {
          $currCid=@$orders['id_cart'];

            if (@$_POST["fox_".$currCid] == 1) {

                $products = $db->executeS("SELECT product_name, product_weight FROM "._DB_PREFIX_."order_detail WHERE id_order='".$orders['id_order']."'");

               $mennyirow=count($products);
               $vr=1;
               $weight_now=0;
               $itemlist="";
               foreach( $products as $resu) {

               $actItem=$resu['product_name'];
               $weight_now=$weight_now+$resu['product_weight'];

               if ($vr==$mennyirow) {
                $itemlist.=$actItem.' ';
               } else {
                $itemlist.=$actItem.' \ ';
               }
                 $vr++;
               }

             $itemlist = preg_replace('/(^|;)"([^"]+)";/','$1$2;',$itemlist);



                $customer = $db->executeS("SELECT email, firstname, lastname FROM "._DB_PREFIX_."customer WHERE id_customer='".$orders['id_customer']."'");

                             $customer_phone = $db->executeS("SELECT phone FROM "._DB_PREFIX_."address WHERE id_customer='".@$orders['id_customer']."' AND id_address='".$orders['id_address_delivery']."'");

                             $names=$customer[0]['firstname'].' '.$customer[0]['lastname'];


                             $emails=@$customer[0]['email'];
                             $phones=@$customer_phone[0]['phone'];

                             $curr_cart=$orders['id_cart'];
                             $dest=$db->executeS("SELECT tracking_number FROM "._DB_PREFIX_."order_carrier WHERE id_order='".$curr_cart."'");
                             $current_carrier=@$dest[0]['tracking_number'];

                             $current_carrier=explode(' - ', $current_carrier);
                             $current_carrier=str_replace('(', "", $current_carrier[1]);
                             $current_carrier=str_replace(')', "", $current_carrier);

                             $total=round($orders['total_paid'], 0);


                    $list = array($names, $phones, $emails, $current_carrier, $total, $weight_now, $itemlist);

                    $array = array_map("utf8_decode", $list);

                     fputcsv($ourFileHandle, $array, ';');

                $car=$db->executeS("SELECT id_cart FROM "._DB_PREFIX_."order_foxpost WHERE id_cart='".$orders['id_order']."'");

                    if (!empty($car)) {
                      $q2 =$db->Execute("UPDATE "._DB_PREFIX_."order_foxpost SET barcode='1' WHERE id_cart='".$orders['id_order']."'");
                    } else {
                        $q2=$db->Execute("INSERT INTO "._DB_PREFIX_."order_foxpost VALUES ('".$orders['id_order']."', '1', '')");
                    }

                }





         }

         fclose($ourFileHandle);

                ?><script type="text/javascript">
                 var url = '<?php echo $ourFileName; ?>';
                  window.open(url, '_blank');
                  window.location.href=window.location.href
                 </script><?php
       }


       public function processMassCsvCod() {
        $db=DB::getInstance();
        $car = $db->executeS("SELECT a.id_cart, a.id_customer, b.id_order, a.id_carrier, b.total_paid, b.id_address_delivery FROM "._DB_PREFIX_."cart a "
                . "LEFT JOIN "._DB_PREFIX_."orders b ON a.id_cart=b.id_cart");
            $filename=date("Ymd_hms");
                $ourFileName ="../modules/foxpost/csv/$filename.csv";

                $whatPutIn=array( 'Vásárló neve', 'Telefonszáma', 'Email címe', 'Cél Temrinál', 'Utánvét összege', 'Súly', 'Termékek');
                $whatPutIn = array_map("utf8_decode", $whatPutIn);
                file_put_contents($ourFileName, $whatPutIn);
                $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
                fputcsv($ourFileHandle, $whatPutIn, ';');



       foreach ($car as $orders) {
          $currCid=@$orders['id_cart'];

            if (@$_POST["fox_".$currCid] == 1) {

                $products = $db->executeS("SELECT product_name, product_weight FROM "._DB_PREFIX_."order_detail WHERE id_order='".$orders['id_order']."'");

               $mennyirow=count($products);
               $vr=1;
               $weight_now=0;
               $itemlist="";
               foreach( $products as $resu) {

               $actItem=$resu['product_name'];
               $weight_now=$weight_now+$resu['product_weight'];

               if ($vr==$mennyirow) {
                $itemlist.=$actItem.' ';
               } else {
                $itemlist.=$actItem.' \ ';
               }
                 $vr++;
               }

             $itemlist = preg_replace('/(^|;)"([^"]+)";/','$1$2;',$itemlist);



             $customer = $db->executeS("SELECT email, firstname, lastname FROM "._DB_PREFIX_."customer WHERE id_customer='".$orders['id_customer']."'");

                          $customer_phone = $db->executeS("SELECT phone FROM "._DB_PREFIX_."address WHERE id_customer='".@$orders['id_customer']."' AND id_address='".$orders['id_address_delivery']."'");

                          $names=$customer[0]['firstname'].' '.$customer[0]['lastname'];


                          $emails=@$customer[0]['email'];
                          $phones=@$customer_phone[0]['phone'];

                          $curr_cart=$orders['id_cart'];
                          $dest=$db->executeS("SELECT tracking_number FROM "._DB_PREFIX_."order_carrier WHERE id_order='".$curr_cart."'");
                          $current_carrier=@$dest[0]['tracking_number'];

                          $current_carrier=explode(' - ', $current_carrier);
                          $current_carrier=str_replace('(', "", $current_carrier[1]);
                          $current_carrier=str_replace(')', "", $current_carrier);

                          $total=round($orders['total_paid'], 0);


                 $list = array($names, $phones, $emails, $current_carrier, '', $weight_now, $itemlist);

                    $array = array_map("utf8_decode", $list);

                     fputcsv($ourFileHandle, $array, ';');

                     $car=$db->executeS("SELECT id_cart FROM "._DB_PREFIX_."order_foxpost WHERE id_cart='".$orders['id_order']."'");

                    if (!empty($car)) {
                      $q2 =$db->Execute("UPDATE "._DB_PREFIX_."order_foxpost SET barcode='1' WHERE id_cart='".$orders['id_order']."'");
                    } else {
                        $q2=$db->Execute("INSERT INTO "._DB_PREFIX_."order_foxpost VALUES ('".$orders['id_order']."', '1', '')");

                    }

                }





         }

         fclose($ourFileHandle);

                ?><script type="text/javascript">
                 var url = '<?php echo $ourFileName; ?>';
                  window.open(url, '_blank');
                  window.location.href=window.location.href
                 </script><?php
       }


    }
?>
