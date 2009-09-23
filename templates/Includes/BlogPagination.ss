<% if BlogEntries.MoreThanOnePage %>
	<div id="PageNumbers">
		<p>
			<% if BlogEntries.NotFirstPage %>
				<a class="prev" href="$BlogEntries.PrevLink" title="View the previous page">Prev</a>
			<% end_if %>
		
			<span>
		    	<% control BlogEntries.PaginationSummary(4) %>
					<% if CurrentBool %>
						<span class="current">$PageNum</span>
					<% else %>
						<% if Link %>
							<a href="$Link" title="View page number $PageNum">$PageNum</a>
						<% else %>
							&hellip;
						<% end_if %>
					<% end_if %>
				<% end_control %>
			</span>
		
			<% if BlogEntries.NotLastPage %>
				<a class="next" href="$BlogEntries.NextLink" title="View the next page">Next</a>
			<% end_if %>
		</p>
	</div>
<% end_if %>