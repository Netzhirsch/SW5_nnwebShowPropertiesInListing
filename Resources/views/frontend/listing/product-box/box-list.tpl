{extends file='parent:frontend/listing/product-box/box-list.tpl'}

{block name='frontend_listing_box_article_description'}
    {$smarty.block.parent}
	{if $sArticle.attributes.nnwebShowPropertiesInListing}
		<table class="properties--table">
			<tbody>
				{foreach from=$sArticle.attributes.nnwebShowPropertiesInListing->get('property') item=property}
					<tr>                                        
						<td class="property--label">{$property.name}</td>
						<td class="property--value">{$property.value}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}
{/block}
