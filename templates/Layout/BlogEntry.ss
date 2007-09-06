<% include BlogSideBar %>
<div id="BlogContent" class="typography">
	<% include BreadCrumbs %>
	
	<div class="blogEntry">
			<h2>$Title</h2>
			<p class="authorDate">Posted by $Author.XML on $Date.Long | $Comments.Count Comments</p>
				<% if Tags %>
					<p class="tags">
						 Tags: 
						<% control Tags %>
							<a href="$Link" title="View all posts tagged '$Tag'">$Tag</a><% if Last %><% else %>,<% end_if %>
						<% end_control %>
					</p>
				<% end_if %>
			<p>$Content.Parse(BBCodeParser)</p>
			<br />
	</div>
			<% if CurrentMember %><p><a href="$EditURL" id="editpost" title="Edit this post">Edit this post</a> | <a href="$Link(unpublishPost)" id="unpublishpost">Unpublish this post</a></p><% end_if %>
			
	$PageComments

</div>