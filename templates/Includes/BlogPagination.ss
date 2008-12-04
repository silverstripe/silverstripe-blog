<% if BlogEntries.MoreThanOnePage %>
	<div id="PageNumbers">
		<p>
			<% if BlogEntries.NotFirstPage %>
				<a class="prev" href="$BlogEntries.PrevLink" title="View the previous page">Prev</a>
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
		
			<% if BlogEntries.NotLastPage %>
				<a class="next" href="$BlogEntries.NextLink" title="View the next page">Next</a>
			<% end_if %>
		</p>
	</div>
<% end_if %>