{capture name="mainbox"}
    {include file="common/scripts.tpl"}
{/capture}

{include file="common/mainbox.tpl"
         title="SMS Campaign"
         content=$smarty.capture.mainbox
}