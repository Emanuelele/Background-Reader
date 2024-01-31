const allButtons = `
    <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
    <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
    <button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>
    <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
    <button onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
`;

const editButtons = (originalData) => {
    return `
        <button type="button" class="btn btn-success btn-rounded btn-icon" onclick="save(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">save</i></button>
        <button type="button" class="btn btn-warning btn-rounded btn-icon" onclick="undo(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">undo</i></button>
    `;
}

const finishButtons = `
    <button onclick=\"window.location.href='${dashboardUrl}'\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">undo</i></button>
    <button onclick=\"window.location.href='${backgroundReadUrl}'\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">skip_next</i></button>
`;

const dashboardButton = `
    <button onclick=\"window.location.href='${dashboardUrl}'\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">undo</i></button>
`;

function request(url, params, method) {
    return new Promise((resolve, reject) => {
        if(params){
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
        } else {
            fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
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
        }
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
    cell.innerHTML = editButtons(originalData);
    for (let i = 0; i < row.cells.length - 1; i++) row.cells[i].innerHTML = `<input type="text" class="form-control" value="${originalData[i]}">`;
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
    const saveResponse = await request(backgroundDownloadUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, result: newStatus }, "POST");
    if(saveResponse.success) {
        const updateResponse =  await request(backgroundUpdateUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, discord_id: data[0], generality: data[1], link: data[2], type: newStatus, note: note }, "PATCH");
        if(updateResponse.success) cells[3].innerHTML = '<label class="badge badge-'+newStatus+'"style="width: 40%">'+newStatus+'</label>';
        const resultResponse = await request(backgroundResultUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, result: newStatus }, "POST");
        if(resultResponse.success){
            updatestats(element);
            setElementHtml('buttonsgroup', finishButtons);
        }
    }
}

const updatestats = async (element) => {
    const response = await request(backgroundInfoUrl, { background_id:  element.parentNode.parentNode.dataset.backgroundid}, "POST");
    setElementText('bgcount_presentati', (response.success && response.data.new) ? response.data.new : "0");
    setElementText('bgcount_approvati', (response.success && response.data.approved) ? response.data.approved : "0");
    setElementText('bgcount_rifiutati', (response.success && response.data.denied) ? response.data.denied : "0");
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

const setElementText = (elementId, newText) => {
    document.getElementById(elementId).innerText = newText;
}

const setElementClass = (elementId, newClass) => {
    document.getElementById(elementId).className = newClass;
}

const setElementHtml = (elementId, newHtml) => {
    document.getElementById(elementId).innerHTML = newHtml;
}

const info = (element) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = backgroundInfoUrl;
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'background_id';
    input.value = element.parentNode.parentNode.dataset.backgroundid;

    const csrfTokenInput = document.createElement('input');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    csrfTokenInput.type = 'hidden';
    csrfTokenInput.name = '_token';
    csrfTokenInput.value = token;

    form.appendChild(input);
    form.appendChild(csrfTokenInput);
    document.body.appendChild(form);
    
    form.submit();
    /*const form = document.createElement('form');
    form.method = 'POST';
    form.action = "http://localhost:8000/api/verifybackground";
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'google_doc_link';
    input.value = "https://docs.google.com/document/d/1KAAHjuReHPdPNEYgvnFqHxptpyseVgZNiaqAXMGsOlk/edit#heading=h.nawpjre40we5";

    const input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'discord_id';
    input2.value = "651360942440972288";

    form.appendChild(input);
    form.appendChild(input2);
    document.body.appendChild(form);
    form.submit();*/
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

document.addEventListener('DOMContentLoaded', async function () {
    if(window.location.href == dashboardUrl) {
        const response = await request(backgroundDashboardStatsUrl, null, "GET");
        if(response.success) {
            setElementText("newbackgroundpresented", response.data.new_backgrounds_count);
            setElementText("newbackgroundpresentedperc", response.data.percentage_change_new_backgrounds);
            if(response.data.percentage_change_new_backgrounds.includes("-")) {
                setElementClass("newbackgroundpresentedperc", "text-danger ml-2 mb-0 font-weight-medium");
                setElementClass("newbackgroundpresentedpercbox", "icon icon-box-danger");
                setElementClass("newbackgroundpresentedpercarrow", "mdi mdi-arrow-bottom-left icon-item");
            } else {
                setElementClass("newbackgroundpresentedperc", "text-success ml-2 mb-0 font-weight-medium");
                setElementClass("newbackgroundpresentedpercbox", "icon icon-box-success");
                setElementClass("newbackgroundpresentedpercarrow", "mdi mdi-arrow-top-right icon-item");
            }
            setElementText("backgrounddenied", response.data.denied_backgrounds_count);
            setElementText("backgrounddeniedperc", response.data.percentage_change_denied_backgrounds);
            if(response.data.percentage_change_denied_backgrounds.includes("+")) {
                setElementClass("backgrounddeniedperc", "text-danger ml-2 mb-0 font-weight-medium");
                setElementClass("backgrounddeniedpresentedpercbox", "icon icon-box-danger");
                setElementClass("backgrounddeniedpresentedpercarrow", "mdi mdi-arrow-bottom-left icon-item");
            } else {
                setElementClass("backgrounddeniedperc", "text-success ml-2 mb-0 font-weight-medium");
                setElementClass("backgrounddeniedpresentedpercbox", "icon icon-box-success");
                setElementClass("backgrounddeniedpresentedpercarrow", "mdi mdi-arrow-top-right icon-item");
            }
            setElementText("backgroundreaded", response.data.current_month_read_count);
            setElementText("backgroundreadedperc", response.data.percentage_change_read_count);
        }
    }
});