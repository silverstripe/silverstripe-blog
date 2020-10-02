<div class="add-existing-autocompleter">
    <div class="input-group">
        <% loop $Fields %>
            <% if $Type == 'action' %>
                <div class="input-group-append">
                    $Field
                </div>
            <% else %>
                $Field
            <% end_if %>
        <% end_loop %>
	</div>
</div>
