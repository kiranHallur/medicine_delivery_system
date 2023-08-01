<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
        <title>@yield('title', '')</title>
        <meta content="" name="description">
        <meta content="" name="author">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- App Icons -->
        <!-- <link rel="shortcut icon" href="assets/images/favicon.ico"> -->

        @section('css')
            
        @show

        @section('style')
            
        @show
    </head>
    <body>
        @section('sidebar')
            
        @show

        @yield('navigation')
        
        @section('content')
            
        @show

        

        @section('footer')
            
        @show

        @section('js')
        
        @show

        @yield('scripts')

    </body>
</html>