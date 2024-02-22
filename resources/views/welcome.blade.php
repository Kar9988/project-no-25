<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ًApplication</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body>
<div id="app" class="mb-2">
</div>
@vite('resources/js/app.js')
{{--<script src="path/to/chartjs/dist/chart.umd.js"></script>--}}

</body>
</html>
