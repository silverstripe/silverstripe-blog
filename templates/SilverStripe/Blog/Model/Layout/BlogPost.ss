<% require themedCSS('blog', 'blog') %>

<div class="blog-entry content-container <% if $SideBarView %>unit size3of4<% end_if %>">
	<article>
		<h1>$Title</h1>

		<% if $FeaturedImage %>
			<p class="post-image">$FeaturedImage.setWidth(795)</p>
		<% end_if %>

		<div class="content">$Content</div>

		<% include EntryMeta %>
	</article>

	$Form
	$CommentsForm
</div>

<% include BlogSideBar %>
