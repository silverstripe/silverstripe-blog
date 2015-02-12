<% require css(themes/simple_blog/css/blog.css) %>

<% include BlogSideBar %>
<div class="unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>

        <% if FeaturedImage %>
            <img src="$FeaturedImage.URL" alt="$FeaturedImage.Title" class="center" />
        <% end_if %>

		<div class="content">$Content</div>

		<% include EntryMeta %>
	</article>
	$PageComments
</div>