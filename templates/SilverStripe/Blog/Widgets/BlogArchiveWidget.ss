<% if $Archive %>
	<ul>
		<% loop $Archive %>
			<li>
				<a href="$Link" title="$Title">
					<span class="arrow">&rarr;</span>
					<span class="text">$Title</span>
				</a>
			</li>
		<% end_loop %>
	</ul>
<% end_if %>
