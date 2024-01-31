<x-app-layout>
      <!-- table -->
      <div class="card">
        <div class="card-body">
          <div style="display:flex; items-align: left;">
            <h4 class="card-title"> Backgrounds</h4>
            <div class="dropdown" style="padding-left:85%">
              <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filtra per
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{ route('background.getall') }}">Rimuovi filtri</a>
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
                <button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>
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
        </div>
      </div>
</x-app-layout>