<p>
	This entry was
	<% if Categories %>
		posted in
		<% loop Categories %>
			<a href="$Link" title="$Title">$Title</a><% if not Last %>, <% else_if Up.Tags %>, <% else %> and<% end_if %>
		<% end_loop %>
	<% end_if %>
	<% if Tags %>
		tagged
		<% loop Tags %>
			<a href="$Link" title="$Title">$Title</a><% if not Last %>, <% end_if %>
		<% end_loop %>
		and
	<% end_if %>
	posted on <a href="$MonthlyArchiveLink">$PublishDate.format("F j, Y")</a>
</p>