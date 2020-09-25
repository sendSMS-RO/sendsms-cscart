{capture name="mainbox"}
    {include file="common/pagination.tpl"}
    {if !$errors}
        {include file="common/pagination.tpl"}
        
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
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $presets as $preset}
                        {include file="addons/advanced_import/views/import_presets/components/preset.tpl"}
                    {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
{/capture}




{include file="common/mainbox.tpl"
title="SMS Campaign"
content=$smarty.capture.mainbox
}