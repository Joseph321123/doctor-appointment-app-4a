{{-- Verifica si hay un elemento en breadcrum--}}
@if (count($breadcrumbs))
    {{-- margin bottom o margen inferior--}}
    <nav class="mb-2 block">
    {{-- ordered list--}}
    <ol  class="flex flex-wrap text-slate-700 text-sm">
        @foreach($breadcrumbs as $item)
            <li class="flex items-center">
                @unless($loop->first) {{--para que el primero lo salte para el espacio--}}
                    {{-- el span es un separador--}}
                    <span class="px-2 text-gray-400">/</span>
                @endunless
                {{--Revisar si existe una llave 'href'--}}
                @isset($item['href'])
                        <a href="{{$item['href']}}"
                        class="opacity-60 hover:opacity-100">
                            {{$item['name']}}
                        </a>
                    @else
                    {{$item['name']}}
                @endisset
            </li>
        @endforeach
    </ol>
        {{--el ultimo item aparece resaltado--}}
        @if(count($breadcrumbs)>1)
            <h6 class="font-bold mt-2">
                {{end($breadcrumbs)['name']}}
            </h6>
        @endif
    </nav>
@endif
