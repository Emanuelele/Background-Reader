<x-app-layout>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Logs</h4>
            <div style="display:flex; items-align: left;">
            <h4 class="card-title"> Backgrounds</h4>
            <div class="dropdown" style="padding-left:85%">
              <!--<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filtra per
              </button>-->
              <button class="btn btn-primary" type="button" aria-expanded="false" id="showtag">
                Mostra/Nascondi nomi
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{ route('background.get') }}">Rimuovi filtri</a>
                <a class="dropdown-item" href="{{ route('background.get', ['type' => 'new']) }}">Nuovi</a>
                <a class="dropdown-item" href="{{ route('background.get', ['type' => 'denied']) }}">Rifiutati</a>
                <a class="dropdown-item" href="{{ route('background.get', ['type' => 'approved']) }}">Approvati</a>
                <a class="dropdown-item" href="{{ route('background.get', ['type' => 'playing']) }}">Giocanti</a>
                <a class="dropdown-item" href="{{ route('background.get', ['type' => 'perma']) }}">Permati</a>
                <a class="dropdown-item" href="{{ route('background.get', ['type' => 'other']) }}">Wipati</a>
              </div>
            </div>
          </div>
            <br>
            <div class="table-responsive">
                <table class="table" id="SearchableTable">
                    <thead>
                        <tr class="text-center">
                            @foreach ($columns as $column)
                                @if(!(ucfirst(str_replace('_', ' ', $column)) == 'Metadata'))
                                <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr class="text-center">
                                @foreach ($columns as $column)
                                    @if (array_key_exists($column, $log->getAttributes()))
                                        @if (!is_array($log[$column]))
                                            <td>{{ $log[$column] }}</td>
                                         @endif
                                    @elseif (isset($log->metadata) && array_key_exists($column, $log->metadata))
                                        <td>
                                            @if (is_array($log->metadata[$column]))
                                                {{ json_encode($log->metadata[$column]) }}
                                            @else
                                                {{ $log->metadata[$column] }}
                                            @endif
                                        </td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row" style="padding-top: 80px;">
                <!-- Sezione informazioni -->
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                        Visualizzando da {{ $logs->firstItem() }} a {{ $logs->lastItem() }} di {{ $logs->total() }} voci
                    </div>
                </div>

                <!-- Sezione paginazione -->
                <div class="col-sm-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        <ul class="pagination">
                            {{-- Pulsante "Precedente" --}}
                            @if ($logs->onFirstPage())
                                <li class="paginate_button page-item previous disabled" id="example2_previous">
                                    <a href="#" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">Prev</a>
                                </li>
                            @else
                                <li class="paginate_button page-item previous" id="example2_previous">
                                    <a href="{{ $logs->previousPageUrl() }}" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">Prev</a>
                                </li>
                            @endif

                            {{-- Numeri di pagina --}}
                            @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                                @if ($page == $logs->currentPage())
                                    <li class="paginate_button page-item active">
                                        <a href="#" aria-controls="example2" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>
                                    </li>
                                @else
                                    <li class="paginate_button page-item">
                                        <a href="{{ $url }}" aria-controls="example2" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Pulsante "Successivo" --}}
                            @if ($logs->hasMorePages())
                                <li class="paginate_button page-item next" id="example2_next">
                                    <a href="{{ $logs->nextPageUrl() }}" aria-controls="example2" data-dt-idx="{{ $logs->lastPage() + 1 }}" tabindex="0" class="page-link">Next</a>
                                </li>
                            @else
                                <li class="paginate_button page-item next disabled" id="example2_next">
                                    <a href="#" aria-controls="example2" data-dt-idx="{{ $logs->lastPage() + 1 }}" tabindex="0" class="page-link">Next</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
