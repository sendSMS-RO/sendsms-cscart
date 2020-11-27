<?php

use Tygh\Registry;
use Tygh\Settings;
require_once (Registry::get('config.dir.addons') ."sendsms_cscart/API/sendsms.php");

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

function fn_add_limit_sendsms_cscart_lenghtlimitation()
{
    return '<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        document.getElementById("addon_option_sendsms_cscart_message-expeditor").setAttribute("maxlength", 11);
    });
    </script>';
}

function fn_add_autocomplete_variables_sendsms_cscart_messageinformation()
{

    return 'Avaible values: ' . '<a onClick="placeInFocusedTextarea(\'{order_id}\')">' . '{order_id}' . '</a>' . ', ' . '<a onClick="placeInFocusedTextarea(\'{total}\')">' . '{total}' . '</a>' . ', ' . '<a onClick="placeInFocusedTextarea(\'{date}\')">' . '{date}' . '</a>' . ', ' . '<a onClick="placeInFocusedTextarea(\'{firstname}\')">' . '{firstname} ' . '</a>' . '
    <script>
    var focused;
    document.addEventListener("DOMContentLoaded", (event) => {
        var inputs = document.getElementsByTagName("textarea");
        for (var i=0, input; i<inputs.length && (input = inputs[i]); i++) {
            input.addEventListener("focus", function(){
                focused = this;
            });
        }
    });
    function placeInFocusedTextarea(textToAdd)
    {
        if(focused)
        {
            focused.value += textToAdd;
            lenghtCounter(focused, focused.previousSibling);
        }
    }

    </script>';
}

function fn_add_get_details_sendsms_cscart_giveaccountinfo()
{
    $api = new SendsmsApi();
    if (Registry::get('addons.sendsms_cscart.login-name') !== "")
        $api->setUsername(Registry::get('addons.sendsms_cscart.login-name'));
    else {
        return "Please add your sendsms.ro username";
    }
    if (Registry::get('addons.sendsms_cscart.login-pass') !== "")
        $api->setPassword(Registry::get('addons.sendsms_cscart.login-pass'));
    else {
        return  "Please add your sendsms.ro password/apikey";
    }
    $result = $api->user_get_balance();
    if($result['status'] >= 0)
    {
        return "Your current credit is " . $result['details'] . " euro.";
    }
    return "Unable to connect to API";
}

function fn_add_word_counter_sendsms_cscart_wordcounter()
{
    return
        '   
        <script>
            var focused;
            document.addEventListener("DOMContentLoaded", (event) => {
                var textareas = document.getElementsByTagName("textarea");
                for (var i=0, textarea; i<textareas.length && (textarea = textareas[i]); i++) {
                    var counter = document.createElement("div");
                    counter.textContent = "Some dummy text";
                    var widthToSet = textarea.offsetWidth;
                    var width = window.getComputedStyle(textarea, null).width;
                    counter.setAttribute("style", "text-align:right; padding-bottom:1em; width:" + width);
                    textarea.parentNode.insertBefore(counter, textarea);
                    
                    textarea.addEventListener("input", (event) => 
                    {
                        lenghtCounter(event.target || event.srcElement, event.target.previousSibling || event.srcElement.previousSibling);
                    });
                    textarea.addEventListener(\'change\', (event) => 
                    {
                        lenghtCounter(event.target || event.srcElement, event.target.previousSibling || event.srcElement.previousSibling);
                    });
                    lenghtCounter(textarea, counter);
                }
            });

            function lenghtCounter(textarea, counter)
            {
                var lenght = textarea.value.length;
                var messages = lenght / 160 + 1;
                if(lenght > 0)
                {
                    if(lenght % 160 === 0)
                    {
                        messages--;
                    }
                    counter.textContent = "Number of messages: " + Math.floor(messages) + " (" + lenght + ")";
                }else
                {
                    counter.textContent = "Field is empty";
                }
            }
        </script>
    ';
}
