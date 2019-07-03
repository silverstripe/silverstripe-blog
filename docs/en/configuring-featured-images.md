## Configuring featured images

By default, featured images for the blog are uploaded to the default SilverStripe location.  
If you prefer, you can specify a directory into which featured images will be uploaded by adding the following to your project's config:



```yaml
SilverStripe\Blog\Model\BlogPost:
  featured_images_directory: 'blog-images'
```

replacing 'blog-images' with the name of the directory you wish to use.