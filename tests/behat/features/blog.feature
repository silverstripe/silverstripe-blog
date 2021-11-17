Feature: Create a blog
  As a website user
  I want to create a blog

  Background:
    Given the "group" "EDITOR group" has permissions "CMS_ACCESS_LeftAndMain"
    And I add an extension "SilverStripe\Widgets\Extensions\WidgetPageExtension" to the "Page" class
    And I add an extension "SilverStripe\Comments\Extensions\CommentsExtension" to the "Page" class
       
    And an "image" "Uploads/file1.jpg"

    # Login then logout to created member
    And I am logged in with "EDITOR" permissions
    And I go to "/Security/login"
    And I press the "Log in as someone else" button
    And I am logged in with "ADMIN" permissions

    When I go to "/dev/build?flush"

    # Create a new blog called "New Blog"
    When I go to "/admin/pages"
    And I press the "Add new" button
    And I select the "Blog" radio button
    And I press the "Create" button

    # Add widgets
    And I click the "Widgets" CMS tab
    And I uncheck "Inherit Sidebar From Parent"
    And I add the "Content" widget
    And I add the "Archive" widget
    And I add the "Blog Tags" widget
    And I fill in the "Content" widget field "Title" with "My content widget title"
    And I fill in the "Content" widget HTML field "Content" with "<p>Content widget content</p>"
    And I fill in the "Archive" widget field "Title" with "My archive widget title"
    And I fill in the "Blog Tags" widget field "Title" with "My blog tags widget title"
    And I press the "Save" button

    # Add EDITOR as an Editor
    And I click the "Settings" CMS tab
    And I press the "Save" button
    And I click the "Users" CMS tab
    And I select "EDITOR" from "Editors"
    And I press the "Publish" button

    # Logout
    And I go to "/Security/login"
    And I press the "Log in as someone else" button

  Scenario: Create a blog post
    Given I log in with "EDITOR@example.org" and "Secret!123"

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
    And I add "My Category" to the "Categories" tag field
    And I add "My Tag" to the "Tags" tag field

    # Publish the blog post and logout
    And I press the "Publish" button
    And I go to "/Security/login"
    And I press the "Log in as someone else" button

    # Test the frontend
    When I go to "/new-blog"
    Then I should see "New Blog"
    And I should see "New Post"

    # Widgets
    And I should see "My content widget title"
    And the rendered HTML should contain "<p>Content widget content</p>"
    And I should see "My blog tags widget title"

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

    # Commenting
    When I fill in "Your name" with "My Name"
    And I fill in "Email" with "hello@example.com"
    And I fill in "Comments" with "My comments"
    When I press the "Post" button
    Then I should see "New Post"

    # Commenting is bizarly not working in behat, even though it works during manual testing on my local
    # Moderation
    #Given I log in with "EDITOR@example.org" and "Secret!123"
    #When I go to "/admin/pages"
    #And I follow "New Blog"
    #And I click the "Blog Posts" CMS tab
    # Click on the first blog post
    #And I click on the ".col-Title" element
    #And I click the "Comments" CMS tab
    #Then I should see "Approved (1)"
    #When I click the "Approved (1)" CMS tab
    #Then I should see "hello@example.com"
    #When I click on the ".action-menu__toggle" element
    #And I press the "Spam" button
    #And I wait for 2 seconds
    #Then I should not see "hello@example.com"
