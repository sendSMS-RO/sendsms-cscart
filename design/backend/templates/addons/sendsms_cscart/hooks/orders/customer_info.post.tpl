{include file="common/subheader.tpl" title="Send a message"}

<div class="control-group">
    <label class="control-label">Message</label>
    <div class="controls">
        <form id="order-message-form" class="cm-ajax submitForm" action="{"orders.details"|fn_url}" method="post" name="order.sms.2">
            <textarea class="input-textarea-long" id="message-to-send" name="message_sendsms_1" rows="8"></textarea>

            <input id="result_ids" type="hidden" name="result_ids" value="" />

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