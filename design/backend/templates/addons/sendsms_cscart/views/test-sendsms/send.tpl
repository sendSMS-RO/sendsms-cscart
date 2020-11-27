{capture name="mainbox"}
    <div id="content_sendsms_cscart_messages">
        <form id="campaign-form" class="cm-ajax submitForm" action="{"test-sendsms.send"|fn_url}" method="POST" name="test.sms.3">
                <label for="phone"  class="cm-required">Test phone</label>
                <input type="number" step="0" id="phone" name="phone">
                <br/>
                <label class="checkbox inline"><input class="checkbox" type="checkbox" id="short-message" name="short_sendsms_1">Change all long url to short url? (Please use only urls that start with https:// or http://)</label>
                {literal}
                    <br>
                    <label class="checkbox inline">
                    <input class="checkbox" type="checkbox" id="gdpr-message" name="gdpr_sendsms_1">
                    Add unsubscribe link? (You must specify {gdpr} key message. {gdpr} key will be replaced automaticaly with confirmation unique confirmation link. If {gdpr} key is not specified confirmation link will be placed at the end of message.)</label>
                {/literal}
                <label for="message-to-send" class="cm-required">Message:</label>
                <textarea class="input-textarea-long" id="message-to-send" name="message" rows="10"></textarea>
            </fieldset>
            {include file="buttons/button.tpl"
                        but_role="submit" 
                        but_name="save"
                        but_text="Send message"
                        but_id="submit-test"
            }
        </form>
    </div>
    <div class=”cm-notification-container”><div>
{/capture}
<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        //textarea counter
        var textarea = document.getElementById("message-to-send");
        if(textarea != null)
        {
            var counter = document.createElement("div");
            counter.textContent = "";
            var widthToSet = textarea.offsetWidth;
            counter.setAttribute("style", "text-align:left; padding-bottom:1em; width:100%");
            textarea.parentNode.insertBefore(counter, textarea.nextSibling);
            textarea.addEventListener("input", (event) => 
            {
                lenghtCounter(event.target || event.srcElement, counter);
            });
            textarea.addEventListener('change', (event) => 
            {
                lenghtCounter(event.target || event.srcElement, counter);
            });
            lenghtCounter(textarea, counter);
        }
    })
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
            counter.textContent = "";
        }
    }
</script>
<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button 
    {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
{include file="common/mainbox.tpl"
         title="SMS Test"
         content=$smarty.capture.mainbox
}