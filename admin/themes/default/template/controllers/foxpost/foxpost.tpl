
<script type="text/javascript">
function checkAll()
{
     var checkboxes = document.getElementsByTagName('input'), val = null;    
     for (var i = 0; i < checkboxes.length; i++)
     {
         if (checkboxes[i].type == 'checkbox')
         {
             if (val === null) val = checkboxes[i].checked;
             checkboxes[i].checked = val;
         }
     }
 }
</script>

<style type="text/css">
{literal}
.send_one_order {
    
    height: 30px;
    width: 200px;
 
}



.send_one_order_csv {
    
    height: 30px;
    width: 170px;
 
}
.foxpost_list td {
    
    padding: 10px;
    
}

.terminal_name {
    width: 250px;
}

.success_order {
    width: 200px;
}


{/literal}
</style>

<h2>Foxpost Rendelésekk</h2><br><br>


<form method="POST" name="CSV">
<form method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="mass" value="1">
   <button class="send_one_order" onclick="document.getElementById('adminForm').submit();" value="csv" name="csv">{l s='Kijelöltek Exportálása CSV-be' mod='Foxpost'}</button>
        <button class="send_one_order" style="width: 350px;" onclick="document.getElementById('adminForm').submit();" name="csv_cod" value="csv_cod">{l s='Kijelöltek Exportálása CSV-be Utánvét Összege nélkül' mod='Foxpost'}</button>
    {$foxpost_orders.select}
    <table class="foxpost_list" cellspacing="0" cellpadding="20" width="1000" style="margin-left: 10px; margin-top: 10px;">
        <tr>
  
            <td width="50"><input type="checkbox" name="toggle" value="" onclick="checkAll()"/></td>
            <td width="120"><b>Azonosító</b></td>
            <td class="terminal_name"><b>Cél terminál</b></td>
            <td width="200"><b>Léterhozás Dátuma</b></td>
            <td width="160"><b>Fizetési Mód</b></td>
            <td width="100"><b>Műveletek</b></td>
        </tr>
        
        {$foxpost_orders.delay}

     </table>
       
