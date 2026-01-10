<div class='mainDetalle bg-dark'>
    {{-- Be like water. --}}
    
    @if(isset($informacionExtraida))
        <div class='containerDetalle d-flex flex-row mx-2'>
            
            <div class ='informacionDetalleContainer d-flex flex-column'>
                
                                <?php //Si es item, muestro la informacion respectiva al item, en algunos campos de item
                                // compruebo que el campo no este vacio ya que podria haber items por ejemplo que
                                // no posean un efecto o una duracion, y solo tengan una funcion mas informativa ?>
                                @if($isItem)
                                <h2>Nombre: {{$informacionExtraida->nombre}}</h2>

                                <p>Descripcion: {{$informacionExtraida->descripcion}}</p>
                                <p>
                                    @if(isset($informacionExtraida->efecto))
                                    Efecto: {{$informacionExtraida->efecto}}
                                    @endif
                                </p>
                                <p>
                                    @if(isset($informacionExtraida->magnitud))
                                        Magnitud: {{$informacionExtraida->magnitud}} ptos
                                    @endif
                                </p>
                                <p>
                                    @if(isset($informacionExtraida->duracion))
                                        Duracion: {{$informacionExtraida->duracion}} seg
                                    @endif
                                </p>
                @else <?php //En caso de ser un enemigo y no un item ?>

                        


                        <h2>Nombre: {{ $informacionExtraida['name'] }}</h2>


                        

                    @foreach($informacionExtraida as $atributo => $valor) 
                    
                    {{-- Recorro todas las celdas que he recivido del array con la informacion del monstruo actual --}}

                    @if(!empty($valor) && $atributo !='name' && $atributo !='id') {{-- Solo muestro aquellos valores que no sean el id, nombre o un valor vacio --}}

                            {{-- Si el valor no es un array, lo imprimo directamente --}}
                            @if(!is_array($valor))
                                <p><strong>{{ ucfirst($atributo) }}:</strong> {{ $valor }}</p>


                                @continue {{-- Paso a la siguiente iteracion (o elemento) --}}
                            @endif

                            {{-- Si es array --}}
                            <h4>{{ ucfirst($atributo) }}</h4>
                            <ul>
                                @foreach($valor as $elemento)

                                    {{-- Si el elemento no un es array, lo imprimo directamente --}}
                                    @if(!is_array($elemento))
                                        <li>{{ $elemento }}</li>
                                        
                                        
                                        @continue {{-- Paso a la siguiente iteracion (o elemento) --}}

                                    @endif

                                    <li>
                                        <ul>
                                            @foreach($elemento as $clave => $dato)

                                                {{-- Si el dato actual es un array anidado --}}
                                                @if(is_array($dato))
                                                    <li>
                                                        <strong>{{ ucfirst($clave) }}:</strong>
                                                        <ul>
                                                            @foreach($dato as $subDato)
                                                                @if(is_array($subDato))
                                                                    <li>
                                                                        <ul>
                                                                            @foreach($subDato as $subClave => $subValor)
                                                                                @if(!is_array($subValor) && !is_null($subValor))
                                                                                    <li>{{ ucfirst($subClave) }}: {{ $subValor }}</li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </li>

                                                {{-- Si es un valor simple --}}
                                                @elseif(!is_null($dato))
                                                    <li>{{ ucfirst($clave) }}: {{ $dato }}</li>
                                                @endif

                                            @endforeach
                                        </ul>
                                    </li>

                            @endforeach
                        </ul>

                        @endif

                    @endforeach



                        @endif
                @endif

            </div>
        
            <div class='imgDetalle mx-2'>
                <img class='border rounded border-danger' src="{{asset('images/enemy.png')}}" alt="">
            </div>

        
        

        
</div>
    <button wire:click='volver' class='btn btn-info'>Volver a {{$paginaOrigen}}</button>
</div>


