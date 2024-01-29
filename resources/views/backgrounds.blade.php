<x-app-layout>
      <!-- table -->
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"> Backgrounds</h4>
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
                  <tr class="text-center" data-backgroundid="{{ $background->id }}">
                    <td style="width: 20%;">{{ $background->discord_id }}</td>
                    <td style="width: 20%;">{{ $background->generality }}</td>
                    <td style="width: 20%"><a href="{{ $background->link }}" target="_blank"><code>Link</code></td>
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
      <!-- more info popup -->
      <div id="moreinfo" class="moreinfo card">
        <i id="closebtn" class="close fa-solid fa-xmark"></i>
        <div class="card-body">
            <h4 class="card-title">Informazioni utente</h4>
            <div class="table-responsive">
            <table class="table">
                <tbody>
                <tr><td>Discord Username</td><td>=></td><td id="discord_username"></td></tr>
                <tr><td>Discord Global Name</td><td>=></td><td id="discord_globalname"></td></tr>
                <tr><td>Background presentati</td><td>=></td><td id="bgcount_presentati"></td></tr>
                <tr><td>Background approvati</td><td>=></td><td id="bgcount_approvati"></td></tr>
                <tr><td>Background rifiutati</td><td>=></td><td id="bgcount_rifiutati"></td></tr>
                </tbody>
            </table>
            </div>
        </div>
      </div>
</x-app-layout>
