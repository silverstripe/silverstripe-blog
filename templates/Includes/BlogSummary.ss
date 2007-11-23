<div class="blogSummary">
	<h2><a href="$Link" title="View full post titled - '$Title'">$MenuTitle</a></h2>
	<p class="authorDate">Posted by $Author.XML on $Date.Long | <a href="$Link#PageComments_holder" title="View Comments Posted">$Comments.Count Comments</a></p>
	<% if Tags %>
		<p class="tags">
			Tags:
			<% control Tags %>
				<a href="$Link" title="View all posts tagged '$Tag'" rel="tag">$Tag</a><% if Last %><% else %>,<% end_if %>
			<% end_control %>
		</p>
	<% end_if %>
	<p>$ParagraphSummary</p>
	<p class="blogVitals"><a href="$Link#PageComments_holder" class="comments" title="View Comments for this post">$Comments.Count comments</a> | <a href="$Link" class="readmore" title="Read Full Post">Read the full post</a></p>
</div>
