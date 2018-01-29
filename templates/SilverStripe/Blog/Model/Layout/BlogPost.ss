<% require css('silverstripe/blog: client/dist/styles/main.css') %>

<div class="blog-entry content-container <% if $SideBarView %>unit size3of4<% end_if %>">
	<article>
		<h1>$Title</h1>

		<% if $FeaturedImage %>
			<p class="post-image">$FeaturedImage.ScaleWidth(795)</p>
		<% end_if %>

		<div class="content">$Content</div>

		<% include SilverStripe\\Blog\\EntryMeta %>
	</article>

	$Form
	$CommentsForm
</div>

<% include SilverStripe\\Blog\\BlogSideBar %>
