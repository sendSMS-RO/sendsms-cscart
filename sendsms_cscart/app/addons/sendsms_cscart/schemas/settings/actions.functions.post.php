<?php
function fn_add_autocomplete_variables_sendsms_cscart_messageinformation()
{

    return 'Avaible values: ' . '<a onClick="placeInFocusedTextarea(\'{order_id}\')">' . '{order_id}' .'</a>'. ', ' . '<a onClick="placeInFocusedTextarea(\'{total}\')">'.'{total}'.'</a>'.', ' . '<a onClick="placeInFocusedTextarea(\'{date}\')">'.'{date}'.'</a>'.', ' . '<a onClick="placeInFocusedTextarea(\'{firstname}\')">'.'{firstname} '.'</a>'.'
    <script>
    var focused;
    document.addEventListener("DOMContentLoaded", (event) => {
        var inputs = document.getElementsByTagName("textarea");
        console.log("before for");
        for (var i=0, input; i<inputs.length && (input = inputs[i]); i++) {
            console.log("after for");
            input.addEventListener("focus", function(){
                console.log("focus changed");
                focused = this;
            });
        }
    });
    function placeInFocusedTextarea(textToAdd)
    {
        if(focused)
        {
            focused.value += textToAdd;
        }
    }

    </script>';
}
