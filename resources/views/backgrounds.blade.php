<x-app-layout>
      <!-- table -->
      <div class="card">
        <div class="card-body">
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
            @php
              if(Auth::user()->grade != "admin" && Auth::user()->grade != "superadmin")
                $buttons = "
                  <button id=\"openbtn\" onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
              ";
              else $buttons = "
                <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
                <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
                <!--<button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>-->
                <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
                <button id=\"openbtn\" onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
              ";
            @endphp
            <table class="table" id="SearchableTable">
              <thead>
                <tr class="text-center">
                  <th style="width: 20%;">Discord_ID</th>
                  <th style="width: 20%;">Generalità</th>
                  <th style="width: 20%;">Link</th>
                  <th style="width: 20%;">Status</th>
                  <th style="width: 20%;">Data</th>
                  <th style="width: 20%;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($backgrounds as $background)
                  @php
                    if(Illuminate\Support\Str::startsWith($background->link, 'pdf')) $link = route('background.view', ['filename' => $background->link]);
                    else $link = $background->link;
                  @endphp
                  <tr class="text-center" data-backgroundid="{{ $background->id }}">
                    <td style="width: 20%;">{{ $background->discord_id }}</td>
                    <td style="width: 20%;">{{ $background->generality }}</td>
                    <td style="width: 20%"><a href="{{ $link }}" target="_blank"><code>Link</code></td>
                    <td style="width: 20%;"><label class="badge badge-{{ $background->type }}" style="width: 40%">{{ $background->type }}</label></td>
                    <td style="width: 20%;">{{ $background->created_at }}</label></td>
                    <td style="width: 20%;">
                      @php echo($buttons) @endphp
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="row" style="padding-top: 80px;">
                <!-- Sezione informazioni -->
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                        Visualizzando da {{ $backgrounds->firstItem() }} a {{ $backgrounds->lastItem() }} di {{ $backgrounds->total() }} voci
                    </div>
                </div>

                <!-- Sezione paginazione -->
                <div class="col-sm-12 col-md-7 d-flex justify-content-center justify-content-md-end">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        <ul class="pagination">
                            {{-- Pulsante "Precedente" --}}
                            @if ($backgrounds->onFirstPage())
                                <li class="paginate_button page-item previous disabled" id="example2_previous">
                                    <a href="#" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">Prev</a>
                                </li>
                            @else
                                <li class="paginate_button page-item previous" id="example2_previous">
                                    <a href="{{ $backgrounds->previousPageUrl() }}" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">Prev</a>
                                </li>
                            @endif

                            {{-- Numeri di pagina --}}
                            @foreach ($backgrounds->getUrlRange(1, $backgrounds->lastPage()) as $page => $url)
                                @if ($page == $backgrounds->currentPage())
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
                            @if ($backgrounds->hasMorePages())
                                <li class="paginate_button page-item next" id="example2_next">
                                    <a href="{{ $backgrounds->nextPageUrl() }}" aria-controls="example2" data-dt-idx="{{ $backgrounds->lastPage() + 1 }}" tabindex="0" class="page-link">Next</a>
                                </li>
                            @else
                                <li class="paginate_button page-item next disabled" id="example2_next">
                                    <a href="#" aria-controls="example2" data-dt-idx="{{ $backgrounds->lastPage() + 1 }}" tabindex="0" class="page-link">Next</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
      </div>
</x-app-layout>