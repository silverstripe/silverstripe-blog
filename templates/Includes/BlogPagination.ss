<% if $PaginatedList.MoreThanOnePage %>
    <nav class="BlogPagination">
        <% if $PaginatedList.NotFirstPage %>
            <a class="prev" href="$PaginatedList.PrevLink">Prev</a>
        <% end_if %>
        <% loop $PaginatedList.Pages %>
            <% if $CurrentBool %>
                $PageNum
            <% else %>
                <% if $Link %>
                    <a href="$Link">$PageNum</a>
                <% else %>
                    ...
                <% end_if %>
            <% end_if %>
            <% end_loop %>
        <% if $PaginatedList.NotLastPage %>
            <a class="next" href="$PaginatedList.NextLink">Next</a>
        <% end_if %>
    </nav>
<% end_if %>