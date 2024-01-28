<x-app-layout>
      <!-- table -->
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"> Backgrounds</h4>
          <br>
          </p>
          <div class="table-responsive">
            @php /*To-Finish*/
              $buttons = "
                <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
                <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
                <button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>
                <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
                <button id=\"openbtn\" onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
            ";
            @endphp
            <table class="table" id="SearchableTable">
              <thead>
                <tr>
                  <th style="width: 20%; padding-left: 3%">Discord_ID</th>
                  <th style="width: 15%;">Generalità</th>
                  <th style="width: 10%; padding-left: 1.5%">Link</th>
                  <th style="width: 10%; padding-left: 3%">Status</th>
                  <th style="width: 20%; padding-left: 10%">Data</th>
                  <th style="width: 10%; padding-left: 6.5%">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($backgrounds as $background)
                <tr data-backgroundid="{{ $background->id }}">
                  <td class="text-left">{{ $background->discord_id }}</td>
                  <td>{{ $background->generality }}</td>
                  <td> {{ $background->link }}</td>
                  <td><label class="badge badge-{{ $background->type }}" style="width: 100%">{{ $background->type }}</label></td>
                  <td style="padding-left: 7.5%">{{ $background->created_at }}</label></td>
                  <td class="text-right">
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
      <div class="card">
        <div id="moreinfo" class="moreinfo">
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
    </div>
</x-app-layout>
