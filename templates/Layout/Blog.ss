<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		<div class="content">$Content</div>
		<% if BlogPosts %>
			<ul>
				<% loop BlogPosts %>
					<li><a href="$Link" title="$Title">$Title</a></li>
				<% end_loop %>
			</ul>
		<% else %>
			<p>Unable to find any blog posts.</p>
		<% end_if %>
	</article>
		$Form
		$PageComments
</div>