# Configuring Widgets

The blog module comes bundled with some useful widgets. To take advantage of them, you'll need to install the [SilverStripe widgets module](https://github.com/silverstripe/silverstripe-widgets). Widgets are totally optional - so your blog will work just fine without having widgets installed.

You can enable the widgets by adding the following YML config:

```yaml
Blog:
  extensions:
    - WidgetPageExtension
BlogPost:
  extensions:
    - WidgetPageExtension
```

Once you have widgets installed you'll see the "Widgets" tab in the content section of your blog.
