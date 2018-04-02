<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Flowerbug's Inkspot</title>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        body {
            background-color: #BAEAFF;
        }
    </style>
</head>

<body>
<div id="container">
    <div id="banner"><a href="/"><img src="{{ url('images/banner.jpg') }}" width="900" height="200" alt="Flowerbug's Stampin Shop"/></a>
    </div>
    <div id="column"></div>
    <div id="buttons">
        <a class="projectFilter" href="/index.php?type=allprojects">All Projects</a>
        <a class="projectFilter" href="/index.php?type=techniqueclasses">Technique Classes</a>
        <a class="projectFilter" href="/index.php?type=3dProjects">3-D Projects</a>
        <a class="projectFilter" href="/index.php?type=scrapBookProjects">Scrapbook Projects</a>
        <a class="projectFilter" href="/index.php?type=4packs">4-Packs</a>
    </div>
    <div id="content"> @yield('content')</div>
    <div id="disclaimer"> LeeAnn Greff, Independent Stampin' Up! Demonstrator, Manager. The content of this website is
        my sole responsibility as an independent Stampin' Up! demonstrator and the use of, and content of, the classes,
        services, or products offered on this website is not endorsed by Stampin' Up!
    </div>
</div>
<!-- google analytics -->
<script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-29634963-1']);
    _gaq.push(['_setDomainName', 'flowerbugshop.com']);
    _gaq.push(['_trackPageview']);

    (function () {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();

</script>
<!-- end google analytics -->
</body>
</html>
