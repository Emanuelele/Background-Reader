<x-app-layout>
    <div class="row">
        <div class="col-sm-4 grid-margin">
          <div class="card">
            <div class="card-body">
              <h5>Nickname Discord</h5>
              <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                  <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0">{{ $additionalInfo['username'] }}</h2>
                  </div>
                </div>
                <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                  <i class="icon-lg mdi mdi-account-card-details text-primary ml-auto"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4 grid-margin">
          <div class="card">
            <div class="card-body">
              <h5>Global Name Discord</h5>
              <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                  <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0">{{ $additionalInfo['global_name'] }}</h2>
                  </div>
                </div>
                <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                  <i class="icon-lg mdi mdi-account text-danger ml-auto"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4 grid-margin">
          <div class="card">
            <div class="card-body" style="max-height: 183px;">
              <h5>Discord Avatar</h5> 
                <div style="display: flex; justify-content: space-around;">
                    <img class="rounded-circle" src="{{ $avatarUrl }}">
                </div>
            </div>
          </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0">{{ $additionalInfo['new'] }}</h3>
                    <p class="text-success ml-2 mb-0 font-weight-medium"></p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-success ">
                    <i class="material-icons">done</i>
                  </div>
                </div>
              </div>
              <h6 class="text-muted font-weight-normal">Background presentati</h6>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0">{{ $additionalInfo['denied'] }}</h3>
                    <p class="text-success ml-2 mb-0 font-weight-medium"></p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-danger">
                    <i class="material-icons">close</i>
                  </div>
                </div>
              </div>
              <h6 class="text-muted font-weight-normal">Background rifiutati</h6>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0">{{ $additionalInfo['approved'] }}</h3>
                    <p class="text-danger ml-2 mb-0 font-weight-medium"></p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-success">
                    <i class="material-icons">done</i>
                  </div>
                </div>
              </div>
              <h6 class="text-muted font-weight-normal">Background accettati</h6>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-9">
                    <div class="d-flex align-items-center align-self-start">
                      <h3 class="mb-0">NO</h3> <!-- TO-DO -->
                      <p class="text-success ml-2 mb-0 font-weight-medium"></p>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="icon icon-box-danger">
                      <i class="material-icons">close</i> <!-- TO-DO -->
                    </div>
                  </div>
                </div>
                <h6 class="text-muted font-weight-normal">Is Whitelist</h6>
              </div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 2%;">
        <div class="card-body">
            <h4 class="card-title">background presentato</h4>
            <br>
            <div class="table-responsive">
            @php
                $buttons = "
                <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
                <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
            ";
            @endphp
            <table class="table">
                <thead>
                <tr class="text-center">
                    <th style="width: 20%">Discord_ID</th>
                    <th style="width: 20%">Generalità</th>
                    <th style="width: 20%">Link</th>
                    <th style="width: 20%">Status</th>
                    <th style="width: 20%">Data</th>
                    <th style="width: 20%">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="text-center" data-backgroundid="{{ $background->id }}">
                    <td style="width: 20%">{{ $background->discord_id }}</td>
                    <td style="width: 20%">{{ $background->generality }}</td>
                    <td style="width: 20%"><a href="{{ $background->link }}" target="_blank"><code>Link</code></td>
                    <td style="width: 20%"><label class="badge badge-{{ $background->type }}" style="width: 40%">{{ $background->type }}</label></td>
                    <td style="width: 20%">{{ $background->created_at }}</label></td>
                    <td style="width: 20%">
                    @php echo($buttons) @endphp
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    @if(is_null($backgrounds))
        <div class="card">
            <div class="card-body">
            <h4 class="card-title"> Altri background di {{ $additionalInfo['username'] }} / {{ $additionalInfo['global_name'] }}</h4>
            <br>
            <div class="table-responsive">
                <table class="table" id="SearchableTable">
                <thead>
                    <tr class="text-center">
                        <th style="width: 20%">Discord_ID</th>
                        <th style="width: 20%">Generalità</th>
                        <th style="width: 20%">Link</th>
                        <th style="width: 20%">Status</th>
                        <th style="width: 20%">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($backgrounds as $background)
                    <tr class="text-center" data-backgroundid="{{ $background->id }}">
                        <td style="width: 20%">{{ $background->discord_id }}</td>
                        <td style="width: 20%">{{ $background->generality }}</td>
                        <td style="width: 20%"><a href="{{ $background->link }}" target="_blank"><code>Link</code></td>
                        <td style="width: 20%"><label class="badge badge-{{ $background->type }}" style="width: 40%">{{ $background->type }}</label></td>
                        <td style="width: 20%">{{ $background->created_at }}</label></td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        </div>
    @endif
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
