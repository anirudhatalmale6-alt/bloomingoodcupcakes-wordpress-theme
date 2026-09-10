# Not part of the theme

These two files install to `wp-content/mu-plugins/`, **not** into the theme.

They hold the enquiry post type, the spam scoring and the email delivery. They
live outside the theme deliberately: the QC checklist requires custom form
handlers to sit in a mu-plugin or child theme, because a theme update silently
wipes a handler in the parent theme and the leads then stop with no error
anywhere. The post type matters for the same reason in reverse — if the theme
registered it and the theme were switched, every stored enquiry would vanish
from wp-admin while its rows sat untouched in the database.

The theme calls these functions defensively (`function_exists`). If the
mu-plugins are missing the order section shows the phone number instead of a
form that cannot deliver.

Exclude this folder when zipping the theme for deployment:

    zip -qr bloomingood.zip bloomingood -x '*/.git/*' '*/_mu-plugins/*'
