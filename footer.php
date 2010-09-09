<?php global $T; ?>
</div> <!-- #wrapper -->
<?php wp_footer(); ?>

<script>
<?php
/*
    This is a simple Javascript loader that accepts an array of files that are
    loaded after each other and accept an optional callback. Add extra
    javascripts to your inc/config.json under 'javascript->custom'. If you
    prefix them with http:// it is assumed they are external, otherwise they
    are loaded from the theme directory.
    < http://www.haykranen.nl/projects/haykranen >
*/
?>
function jsDynaLoad(b,c){function f(h,d){d=d||function(){};var a=document.createElement("script");a.type="text/javascript";if(a.readyState)a.onreadystatechange=function(){if(a.readyState==="loaded"||a.readyState==="complete"){a.onreadystatechange=null;d()}};else a.onload=function(){d()};a.src=h;document.getElementsByTagName("head")[0].appendChild(a)}c=c||function(){};if(typeof b==="string")f(b,c);else if(b instanceof Array){var e=0,i=b.length,g=function(){if(e>=i){c();return false}f(b[e],g);e++};
g()}};

var __scripts = [];
<?php if ($T->getConfig()->javascript->jquery): ?>
__scripts.push("http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js");
<?php endif; ?>

<?php if ($T->getConfig()->javascript->files): ?>
    <?php foreach($T->getConfig()->javascript->files as $js) :?>
        __scripts.push("<?php echo $js; ?>");
    <?php endforeach; ?>
<?php endif; ?>

jsDynaLoad(__scripts);
</script>

<?php if ($T->getConfig()->google_analytics->enabled): ?>
<!-- Google Analytics -->
<script>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo $T->getConfig()->google_analytics->id; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- /Google Analytics -->
<?php endif; ?>

<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
<?php /* "Just what do you think you're doing Dave?" */ ?>
</body>
</html>