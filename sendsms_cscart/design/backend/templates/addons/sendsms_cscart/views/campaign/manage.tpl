{capture name="mainbox"}
    <div id="content_sendsms_cscart_messages">
        <form id="campaign-form" class="cm-ajax submitForm" action="{"campaign.manage"|fn_url}" method="post" name="campaign.sms.1">
            <fieldset>
                <h4>When was the order placed?</h4>
                <div>
                    <input style="margin-top:0px"  type="radio" id="anytime" name="time" checked="checked">
                    <label style="display:inline;" for="anytime">It doesn't matter</label><br>
                </div>
                <div>
                    <input style="margin-top:0px" type="radio" id="3-mths" name="time">
                    <label style="display:inline;" for="3-mths">In the last 3 months</label><br>
                </div>
                <div>
                    <input style="margin-top:0px" type="radio" id="6-mths" name="time">
                    <label style="display:inline;" for="6-mths">In the last 6 months</label><br>
                </div>
                <div>
                    <input style="margin-top:0px" type="radio" id="other" name="time">
                    <label style="display:inline;" for="other">Time period</label><br>
                    <label for="start">From:</label>
                    <input type="date" id="start" name="From">
                    <label for="start">To:</label>
                    <input type="date" id="end" name="To">
                </div>
            </fieldset>

            <input id="result_ids" type="hidden" name="result_ids" value="" />
                {include file="buttons/button.tpl"
                            but_role="submit" 
                            but_name="Save"
                            but_text="Send message"
                }

        </form>
    </div>
{/capture}
{include file="common/mainbox.tpl"
         title="SMS Campaign"
         content=$smarty.capture.mainbox
}

