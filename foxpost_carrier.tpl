
<script type="text/javascript" src="http://dev/presta1724/js/jquery/jquery-1.11.0.min.js" ></script>

<div id='fox_div' style='display:;'>
    <h3>Foxpost Csomagautomata kiválasztása</h3>
    Kérem válasszon egy csomagautomatát, ahol szeretné átvenni a csomagot!<br />
    <br />

    <select type='text' name='fox_select' id='fox_select'>
        <option value='-1'>Kérjük válasszon csomagautomatát!</option>
        {$options nofilter}
    </select>

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <br />
    <div>
        <div id='foxpost_info' style='width: 640px;margin-bottom:10px;margin-top:10px;'></div>
        <img id="foxpost_map" src="img/bg_loader.png" style="" />
    </div>
    <br>
    <div style="clear:both;"> </div>
</div>


<script type="text/javascript">

    function fox_myCallbackFunction(name, objMachine, openIndex) {
       fox_megjelenit();
    }

    function fox_megjelenit() {
        var selected = $('#fox_select').find('option:selected');
        $("#foxpost_map").attr('src', selected.data('map'));
        $("#foxpost_info").html(selected.data('info'));

        $.ajax({
                type: "GET",
                url: "{$modules_dir}foxpost/foxpost_ajax.php",
                data: "shop=" + selected.val(),
                dataType: "json",
                cache: false,
                success: function (json) {
                    //alert('success');
                    ;
                },
        });

        //alert(5);
    }

    $('document').ready(function () {
        //alert('ready');
        $('#fox_select').change(function () {
            fox_megjelenit();
        });

        if ($('input:radio[name=delivery_option\\[{$id_address_d}\\]]:checked').val() == '{$foxpost_ID}' + ',') {
            $('div#fox_div').show();
        }

        if ($("#fox_select").val() > '0') {
            fox_megjelenit();
        }
    });

</script>

{if $opc}
        <script type="text/javascript">
            function fox_finishcheck(event) {
                if ($('input:radio[name=delivery_option\\[{$id_address_d}\\]]:checked').val() == '{$foxpost_ID}' + ',') {
                    if ($('#fox_select').val() < '1') {
                        alert("Foxposttal történő szállítást választott,\nde nem jelölt meg átvételi pontot.\nKérjük válasszon egy átvételi pontot!");
                        event.preventDefault();
                    }
                }
            }

            $(window).load(function () {
                    $('#HOOK_PAYMENT .payment_module a').click(function (event) {
                        fox_finishcheck(event);
                    }
        );

                    $('#HOOK_PAYMENT').bind("DOMSubtreeModified", function () {
                            $('#HOOK_PAYMENT .payment_module a').click(function (event) {
                                fox_finishcheck(event);
                            }
                );
                        });
                    });

        </script>
    {else}
        <script type="text/javascript">
                    $('document').ready(function () {
                        $("#form").submit(function (event) {
                            if ($('input:radio[name=delivery_option\\[{$id_address_d}\\]]:checked').val() == '{$foxpost_ID}' + ',') {
                                if ($('#fox_select').val() < '1') {
                                    alert("Foxposttal történő szállítást választott,\nde nem jelölt meg átvételi pontot.\nKérjük válasszon egy átvételi pontot!");
                                    event.preventDefault();
                                }
                            }
                        });
                    });
        </script>
    {/if}
