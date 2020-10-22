<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

{capture name="mainbox"}
    <div id="content_sendsms_cscart_messages">
        <form id="campaign-form" class="cm-ajax submitForm" action="{"campaign.manage"|fn_url}" method="post" name="campaign.sms.1">
            <fieldset>
                <h4>When was the order placed?</h4>
                <div>
                    <input style="margin-top:0px"  type="radio" id="anytime" name="time" checked="checked" value="all">
                    <label style="display:inline;" for="anytime">It doesn't matter</label><br>
                </div>
                <div>
                    <input style="margin-top:0px" type="radio" id="3-mths" name="time" value="3-mths">
                    <label style="display:inline;" for="3-mths">In the last 3 months</label><br>
                </div>
                <div>
                    <input style="margin-top:0px" type="radio" id="6-mths" name="time" value="6-mths">
                    <label style="display:inline;" for="6-mths">In the last 6 months</label><br>
                </div>
                <div>
                    <input style="margin-top:0px" type="radio" id="other" name="time" value="period">
                    <label style="display:inline;" for="other">Time period</label><br>
                    <label for="start">From:</label>
                    <input type="date" id="start" name="from">
                    <label for="start">To (inclusive):</label>
                    <input type="date" id="end" name="to">
                </div>
            </fieldset>
            <br>
            <fieldset>
                <h4>Select the country of your customer or left it empty for all your customers.</h4>
                <select class="country-selector" name="countries[]" multiple style="width: 258px;">                
                    {foreach $countries as $country}
                        {foreach $country as $country_name}
                            <option value="{$country_name}">{$country_name}</option>
                        {/foreach}
                    {/foreach}
                </select>
                <br>
                <h4>Find a product bought by your customers or left it empty for all your customers.</h4>
                {include file="common/products_to_search.tpl"}

                <label for="price">Choose the minimum order price</label>
                <input type="number" step="0.01" min=0 id="price" name="price" value=0>

                <label for="message-to-send" class="cm-required (asd)">Message:</label>
                <textarea class="input-textarea-long" id="message-to-send" name="message" rows="10"></textarea>
            </fieldset>

            <input id="result_ids" type="hidden" name="result_ids" value="" />
                {include file="buttons/button.tpl"
                            but_role="submit" 
                            but_name="check"
                            but_text="How many sms will be send?"
                }
                {include file="buttons/button.tpl"
                            but_role="submit" 
                            but_name="save"
                            but_text="Send message"
                }

        </form>
    </div>
    <div class=”cm-notification-container”><div>
{/capture}
<script>
    //jquery
    $(document).ready(function() {
        $('.country-selector').select2();
            });

    var focused;
    document.addEventListener("DOMContentLoaded", (event) => {
        //textarea counter
        var textareas = document.getElementsByTagName("textarea");
        for (var i=0, textarea; i<textareas.length && (textarea = textareas[i]); i++) {
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

<style type="text/css">
    .select2-selection__rendered
    {
        display:block !important;
    }
    .select2-search--inline 
    {
        float: inherit !important;
    }
</style>

{include file="common/mainbox.tpl"
         title="SMS Campaign"
         content=$smarty.capture.mainbox
}

