<div class='mainDetalle bg-dark'>
    {{-- Be like water. --}}
    
    @if(isset($informacionExtraida))
        <div class='containerDetalle'>
            
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

            <h2>Nombre: {{$informacionExtraida->nombre_enemigo}}</h2>

            <p>Descripcion: {{$informacionExtraida->descripcion}}</p>
               
            <p> Debilidades: {{$informacionExtraida->debilidades}}</p>
                
            <p>Daño: {{$informacionExtraida->daño}} ptos</p>
                <p>
                
                        Tipo de daño: {{$informacionExtraida->tipo_daño}} 
                   
                </p>

            @endif
        </div>

        <button wire:click='volver' class='btn btn-info'>Volver a {{$paginaOrigen}}</button>
    @endif

</div>
