<!DOCTYPE html>
<html>
    <head>
        <title>Loading...</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex,nofollow">
        <meta name="referrer" content="never">
        <meta http-equiv="refresh" content="0;URL={!! $url !!}" />    
    </head>
    <body>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-108368181-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'UA-108368181-1');
        </script>
        <script>window.location.href = "{!! $url !!}";</script>
    </body>
</html>

