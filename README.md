Haybase
=======
A clean, object-orientated PHP5 theme framework for WordPress

If you're like me, you like clean object-orientated modern PHP5 code.
Unfortunately 99% of all WordPress themes and plugins (including the core
itself) is not OO at all, in fact, it's a mess. Mixing of code and HTML, using
endless functions instead of classes, repetition of large blocks of code. Does
this sound familiar to you? It makes developing a clean theme a lot of work,
because there is no good foundation.

If you're like me, you will therefore love Haybase, a great starting point for
your new HTML5 WordPress theme, with all the building blocks you need when
developing a great new theme.

Features
--------
* Clean, object-orientated, PHP5 code with classes instead of
  endlessly_long_function_names_with_lots_of_underscores_and_prefixes
* HTML5 from the ground up, no useless XHTML namespaces and other stuff you
  don't need.
* Loads Javascripts at the bottom of the page with a Javascript loader,
  to speed up your site
* HTML5 CSS reset
* Easy addition of sidebars / widget areas
* Timthumb image resize script for post thumbnails
* Configuration by a JSON file instead of PHP constants or global variables
* Easy ways to access common variables in your theme
* Open source, GPL licensed. Just like WordPress itself.

Requirements
------------
* PHP 5.2 or higher
* WordPress 2.7 or higher

Documentation
-------------
Learn more about Haybase
http://www.haykranen.nl/projects/haybase

Fork it on github
http://www.github.com/hay/haybase

Mail me
hay at bykr dot org

FAQ
---
*I don't get it. I installed your theme and now i get unformatted pages.
What's the deal?*

Haybase is not ment as a production-ready theme you can install to make your
blog look pretty. It's a starting point to make your own theme.

*What's this `$T` i see everywhere in the code?*

`$T` is the global variable that has the instance of the current theme class.
You use this to access all the public methods and variables of Haybase and your
derived theme class. Read the [docs] for an overview of all available
options.

*Why can't i use kubrick, twentyten or (insert other theme here) as a starting
point for my own theme?*

I did that for years, but all themes include lots of stuff you don't need and
many things you do need, but not in the way you want it. The best way to start a
new theme is to start from scratch. However, many lower-level functions are
very common amongs themes, and Haybase provides a nice interface and starting
point for those functions.

*Where's (something.php) in your theme? It's included in twentyten/kubrick!*

You don't need all files for every site. A theme actually only needs two files:
style.css and index.php. WordPress can figure out most 'types' from only an
index.php file. This saves in maintenance, so only make pages for 'page types'
you really need.

*I get a 'Could not decode JSON' error!*

The config.json in 'inc/' should be valid. Check for missing commas and quotes.
Copy-paste your file in [jsonlint] to figure out what's wrong.

*Why bother with WordPress at all? Why not use a proper coded CMS such as 
(insert name here)?*

Many people know WordPress, and the ui is pretty nice. There are thousands of 
plugins available, and it runs on virtually any web host. 

TODO
----
Cool stuff in the near feature (fork on Github and contribute if you want!)
* Configuration of theme options page
* Move config.json options to that theme page (maybe?, might be a bit heavy on
  the db.
* Make something similar for plugins
* Let Haybase make coffee and do your dishes (nah, not really ;)

Thank you
---------
Haybase includes code from:
* html5reset:
* html5boilerplate

[docs]: http://www.haykranen.nl/projects/haybase
[jsonlint]: http://www.jsonlint.com