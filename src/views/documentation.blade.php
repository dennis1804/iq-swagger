<!DOCTYPE html>
<html>
<head>
    <title>API documentatie</title>
    <meta name="robots" content="noindex, nofollow" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">
    <link href="{{ asset('vendor/swagger/swaggerui/css/typography.css') }}" media="screen" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('vendor/swagger/swaggerui/css/reset.css') }}" media="screen" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('vendor/swagger/swaggerui/css/screen.css') }}" media="screen" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('vendor/swagger/swaggerui/css/reset.css') }}" media="print" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('vendor/swagger/swaggerui/css/print.css') }}" media="print" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/jquery-1.8.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/jquery.slideto.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/jquery.wiggle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/jquery.ba-bbq.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/handlebars-2.0.0.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/underscore-min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/backbone-min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/swagger-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/highlight.7.3.pack.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/jsoneditor.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/swagger/swaggerui/lib/marked.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(function () {
            window.swaggerUi = new SwaggerUi({
                url: "{{route('iq-swagger.swagger')}}",
                dom_id: "swagger-ui-container",
                supportedSubmitMethods: ['get', 'post'],
                onFailure: function (data) {
                    log("Unable to Load Swag");
                },
                docExpansion: "none",
                jsonEditor: false,
                apisSorter : "alpha",
                validatorUrl: null,
                onComplete: function () {
                    $('pre code').each(function (i, e) {
                        hljs.highlightBlock(e)
                    });
                    $('input[name="password"]').prop('type', 'password');

                    // Injecting JWT token field

                    $('#api_info .info_description').after($('#jwt-input-tpl').html());
                    if(localStorage.explorerToken) {
                        var lsToken = JSON.parse(localStorage.explorerToken);
                        if($.now() - lsToken.time < (60 * 60 * 1000)) {
                            $('#jwt-input').val(lsToken.token);
                            swaggerUi.api.clientAuthorizations.add(
                                    "JWT",
                                    new SwaggerClient.ApiKeyAuthorization('Authorization', 'Bearer ' + lsToken.token, 'header')
                            );
                        } else {
                            localStorage.removeItem('explorerToken');
                        }
                    }

                    $('#jwt-set-token').click(function () {
                        var token = $('#jwt-input').val();
                        swaggerUi.api.clientAuthorizations.add(
                                "JWT",
                                new SwaggerClient.ApiKeyAuthorization('Authorization', 'Bearer ' + token, 'header')
                        );
                        localStorage.explorerToken = JSON.stringify({time: $.now(), token: token});
                        alert('JWT token has been set for further requests in API explorer.')
                    });

                }
            });
            window.swaggerUi.load();

            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
        });
    </script>
    <style>
        #back {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            text-align:center;
            background: #008cba;
            font-family: "Source Sans Pro", sans-serif;
            color: #fff;
        }
        .brand-name{
            float:left;
        }
        #back a { color: #fff; text-decoration: none; }
        body { margin-top: 60px; }
        .align-right { text-align: right; }
    </style>
</head>
<body class="swagger-section">
<script type="text/template" id="jwt-input-tpl">
    <div class="align-right">
        <strong>JWT Token:</strong>
        <input type="text" id="jwt-input" placeholder="JWT token">
        <input type="button" id="jwt-set-token" value="Set token">
    </div>
</script>
<div id="back" class="navbar navbar-inverse navbar-static-top" >
<div class="container-fluid">
    <div class="navbar-header">
  <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <a class="brand-name">{{ config('app.name', 'Laravel') }}</a></span>
                </a>
            <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
           <li> <a href="{{ url('/') }}">Terug naar de applicatie</a></li>
           </ul>
    </div>
</div>
</div>
 <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                            <div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
                            <div id="swagger-ui-container" class="swagger-ui-wrap"></div>
                </div>
</div>
</div>
</body>
</html>
