{capture name="mainbox"}
<form class="cm-ajax" action="campaign.php" method="post" name="product_form_817">
    {include file="common/daterange_picker.tpl"
    }

    <input type="hidden" name="result_ids" value="" />
    <input id="button_cart_817" type="submit" name="dispatch[checkout.add..817]" value="Add to cart" />
</form>
{/capture}
{include file="common/pagination.tpl"}
{include file="common/mainbox.tpl"
         title="SMS Campaign"
         content=$smarty.capture.mainbox
}
