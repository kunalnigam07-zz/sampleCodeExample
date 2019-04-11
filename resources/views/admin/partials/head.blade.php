<title>{{ (isset($page[0][1]) ? $page[0][1] . ' - ' : '') . (isset($page[1][1]) ? $page[1][1] . ' - ' : '') . (isset($page[2][1]) ? $page[2][1] . ' - ' : '') . 'Admin' }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="{{ AssetHelper::asset('static/admin/css/style.css') }}">
<link rel="stylesheet" href="{{ AssetHelper::asset('static/admin/css/colours.css') }}">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<link rel="shortcut icon" href="{{ AssetHelper::asset('static/web/images/favicon.ico') }}">
<link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
