<x-app-layout>
    <div class="row">
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0" id="newbackgroundpresented">N/D</h3>
                    <p class="text-success ml-2 mb-0 font-weight-medium" id="newbackgroundpresentedperc">N/D</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-success" id="newbackgroundpresentedpercbox">
                    <span class="mdi mdi-arrow-top-right icon-item" id="newbackgroundpresentedpercarrow"></span>
                  </div>
                </div>
              </div>
              <h6 class="text-muted font-weight-normal">Nuovi background</h6>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0" id="whitelist_users_count">N/D</h3>
                    <p class="text-success ml-2 mb-0 font-weight-medium" id="newwhitelistedperc">N/D</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-success">
                    <span class="mdi mdi-arrow-top-right icon-item"></span>
                  </div>
                </div>
              </div>
              <h6 class="text-muted font-weight-normal">Utenti Whitelistati</h6>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-9">
                  <div class="d-flex align-items-center align-self-start">
                    <h3 class="mb-0" id="backgrounddenied">N/D</h3>
                    <p class="text-danger ml-2 mb-0 font-weight-medium" id="backgrounddeniedperc">N/D</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-danger" id="backgrounddeniedpresentedpercbox">
                    <span class="mdi mdi-arrow-bottom-left icon-item"  id="backgrounddeniedpresentedpercarrow"></span>
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
                    <h3 class="mb-0" id="whitelist_denied_users_count">N/D</h3>
                    <p class="text-danger ml-2 mb-0 font-weight-medium" id="whitelistdeniedperc">N/D</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="icon icon-box-danger ">
                    <span class="mdi mdi-arrow-top-right icon-item"></span>
                  </div>
                </div>
              </div>
              <h6 class="text-muted font-weight-normal">Whitelist rimandate</h6>
            </div>
          </div>
        </div>
    </div>
    <div class="row">
      <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <h4 class="card-title">Utenza media</h4>
            <canvas id="lineChart" style="height: 404px; display: block; width: 808px;" width="1616" height="808" class="chartjs-render-monitor"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <h4 class="card-title">Numero ingressi</h4>
            <canvas id="barChart" style="height: 404px; display: block; width: 808px;" width="1616" height="808" class="chartjs-render-monitor"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-sm-4 grid-margin">
          <div class="card">
            <div class="card-body">
              <h5>Background letti</h5>
              <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                  <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0" id="backgroundreaded">N/D</h2>
                  </div>
                  <h6 class="text-muted font-weight-normal" id="backgroundreadedperc">N/D</h6>
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
              <h5>Background rifiutati</h5>
              <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                  <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0" id="current_month_denied_count">N/D</h2>
                  </div>
                  <h6 class="text-muted font-weight-normal" id="percentage_change_denied_count">N/D</h6>
                </div>
                <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                  <i class="icon-lg mdi mdi-close text-danger ml-auto"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4 grid-margin">
          <div class="card">
            <div class="card-body">
              <h5>Background approvati</h5>
              <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                  <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0" id="current_month_approved_count">N/D</h2>
                  </div>
                  <h6 class="text-muted font-weight-normal" id="percentage_change_approved_count">N/D</h6>
                </div>
                <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                  <i class="icon-lg mdi mdi-check text-success ml-auto"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <form>
        @csrf
      </form>
</x-app-layout>
