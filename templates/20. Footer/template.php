<!-- Fullpage-Options -->
<script>
var fps_options = {
<?php
$opts = array();
if (rex_addon::get('demo_fullpage')->getConfig('showscrollbar')) {
    $opts[] = 'scrollBar: true';
}
$opts[] = 'scrollingSpeed: ' . rex_addon::get('demo_fullpage')->getConfig('scrollingspeed');
if (rex_addon::get('demo_fullpage')->getConfig('autoscrolling')) {
    $opts[] = 'autoScrolling: true';
    $opts[] = 'fitToSection: true';
}
if (rex_addon::get('demo_fullpage')->getConfig('shownavigation')) {
    $opts[] = 'navigation: true';
    $opts[] = 'navigationPosition: \'' . rex_addon::get('demo_fullpage')->getConfig('navigationposition') . '\'';
}
if (rex_addon::get('demo_fullpage')->getConfig('showslidearrows')) {
    $opts[] = 'controlArrows: true';
}
if (rex_addon::get('demo_fullpage')->getConfig('showslidenavigation')) {
    $opts[] = 'slidesNavigation: true';
    $opts[] = 'slidesNavPosition: \'' . rex_addon::get('demo_fullpage')->getConfig('slidenavigationposition') . '\'';
}
echo implode(",\n", $opts) . "\n";
?>
};
</script>

<!-- Javascripts -->
<script src="<?php echo rex_url::base('assets/addons/demo_fullpage/js/jquery.min.js') ?>"></script>
<script src="<?php echo rex_url::base('assets/addons/demo_fullpage/js/fullPage.min.js') ?>"></script>
<script src="<?php echo rex_url::base('assets/addons/demo_fullpage/js/bootstrap.min.js') ?>"></script>

<script src="<?php echo rex_url::base('assets/addons/demo_fullpage/themes/' . $fullpagedemo['theme'] . '/js/theme.js') ?>"></script>

<?php
// Theme-Switcher ausgeben
if (rex_addon::get('demo_fullpage')->getConfig('themeswitch')) {
    $_ts = '';
    $_ts .= '<div class="for" title="FriendsOfREDAXO - TREX - Gojira"><a href="https://friendsofredaxo.github.io/" class="newwindow"></a></div>' . "\n\n";

    $_ts .= '<ul class="styleswitch" title="Theme Switch">' . "\n";
    $myPath = rex_url::base('redaxo/src/addons/demo_fullpage/assets/themes/');
    $directories = glob($myPath . '/*' , GLOB_ONLYDIR);
    foreach ($directories as $dir) {
        $dir = basename($dir);
        if ($ff = file($myPath  .$dir . '/css/theme.css')) {
            if (isset($ff[0]) and trim($ff[0]) == '/*' and isset($ff[3]) and trim($ff[3]) == '*/' and isset($ff[2])) {
                $_ts .= '<li><a class="' . $dir . '" href="' . rex::getServer() . '?theme=' . $dir . '" title="' . trim($ff[2]) . '"></a></li>' . "\n";
            }
        }
    }
    $_ts .= '</ul>' . "\n";
    echo $_ts;
}
?>

<div style="display:none;">REDAXO 5 really rocks!</div>

</body>
</html>