h1. Cookbook
The cookbook contains some common tasks and how you can solve them with Haybase.

For all examples we assume Haybase is loaded in the `$T` global variable.

h2. Loading Javascript and CSS
Every WordPress theme contains Javascript and CSS. Haybase makes it easy to load
those files and adds an option to serve minified files, speeding up your website
and saving bandwidth.

h3. Basics
To write <script> and <link> tags containing your CSS/JS you can use the
`loadStylesheets` and `loadJavascripts` functions like this:

    $T->loadJavascripts(
        "http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js",
        "js/javascript.js"
    );

Will output:

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script src="http://www.example.com/wp-content/themes/mytheme/js/javascript.js"></script>

Note how you can include both external (jquery) and internal (javascript.js) files.
Haybase will automatically prefix all url's without 'http' with your theme path.

CSS works the same

    $T->loadStylesheets(
        "css/reset.css",
        "css/style.css"
    );

Will output:

    <link rel="stylesheet" href="http://www.example.com/wp-content/themes/mytheme/css/reset.css" />
    <link rel="stylesheet" href="http://www.example.com/wp-content/themes/mytheme/css/style.css" />
    
h3. Using an array as the argument
Both `loadStylesheets` and `loadJavascripts` also accept an array as an argument:

    $css = array("css/reset.css", "css/style.css");    
    $T->loadStylesheets($css);
    
h3. Returning the value instead of directly outputting it    
If you want the rewritten urls but don't want them written to the page directly you can 
use the `getStylesheets` and `getJavascripts` methods. These functions will rewrite the 
url's, and return them as an array without the html tags:

    $css = $T->getStylesheets("css/reset.css", "css/style.css");
    print_r($css); // array : [0] => "http://www.example.com/wp-content/themes/mytheme/css/reset.css", [1] => "http://www.example.com/wp-content/themes/mytheme/css/style.css"
    
h3. Minified Javascript and CSS
To make your site faster and save bandwidth Haybase provides a way to minify your Javascript and CSS.
This means that all seperate CSS/JS files are merged together and made as 
compact as possible by stripping out everything that isn't being processed by the 
browser, such as comments and whitespace. For example, a library such as jQuery 
is normally around 180kb, but only 77kb when minified. 

Haybase concats all your files and saves the new file under a 'hashed' name in 
its cache. This might look a little weird in your source, but it is the best 
way to make sure you don't overwrite other files.

To minify your CSS / Javascript simply use the `loadMinifiedJavascripts` and 
`loadMinifiedStylesheets` functions:

    $T->loadMinifiedJavascripts(
        "js/jquery.masonry.js",
        "js/jquery.colorbox.js",
        "js/javascript.js"
    );
    
Will output something like this:
    
    <script src="http://example.com/wp-content/plugins/haybase/cache/js/73a9c334c5ca71d70d092b42064f6476.js"></script>
    
Just as with the regular functions you can use `getMinifiedStylesheets` and 
`getMinifiedJavascripts` to get an array of files instead of directly writing to 
your page.

h3. Considerations during development
Using the minified option might pose some problems when developing. Haybase caches 
your minified files and therefore any changes you make in your files **won't** 
be seen and your cached files won't be updated. 

You can force a cache purge (and re-minifying of all CSS and JS) by adding the 
`purgecache` parameter to an URL like this:

    http://www.example.com?purgecache=1
    
This is still not very handy during development, when you want to see the 
individual files, so you might be better of 
writing a small if statement in your theme to load individual files (for development) 
or the minified files (for production), based on a URL parameter.

Here's an example:

    $css = array("css/reset.css", "css/style.css", "css/print.css");
    if (empty($_GET['debug')) {
        // Production
        $T->loadMinifiedStylesheets($css);
    } else {
        // Development
        $T->loadStylesheets($css);
    }
    
So, whenever you load an url like
    
    http://www.example.com/?debug=1
    
You will get the individual stylesheets instead of the minified one.