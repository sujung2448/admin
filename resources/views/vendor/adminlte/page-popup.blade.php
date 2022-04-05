@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@if($layoutHelper->isLayoutTopnavEnabled())
    @php( $def_container_class = 'container' )
@else
    @php( $def_container_class = 'container-fluid' )
@endif

@section('adminlte_css')
    @toastr_css
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">
        <div class="content px-3 pt-3">
            @isset($slot)
                {{ $slot }}
            @endisset
            @yield('content')
        </div>
    </div>
@stop

@section('adminlte_js')
    @toastr_js
    @toastr_render
    <script src="/js/app.js"></script>

    <script>
        $(function () {
            $('[data-toggle="popover"]').popover()
        })

        $('.popover-dismiss').popover({
            trigger: 'focus'
        })

        function openPopup(url, option = false) {
            if (!option) {
                option = "left=50,top=50,width=800,height=900,scrollbars=1"
            }
            var popup = window.open(url, url, option);
            popup.focus();
        }
        toastr.options = {
            "positionClass": "toast-bottom-right",
        }
    </script>
    @stack('js')
    @yield('js')
@stop
