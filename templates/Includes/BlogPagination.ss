<% if BlogEntries.MoreThanOnePage %>
	<div id="PageNumbers">
		<% if BlogEntries.NotLastPage %>
			<a class="next" href="$Children.NextLink" title="View the next page">Next</a>
		<% end_if %>
		
		<% if BlogEntries.NotFirstPage %>
			<a class="prev" href="$Children.PrevLink" title="View the previous page">Prev</a>
		<% end_if %>
		
		<span>
	    	<% control BlogEntries.Pages %>
				<% if CurrentBool %>
					$PageNum
				<% else %>
					<a href="$Link" title="View page number $PageNum">$PageNum</a>
				<% end_if %>
			<% end_control %>
		</span>
	</div>
<% end_if %>