<%-- NOTE: Before including this, you will need to wrap the include in a with block  --%>

<% if $MoreThanOnePage %>
	<p class="pagination">
		<% if $NotFirstPage %>
			<a class="prev" href="{$PrevLink}">&larr;</a>
		<% end_if %>

		<% loop $PaginationSummary(4) %>
			<% if $CurrentBool %>
				<span>$PageNum</span>
			<% else %>
				<% if $Link %>
					<a href="$Link">$PageNum</a>
				<% else %>
					<span>...</span>
				<% end_if %>
			<% end_if %>
		<% end_loop %>

		<% if $NotLastPage %>
			<a class="next" href="{$NextLink}">&rarr;</a>
		<% end_if %>
	</p>
<% end_if %>
