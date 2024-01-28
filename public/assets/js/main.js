function request(url, params, method) {
    return new Promise((resolve, reject) => {
        fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify(params)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success){ 
                showToast(data.success);
                resolve(true);
            } else showToast(data.error, true);
            resolve(false);
        })
        .catch(error => {
            console.error(error);
            reject(error);
        });
    });
}

const cancel = (element) => {
    if (confirm("Sei sicuro di voler procedere?")) {
        var elementId = element.parentNode.parentNode.dataset.backgroundid;
        var url = backgroundDeleteUrl;
        var params = { background_id: elementId };
        var method = "DELETE";
        var response = request(url, params, method);
        if(response) element.parentNode.parentNode.remove();
    }
}

const edit = (element) => {
    var cell = element.closest('td');
    var row = cell.closest('tr');
    var originalData = [];
    for (var i = 0; i < row.cells.length; i++) originalData.push(row.cells[i].innerText);
    cell.innerHTML = `
        <button type="button" class="btn btn-success btn-rounded btn-icon" onclick="save(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">save</i></button>
        <button type="button" class="btn btn-warning btn-rounded btn-icon" onclick="undo(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">undo</i></button>
    `;
    for (var i = 0; i < row.cells.length -1; i++) row.cells[i].innerHTML = `<input type="text" class="form-control" value="${originalData[i]}">`;
}

const undo = (element, originalData) => {
    var row = element.closest('tr');
    var cells = row.cells;
    cells[cells.length - 1].innerHTML = `
        <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
        <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
        <button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>
        <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
        <button onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
    `;
    for (var i = 0; i < cells.length - 1; i++) if(i == 3) cells[i].innerHTML = '<label class="badge badge-'+originalData[i]+'"style="width: 100%">'+originalData[i]+'</label>'; else cells[i].innerText = originalData[i];
}

const save = (element, originalData) => {
    var row = element.closest('tr');
    var cells = row.cells;
    var newData = [];
    for (var i = 0; i < cells.length - 1; i++) newData.push(cells[i].querySelector('input').value);
    var elementId = element.parentNode.parentNode.dataset.backgroundid;
    var url = backgroundUpdateUrl;
    var method = "PATCH";
    var params = { background_id: elementId, discord_id: newData[0], generality: newData[1], link: newData[2], type: newData[3] };
    var response = request(url, params, method);
    if(response){
        cells[cells.length - 1].innerHTML = `
            <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
            <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
            <button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>
            <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
            <button onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
        `;
        for (var i = 0; i < cells.length - 1; i++) if(i == 3) cells[i].innerHTML = '<label class="badge badge-'+newData[i]+'"style="width: 100%">'+newData[i]+'</label>'; else cells[i].innerText = newData[i];
    }
}

const updatestatus = (element, newStatus) => {
    var row = element.closest('tr');
    var cells = row.cells;
    var data = [];
    for (var i = 0; i < cells.length - 1; i++) data.push(row.cells[i].innerText);
    var elementId = element.parentNode.parentNode.dataset.backgroundid;
    var url = backgroundUpdateUrl;
    var method = "PATCH";
    var params = { background_id: elementId, discord_id: data[0], generality: data[1], link: data[2], type: newStatus };
    var response = request(url, params, method);
    if(response) cells[3].innerHTML = '<label class="badge badge-'+newStatus+'"style="width: 100%">'+newStatus+'</label>';
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchbar');
    if(document.getElementById('SearchableTable')){
        const table = document.getElementById('SearchableTable').getElementsByTagName('tbody')[0];
        searchInput.addEventListener('input', function () {
            const searchValue = searchInput.value.toLowerCase();
            for (const row of table.rows) {
                let matchFound = false;
                for (const cell of row.cells) {
                    const cellText = cell.innerText.toLowerCase();
                    if (cellText.includes(searchValue)) {
                        matchFound = true;
                        break;
                    }
                }
                row.style.display = matchFound ? '' : 'none';
            }
        });
    }
});

const info = async (element) => {
    document.getElementById('discord_username').innerText = "";
    document.getElementById('discord_globalname').innerText = "";
    document.getElementById('bgcount_presentati').innerText = "";
    document.getElementById('bgcount_approvati').innerText = "";
    document.getElementById('bgcount_rifiutati').innerText = "";
    var moreinfo = document.getElementById('moreinfo');
    moreinfo.style.display = 'block';
    var closebtn = document.getElementById('closebtn');
    closebtn.addEventListener('click', function() {
        moreinfo.style.display = 'none';
    });
    var row = element.closest('tr');
    var discord_id = row.cells[0].innerText;
    const url = backgroundInfoUrl+"/"+discord_id;
    var response = await fetch(url, {
        method: "GET",
        headers: {'content-Type': 'application/json'}
    });
    response = await response.json();
    document.getElementById('discord_username').innerText = response.data.username;
    document.getElementById('discord_globalname').innerText = response.data.global_name;
    document.getElementById('bgcount_presentati').innerText = response.data.new;
    document.getElementById('bgcount_approvati').innerText = response.data.approved;
    document.getElementById('bgcount_rifiutati').innerText = response.data.denied;
}

showToast = (message, error) => {
    Toastify({
        text: message,
        close: true,
        style: {
            background: `linear-gradient(to right, rgb(${error ? "255, 95, 109" : "34, 139, 34"}), rgb(${error ? "189, 89, 17" : "122, 207, 122"}))`
        }
    }).showToast();
}