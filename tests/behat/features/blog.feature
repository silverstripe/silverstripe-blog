Feature: Create a blog
  As a website user
  I want to create a blog

  Background:
    Given the "group" "EDITOR" has permissions "CMS_ACCESS_CMSMain"

    And an "image" "Uploads/file1.jpg"
    And I am logged in as a member of "EDITOR" group

    # Create a new blog called "New Blog"
    When I go to "/admin/pages"
    And I press the "Add new" button
    And I select the "Blog" radio button
    And I press the "Create" button

    # Logout
    And I go to "/Security/login"
    And I press the "Log in as someone else" button
    And I am logged in with "ADMIN" permissions

    # Add EDITOR as an Editor
    When I go to "/admin/pages"
    And I follow "New Blog"
    And I click the "Settings" CMS tab
    And I click the "Users" CMS tab
    And I wait for 3 seconds
    And I select "EDITOR" from "Editors"
    And I press the "Publish" button

    # Logout
    And I go to "/Security/login"
    And I press the "Log in as someone else" button

  Scenario: Create a blog post

    Given I am logged in as a member of "EDITOR" group
    # Create a new blog post called "New Post"
    When I go to "/admin/pages"
    And I follow "New Blog"
    And I click the "Blog Posts" CMS tab
    And I press the "Add new Blog Post" button
    And I fill in "Post Title" with "New Post"

    # Add a "Featured image"
    And I press the "Choose existing" button

    # Select file1.jpg - asset-admin FeatureContext is not available here so use css selector
    And I click on the ".gallery__files .gallery-item" element
    And I press the "Insert" button

    # Add categories and tags
    And I click the "Post Options" CMS tab
    And I add "My Category" to the "#Form_EditForm_Categories_Holder" tag field
    And I add "My Tag" to the "#Form_EditForm_Tags_Holder" tag field

    # Publish the blog post and logout
    And I press the "Publish" button
    And I go to "/Security/login"
    And I press the "Log in as someone else" button

    # Test the frontend
    When I go to "/new-blog"
    Then I should see "New Blog"
    And I should see "New Post"

    # Hyperlink to "New Post"
    Then the rendered HTML should contain "href=\"/new-blog/new-post"

    # Category
    And the rendered HTML should contain "href=\"/new-blog/category/my-category\""

    # Tag
    And the rendered HTML should contain "href=\"/new-blog/tag/my-tag\""

    # Test that blog post shows in category view
    When I go to "/new-blog/category/my-category"
    Then I should see "New Post"

    # Test that blog post shows in tag view
    When I go to "/new-blog/tag/my-tag"
    Then I should see "New Post"
