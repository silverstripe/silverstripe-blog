<div class="section clearfix blogSummary">
	<div class="col6 pink-border first left">
		<div class="col1 first left"></div>
		<div class="col5 first left">
			<h3 class="postTitle"><a href="$Link" title="<% _t('VIEWFULL', 'View full post titled -') %> '$Title'">$MenuTitle</a></h3>
			
			<div class="left icon icon-s16 icon-employee"></div><p class="authorDate"> by: $Author.XML</p>
			<p>$Date.Long</p>
			<p><a href="$Link#PageComments_holder" title="View Comments Posted">$Comments.Count <% _t('COMMENTS', 'Comments') %></a></p>
			<p>
				<% if TagsCollection %>
					<p class="tags">
						Tags:
						<% control TagsCollection %>
							<a href="$Link" title="View all posts tagged '$Tag'" rel="tag">$Tag</a><% if Last %><% else %>,<% end_if %>
						<% end_control %>
					</p>
				<% end_if %>
			</p>
			<p class="top-anchor"><a href="contact/#ContactPageClass">↑ top</a></p>
			</div>
		</div>
		<div class="col10 left">
			$ParagraphSummary
			<p class="blogVitals"><a href="$Link" class="readmore" title="Read Full Post">Read the full post</a></p>
		</div>
	
</div>