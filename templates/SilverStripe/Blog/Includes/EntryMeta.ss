<p class="blog-post-meta">
    <% if $Categories.exists %>
        <%t SilverStripe\\Blog\\Model\\Blog.PostedIn "Posted in" %>
        <% loop $Categories %>
            <a href="$Link" title="$Title">$Title</a><% if not Last %>, <% else %>;<% end_if %>
        <% end_loop %>
    <% end_if %>

    <% if $Tags.exists %>
        <%t SilverStripe\\Blog\\Model\\Blog.Tagged "Tagged" %>
        <% loop $Tags %>
            <a href="$Link" title="$Title">$Title</a><% if not Last %>, <% else %>;<% end_if %>
        <% end_loop %>
    <% end_if %>

    <% if $Comments.exists %>
        <a href="{$Link}#comments-holder">
            <%t SilverStripe\\Blog\\Model\\Blog.Comments "Comments" %>
            $Comments.count
        </a>;
    <% end_if %>

    <%t SilverStripe\\Blog\\Model\\Blog.Posted "Posted" %>
    <a href="$MonthlyArchiveLink">$PublishDate.ago</a>

    <% if $Credits %>
        <%t SilverStripe\\Blog\\Model\\Blog.By "by" %>

        <% loop $Credits %>
            <% if not $First && not $Last %>, <% end_if %>
            <% if not $First && $Last %> <%t SilverStripe\\Blog\\Model\\Blog.AND "and" %> <% end_if %>
            <% if $URLSegment && not $Up.ProfilesDisabled %>
                <a href="$URL">$Name.XML</a>
            <% else %>
                $Name.XML
            <% end_if %>
        <% end_loop %>
    <% end_if %>

    <% if $MinutesToRead < 1 %>
        <%t SilverStripe\\Blog\\Model\\Blog.LessThanAMinuteToRead "Less than a minute to read" %>
    <% else %>
        $MinutesToRead <%t SilverStripe\\Blog\\Model\\Blog.MinutesToRead "Minute(s) to read" %>
    <% end_if %>
</p>
