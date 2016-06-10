<p class="blog-post-meta">
	<% if $Categories.exists %>
		<%t Blog.PostedIn "Posted in" %>
		<% loop $Categories %>
			<a href="$Link" title="$Title">$Title</a><% if not Last %>, <% else %>;<% end_if %>
		<% end_loop %>
	<% end_if %>

	<% if $Tags.exists %>
		<%t Blog.Tagged "Tagged" %>
		<% loop $Tags %>
			<a href="$Link" title="$Title">$Title</a><% if not Last %>, <% else %>;<% end_if %>
		<% end_loop %>
	<% end_if %>

	<% if $Comments.exists %>
		<a href="{$Link}#comments-holder">
			<%t Blog.Comments "Comments" %>
			$Comments.count
		</a>;
	<% end_if %>

	<%t Blog.Posted "Posted" %>
	<a href="$MonthlyArchiveLink">$PublishDate.ago</a>

	<% if $Credits %>
		<%t Blog.By "by" %>
		<% loop $Credits %><% if not $First && not $Last %>, <% end_if %><% if not $First && $Last %> <%t Blog.AND "and" %> <% end_if %><% if $URLSegment %><a href="$URL">$Name.XML</a><% else %>$Name.XML<% end_if %><% end_loop %>
	<% end_if %>

</p>
