const allButtons = `
    <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
    <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
    <button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>
    <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
    <button onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
`;

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
                resolve({ success: true, data: data.data });
            } else showToast(data.error, true);
            resolve({ success: false, data: data.data });
        })
        .catch(error => {
            console.error(error);
            reject(error);
        });
    });
}

const cancel = async (element) => {
    if (confirm("Sei sicuro di voler procedere?")) {
        const response = await request(backgroundDeleteUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid }, "DELETE");
        if(response.success) element.parentNode.parentNode.remove();
    }
}

const edit = (element) => {
    const cell = element.closest('td');
    const row = cell.closest('tr');
    const originalData = [];
    for (let i = 0; i < row.cells.length; i++) originalData.push(row.cells[i].innerText);
    cell.innerHTML = `
        <button type="button" class="btn btn-success btn-rounded btn-icon" onclick="save(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">save</i></button>
        <button type="button" class="btn btn-warning btn-rounded btn-icon" onclick="undo(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">undo</i></button>
    `;
    for (let i = 0; i < row.cells.length -1; i++) row.cells[i].innerHTML = `<input type="text" class="form-control" value="${originalData[i]}">`;
}

const undo = (element, originalData) => {
    const cells = element.closest('tr').cells;
    cells[cells.length - 1].innerHTML = allButtons;
    for (let i = 0; i < cells.length - 1; i++) if(i == 3) cells[i].innerHTML = '<label class="badge badge-'+originalData[i]+'"style="width: 40%">'+originalData[i]+'</label>'; else cells[i].innerText = originalData[i];
}

const save = async (element, originalData) => {
    const cells = element.closest('tr').cells;
    const newData = [];
    for (let i = 0; i < cells.length - 1; i++) newData.push(cells[i].querySelector('input').value);
    const response = await request(backgroundUpdateUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, discord_id: newData[0], generality: newData[1], link: newData[2], type: newData[3] }, "PATCH");
    if(response.success){
        cells[cells.length - 1].innerHTML = allButtons;
        for (let i = 0; i < cells.length - 1; i++) if(i == 3) cells[i].innerHTML = '<label class="badge badge-'+newData[i]+'"style="width: 40%">'+newData[i]+'</label>'; else cells[i].innerText = newData[i];
    } else undo(element, originalData);
}

const updatestatus = async (element, newStatus) => {
    const row = element.closest('tr');
    const cells = row.cells;
    const data = [];
    for (let i = 0; i < cells.length - 1; i++) data.push(row.cells[i].innerText);
    const note = prompt("Inserisci delle note:");
    const updateResponse =  await request(backgroundUpdateUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, discord_id: data[0], generality: data[1], link: data[2], type: newStatus, note: note }, "PATCH");
    if(updateResponse.success) cells[3].innerHTML = '<label class="badge badge-'+newStatus+'"style="width: 40%">'+newStatus+'</label>';
    const resultResponse = await request(backgroundResultUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, result: newStatus }, "POST");
    //if(resultResponse.success) cells[4].innerHTML = ''; /* TO-FINISH: pulsante skip o return dash, aggiornamento stats, correzione tabelle,  */
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

const setElementText = (elementId, text) => {
    document.getElementById(elementId).innerText = text;
}

const info = async (element) => {
    const moreinfo = document.getElementById('moreinfo');
    moreinfo.style.display = 'block';
    
    const response = await request(backgroundInfoUrl, { discord_id: element.closest('tr').cells[0].innerText }, "POST");
    
    setElementText('discord_username', (response.success && response.data.username) ? response.data.username : "null");
    setElementText('discord_globalname', (response.success && response.data.global_name) ? response.data.global_name : "null");
    setElementText('bgcount_presentati', (response.success && response.data.new) ? response.data.new : "0");
    setElementText('bgcount_approvati', (response.success && response.data.approved) ? response.data.approved : "0");
    setElementText('bgcount_rifiutati', (response.success && response.data.denied) ? response.data.denied : "0");

    const closebtn = document.getElementById('closebtn');
    closebtn.addEventListener('click', () => {
        moreinfo.style.display = 'none';
    });
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