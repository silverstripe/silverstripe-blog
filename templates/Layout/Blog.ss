<% require themedCSS('blog', 'blog') %>

<div class="blog-entry content-container <% if $SideBarView %>unit size3of4<% end_if %>">

    <article>
        <h1>
            <% if $ArchiveYear %>
                <%t Blog.Archive "Archive" %>:
                <% if $ArchiveDay %>
                    $ArchiveDate.Nice
                <% else_if $ArchiveMonth %>
                    $ArchiveDate.format("F, Y")
                <% else %>
                    $ArchiveDate.format("Y")
                <% end_if %>
            <% else_if $CurrentTag %>
                <%t Blog.Tag "Tag" %>: $CurrentTag.Title
            <% else_if $CurrentCategory %>
                <%t Blog.Category "Category" %>: $CurrentCategory.Title
            <% else %>
                $Title
            <% end_if %>
        </h1>
        
        <div class="content">$Content</div>
        
        <% if $PaginatedList.Exists %>
            <% loop $PaginatedList %>
                <div class="post-summary">
                    <h2>
                        <a href="$Link" title="<%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>">
                            <% if $MenuTitle %>$MenuTitle
                            <% else %>$Title<% end_if %>
                        </a>
                    </h2>
                    
                    <p class="post-image">
                        <a href="$Link" <%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>>
                            $FeaturedImage.setWidth(795)
                        </a>
                    </p>
                    
                    <% if $Excerpt %>
                        <p>
                            $Excerpt
                            <a href="$Link">
                                <%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>
                            </a>
                        </p>
                    <% else %>
                        <p><a href="$Link">
                            <%t Blog.ReadMoreAbout "Read more about '{title}'..." title=$Title %>
                        </a></p>
                    <% end_if %>
                    
                    <% include EntryMeta %>
                </div>
            <% end_loop %>
        <% else %>
            <p><%t Blog.NoPosts "There are no posts" %></p>
        <% end_if %>
    </article>
    
    $Form
    $PageComments
        
    <% with $PaginatedList %>
		<% include Pagination %>
	<% end_with %>
</div>

<% include BlogSideBar %>
