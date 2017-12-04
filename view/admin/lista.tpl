	<div class="table-responsive clearfix">
            
{if isset($webox_error_msg)}<span style="color:red;font-weight:bold;">{$webox_error_msg}<br /><br /></span>{/if}

<a href="{$link->getAdminLink('AdminWebox')}&method=allUpdateStatusz">Lista elemeinek csoportos státuszfrissítése</a> | 
<a href="{$link->getAdminLink('AdminWebox')}&method=logView" target="_blank">Naplófájl megtekintése</a> | 
<a href="{$link->getAdminLink('AdminWebox')}&method=documentation" target="_blank">Webox dokumentáció</a><br /><br />

<table class="table order" style="width:100%;">
    <thead>
    <form action="{$link->getAdminLink('AdminWebox')}&method=search" method="post">
    <tr>
        <th>Rendelés</th>
        <th>Cél automata</th>
        <th>Csomag azonosító</th>
        <th>Státusz</th>
        <th>Időpont</th>
        <th colspan="4" style="text-align:center;">Műveletek</th>
    </tr>
    <tr>
        <th><input class="filter" type="text" value="{$search_params_1|default:""}" name="order_id" /></th>
        <th><input class="filter" type="text" value="{$search_params_2|default:""}" name="target_machine_id" /></th>
        <th><input class="filter" type="text" value="{$search_params_3|default:""}" name="parcel_id" /></th>
        <th><select name="status">
                <option value=""></option>
                <option value="new" {if isset($search_params_4) && $search_params_4 == 'new'}selected{/if}>új (new)</option>
                <option value="created" {if isset($search_params_4) && $search_params_4 == 'created'}selected{/if}>létrehozott (created)</option>
                <option value="cancelled" {if isset($search_params_4) && $search_params_4 == 'cancelled'}selected{/if}>törölt (cancelled)</option>
                <option value="prepared" {if isset($search_params_4) && $search_params_4 == 'prepared'}selected{/if}>fizetett (prepared)</option>
                <option value="delivered" {if isset($search_params_4) && $search_params_4 == 'delivered'}selected{/if}>szállított (delivered)</option>
                <option value="sent" {if isset($search_params_4) && $search_params_4 == 'sent'}selected{/if}>elküldött (sent)</option>
            </select>
        </th>
        <th><input class="filter" type="text" value="{$search_params_5|default:""}" name="datetime" /></th>
        <th colspan="4"><input type="submit" value="Keresés" class="btn btn-default" /></th>
    </tr>
    </form>
    </thead>
    
    <tbody>
    {foreach from=$rows key=kulcs item=elem}
    <tr>
        <td>#{$elem.id_order}</td>
        <td>{$elem.automata}</td>
        <td>{$elem.id_parcel}</td>
        <td>{$elem.statusz|replace:'new':'új'|replace:'created':'létrehozott'|replace:'cancelled':'törölt'|replace:'prepared':'fizetett'|replace:'delivered':'szállított'|replace:'sent':'elküldött'}</td>
        <td>{$elem.idopont}</td>
        
        {if $elem.id_parcel && $elem.statusz != 'cancelled'}
            <!--<td id="statusz_{$elem.id_order}"><a class="btn btn-default" href="javascript:void(0);" onclick="getstatusz({$elem.id_parcel},{$elem.id_order});">Státuszlekérdezés</a></td>-->
            <td><a href="{$link->getAdminLink('AdminWebox')}&method=updateStatusz&id_parcel={$elem.id_parcel}">Státuszlekérdezés</a></td>
            {if $elem.statusz == 'created'}<td><a href="{$link->getAdminLink('AdminWebox')}&method=payParcel&id_parcel={$elem.id_parcel}">Fizetés</a></td>{/if}
            {if $elem.statusz != 'created'}<td><a href="{$link->getAdminLink('AdminWebox')}&method=getSticker&id_parcel={$elem.id_parcel}">Fuvarlevél</a></td>{/if}
            {if $elem.statusz == 'created'}<td><a href="{$link->getAdminLink('AdminWebox')}&method=cancelParcel&id_parcel={$elem.id_parcel}">Csomagtörlés</a></td>{/if}
        
        {else}
            <td colspan="4">
                <form method="post" action="{$link->getAdminLink('AdminWebox')}&method=createParcel&id_order={$elem.id_order}" onsubmit="return isValidTel({$elem.id_order});">
                    <input type="hidden" name="id_order" value="{$elem.id_order}" />
                <table>
                    <tr><td>Feladó automata</td><td>
                            <select type='text' name='sender_machine_id'>
                                <option value='HUWBX000'>Futár jöjjön érte.</option>
                                {$options}
                            </select> Alapértelmezett: {$elem.rendeles.sender_machine_id|replace:'HUWBX000':'Futár jöjjön érte.'}</td></tr>
                    <tr><td>Címzett automata</td><td>
                        <select type='text' name='target_machine_id'>
                                <option value='HUWBX000'>Futár jöjjön érte.</option>
                                {$elem.rendeles.options2}
                            </select> Rendelésnél kiválasztott: {$elem.rendeles.target_machine_id}
                        </td></tr>
                    <tr><td>Címzett email címe</td><td><input type="text" name="receiver_email" value="{$elem.rendeles.receiver_email}" /></td></tr>
                    <tr><td>Címzett telefonszáma</td><td><input type="text" id="tel_{$elem.id_order}" onkeyup="this.value=this.value.replace(/[^\d]/,'');" maxlength="9" name="receiver_phone" value="{$elem.rendeles.receiver_phone}" /> Rendelésnél megadott: {$elem.rendeles.receiver_phone}</td></tr>
                    <tr><td>Megjegyzés</td><td><input type="text" name="customer_reference" value="{$elem.rendeles.customer_reference}" /></td></tr>
                    <tr><td>Utánvét összege (Ft)</td><td><input type="text" name="cod_amount" value="{$elem.rendeles.cod_amount|intval}" /> Végösszeg: {$elem.rendeles.osszesen} Ft, fizetve: {$elem.rendeles.fizetve} Ft</td></tr>
                    <tr><td colspan="2"><input type="submit" value="Csomagkészítés" /></td></tr>
                </table>
                
                </form>
            </td>
        {/if}
    </tr>
    
{/foreach}
    </tbody>
</table>

<script type="text/javascript">
    var weboxUrl = "{$link->getAdminLink('AdminWebox')}";
{literal}    
    function getstatusz(id_parcel, id_order){
        $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: weboxUrl + '&rand=' + new Date().getTime(),
                async: false,
                cache: false,
                dataType : "json",
                data: '&method=updateStatusz&id_parcel='+id_parcel,
                success: function(jsonData){
                    alert(jsonData);
                    $('statusz_'+id_order).html(jsonData);
                }
        });
    }
    
    function isValidTel(order_id){
        //alert($("#tel_"+order_id).val());
        if($("#tel_"+order_id).val().length != 9){
            alert('Hibás telefonszám a #' + order_id + ' számú rendelésnél (9 számjegy, 20/30/70 előhívószámmal kezdve)!');
            return false;
        }
        if($("#tel_"+order_id).val().replace(/[^\d]/,'') != $("#tel_"+order_id).val()){
            alert('A #' + order_id + ' számú rendelés telefonszáma nem csak számokat tartalmaz!');
            return false;
        }
        
        if($("#tel_"+order_id).val().substr(0,2) != '20' && $("#tel_"+order_id).val().substr(0,2) != '30' && $("#tel_"+order_id).val().substr(0,2) != 70){
            alert('A #' + order_id + ' számú rendelés telefonszáma nem 20/30/70 előhívószámmal kezdődik!');
            return false;
        }
        
        return true;
    }
    
{/literal}
</script>
        </div>