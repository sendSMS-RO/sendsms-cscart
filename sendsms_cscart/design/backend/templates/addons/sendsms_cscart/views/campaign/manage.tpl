{$c_dummy = "<i class=\"icon-dummy\"></i>"}
{$c_icon  = "<i class=\"icon-`$search.sort_order_rev`\"></i>"}
{$c_url   = $config.current_url|fn_query_remove:"sort_by":"sort_order"}
{$rev     = $smarty.request.content_id|default:"pagination_contents"}