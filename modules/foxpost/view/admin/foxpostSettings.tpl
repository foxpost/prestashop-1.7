{if isset($webox_error_msg)}<span style="color:red;font-weight:bold;">{$webox_error_msg}<br /><br /></span>{/if}

<form method="post" action="{*$link->getAdminLink('AdminFoxpost')*}{*&method=foxpostSettings*}{$formaction}">

    <table class="table" style="width:100%;">
         <tr>
            <th>Api url</th>
            <td><input type="text" name="foxpost_api_url" value="{$foxpost_api_url}" style="width:500px;" /></td>
        </tr>
        <tr>
            <th>Api felhasználónév {if $foxpost_api_username}{else} (Jelenleg NINCS megadva felhasználónév!){/if}</th>
            <td><input type="text" name="foxpost_api_username" value="{$foxpost_api_username}" style="width:500px;" /></td>
        </tr>
        <tr>
            <th>Api jelszó {if $foxpost_api_password}{else} (Jelenleg NINCS megadva az api jelszó){/if}</th>
            <td><input type="text" name="foxpost_api_password" value="{$foxpost_api_password}" style="width:500px;" /></td>
        </tr>
        <tr>
            <th>Szállítási Költség</th>
            <td><input type="text" name="foxpost_shipping_fee" value="{$foxpost_shipping_fee}" style="width:500px;" /></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="SubmitFoxpostConfig" value="Beállítások mentése" />
                <!--<input type="button" onclick="self.location = '{$link->getAdminLink('AdminFoxpost')}';" value="Vissza a listához" />-->
            </td>
        </tr>
    </table>

</form>