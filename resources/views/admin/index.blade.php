<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('component')


</head>
<body>

    @include('admin.theme.navbar')
    @include('admin.theme.menu')

    <div id="main">
        @include($content)
    </div>

</body>
</html>
