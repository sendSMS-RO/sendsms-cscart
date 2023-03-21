{if Tygh\Registry::get('addons.sendsms_cscart.allow-vendor-access') eq 'N' and $auth.user_type neq 'A'}
{else}
    {include file="common/subheader.tpl" title="Send a message"}

    <form>
    </form>
    <div class="control-group">
        <label class="control-label">SendSMS</label>
        <div class="controls">
            <form id="order-message-form" class="cm-ajax submitForm" action="" method="post" name="order.sms.2">
                <label>Message:<textarea class="input-textarea-long" id="message-to-send" name="message_sendsms_1" rows="8"></textarea></label>
                <label class="checkbox inline"><input class="checkbox" type="checkbox" id="short-message" name="short_sendsms_1">Change all long url to short url? (Please use only urls that start with https:// or http://)</label>
                {literal}
                <br/>
                <label class="checkbox inline"><input class="checkbox" type="checkbox" id="gdpr-message" name="gdpr_sendsms_1">Add unsubscribe link? (You must specify {gdpr} key message. {gdpr} key will be replaced automaticaly with confirmation unique confirmation link. If {gdpr} key is not specified confirmation link will be placed at the end of message.)</label>
                {/literal}
                <div style="display: block; margin-left: auto; margin-right: auto; width: 40%; margin-top: 1em;">
                    {include file="buttons/button.tpl"
                    but_role="submit"
                    but_name="send_sms_message"
                    but_text="Send message"
                    }
                </div>
            </form>
        </div>
    </div>
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
{/if}