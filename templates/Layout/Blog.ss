<% require css(themes/simple_blog/css/blog.css) %>

<% include BlogSideBar %>
<div class="unit size3of4 lastUnit searchResults">
	<h1>
		<% if ArchiveYear %>
			Archive: <% if ArchiveDay %>$ArchiveDate.Nice<% else_if ArchiveMonth %>$ArchiveDate.format("F, Y")<% else %>$ArchiveDate.format(Y)<% end_if %>
		<% else_if CurrentTag %>
			Tag: $CurrentTag.Title
		<% else_if CurrentCategory %>
			Category: $CurrentCategory.Title
		<% else %>
			$Title
		<% end_if %>
	</h1>

	<% if PaginatedList %>
	<ul id="SearchResults">
		<% loop PaginatedList %>
		<li class="clear">
			<% if FeaturedImage %>
				<img src="$FeaturedImage.CroppedImage(200, 120).URL" alt="$FeaturedImage.Title" class="left" />
			<% end_if %>
			<h4>
				<a href="$Link">
					<% if $MenuTitle %>
					$MenuTitle
					<% else %>
					$Title
					<% end_if %>
				</a>
			</h4>
			<% if $Excerpt %>
				<p>$Excerpt</p>
			<% end_if %>
			<a class="readMoreLink" href="$Link" title="Read more about &quot;{$Title}&quot;">Read more about &quot;{$Title}&quot;...</a>
		</li>
		<% end_loop %>
	</ul>
	<% else %>
		<div class="content"><p>No Posts</p></div>
	<% end_if %>

	<% if PaginatedList.MoreThanOnePage %>
	<div id="PageNumbers">
		<div class="pagination">
			<% if PaginatedList.NotFirstPage %>
				<a class="prev" href="$PaginatedList.PrevLink" title="View the previous page">&larr;</a>
			<% end_if %>
			<% loop PaginatedList.Pages %>
				<% if $CurrentBool %>
					$PageNum
				<% else %>
					<a href="$Link" title="View page number $PageNum" class="go-to-page">$PageNum</a>
				<% end_if %>
			<% end_loop %>
			<% if PaginatedList.NotLastPage %>
				<a class="next" href="$PaginatedList.NextLink" title="View the next page">&rarr;</a>
			<% end_if %>
		</div>
		<p>Page $PaginatedList.CurrentPage of $PaginatedList.TotalPages</p>
	</div>
	<% end_if %>
</div>
