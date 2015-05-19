<% if $Tags %>
	<ul class="blogTagCloud">
		<% loop $Tags %>
			<li>
				<a href="$Link" title="$TagName">
					<span class="text tagCount{$NormalizedTag}">$TagName</span>
				</a>
			</li>
		<% end_loop %>
	</ul>
	<div style="clear: both;"></div>
<% end_if %>
