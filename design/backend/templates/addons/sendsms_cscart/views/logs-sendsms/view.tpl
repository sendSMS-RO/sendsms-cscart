{capture name="mainbox"}
    {if $errors}
    <form id="search-form" action="{"logs-sendsms.view"|fn_url}" method="post" name="logs.sms.1">
        <label style="display:inline; font-size:15px" for="phone-number">Search by phone number: </label>
        <input style="margin-bottom: 0" type="number" id="phone-number" name="phone">
        <label style="display:inline; font-size:15px" for="date">Search by date: </label>
        <input style="margin-bottom: 0" type="date" id="date" name="date">
        {include file="buttons/button.tpl"
                        but_role="submit" 
                        but_name="save"
                        but_text="Show logs"
            }
    </form>
    {include file="common/pagination.tpl" 
        
    }
    
    <div class="table-responsive-wrapper">
        <table width="100%" class="table table-middle table--relative table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Send to</th>
                    <th>Date</th>
                    <th>Message</th>
                    <th>Info</th>
                </tr>
            </thead>
            <tbody>
                {foreach $errors as $error}
                    {include file="addons/sendsms_cscart/views/logs-sendsms/components/error.tpl"}
                {/foreach}
            </tbody>
        </table>
    </div>
    
    <div class="clearfix">
        {include file="common/pagination.tpl"}
    </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
{/capture}

{include file="common/mainbox.tpl"
title="SMS Logs"
content=$smarty.capture.mainbox
}