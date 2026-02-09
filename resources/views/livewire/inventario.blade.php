<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div id='inventario' class='bg-dark inventarioContainer'>
    <h1>Inventario</h1>

    @if(count($items) > 0 || isset($items))

    

        @if(isset($aviso))
            <h4>{{$aviso}}</h4>
        @endif

        <h2>Tus objetos:</h2>

        
        @foreach($items as $item)
        <ul>
            <li>
               <b>Nombre: {{$item->nombre}}</b>
            </li>
            <li>
                @if(isset($item->efecto))
                      Efecto: {{$item->efecto}}
                @else
                      Sin Efecto.
                @endif
                
            </li>
            <li>
               Cantidad: {{$item->pivot->cantidad}}
            </li>
            <li><button class='btn btn-warning' value='{{$item->id}}' wire:click='detalles({{$item->id}})' wire:model='idSeleccionado'>Detalle</button></li>
            
            {{-- No puedo usar wire:model en buttons o inputs type submit (botones en general), wire:model
            solo funciona y se enlaza con los atributos del componente cuando uso un input mas convencional
            como inputs text,email,number,select,radio,checbox... 
            En resumen solo son validos en los inputs cuyo valor varia en funcion de lo que introduce el 
            usuario --}}
            
            
        </ul>
        @endforeach
    

    @else
        <p>{{$aviso}}</p>
    @endif
    </div>
    <div class='opcionesBtn'>
    
    @if(!$vistaAdmin)
        <button class='btn btn-success' wire:click='importarInventario'>Importar mi inventario</button>
    @endif
    <button class='btn btn-info' wire:click='volver'>Volver atrás</button>
    </div>
</div>
