/**
 * Generates HTML code for a set of buttons with different actions.
 *
 * @returns {string} HTML code for buttons
 */
const allButtons = `
    <button onclick=\"updatestatus(this, 'approved')\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">done</i></button>
    <button onclick=\"updatestatus(this, 'denied')\" type=\"button\" class=\"btn btn-warning btn-rounded btn-icon\"><i class=\"material-icons\">close</i></button>
    <!--<button onclick=\"edit(this)\" type=\"button\" class=\"btn btn-success btn-rounded btn-icon\"><i class=\"material-icons\">edit</i></button>-->
    <button onclick=\"cancel(this)\" type=\"button\" class=\"btn btn-danger btn-rounded btn-icon\"><i class=\"material-icons\">delete</i></button>
    <button onclick=\"info(this)\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">info</i></button>
`;

/**
 * Generates HTML code for edit buttons based on original data.
 *
 * @param {Array} originalData - Array of original data for the record
 * @returns {string} HTML code for edit buttons
 */
const editButtons = (originalData) => {
    return `
        <button type="button" class="btn btn-success btn-rounded btn-icon" onclick="save(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">save</i></button>
        <button type="button" class="btn btn-warning btn-rounded btn-icon" onclick="undo(this, [${originalData.map(data => `'${data}'`).join(', ')}])"><i class="material-icons">undo</i></button>
    `;
}

/**
 * Generates HTML code for finish buttons.
 *
 * @returns {string} HTML code for finish buttons
 */
const finishButtons = `
    <button onclick=\"window.location.href='${dashboardUrl}'\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">undo</i></button>
    <button onclick=\"window.location.href='${backgroundReadUrl}'\" type=\"button\" class=\"btn btn-primary btn-rounded btn-icon\"><i class=\"material-icons\">skip_next</i></button>
`;

/**
 * Generates HTML code for a dashboard button.
 *
 * @returns {string} HTML code for dashboard button
 */
const dashboardButton = `
    <button onclick=\"window.location.href='${dashboardUrl}'\" type=\"button\" class=\"btn btn-info btn-rounded btn-icon\"><i class=\"material-icons\">undo</i></button>
`;

/**
 * Sends an HTTP request with optional parameters.
 *
 * @param {string} url - The URL for the request
 * @param {Object} params - Optional parameters for the request
 * @param {string} method - HTTP method (GET, POST, PATCH, DELETE)
 * @returns {Promise} A promise that resolves with the response or rejects with an error
 */
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

/**
 * Deletes a background record after confirming with the user.
 *
 * @param {HTMLElement} element - The button element triggering the action
 */
const cancel = async (element) => {
    if (confirm("Sei sicuro di voler procedere?")) {
        const response = await request(backgroundDeleteUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid }, "DELETE");
        if(response.success) element.parentNode.parentNode.remove();
    }
}

/**
 * Switches a record into edit mode, allowing modifications.
 *
 * @param {HTMLElement} element - The button element triggering the action
 */
const edit = (element) => {
    const cell = element.closest('td');
    const row = cell.closest('tr');
    const originalData = [];
    for (let i = 0; i < row.cells.length; i++) if(i == 2) originalData.push(row.cells[i].querySelector('a').getAttribute('href')); else originalData.push(row.cells[i].innerText);
    cell.innerHTML = editButtons(originalData);
    for (let i = 0; i < row.cells.length - 1; i++) row.cells[i].innerHTML = `<input type="text" class="form-control" value="${originalData[i]}">`;
}

/**
 * Reverts the modifications made during edit mode.
 *
 * @param {HTMLElement} element - The button element triggering the action
 * @param {Array} originalData - Original data of the record
 */
const undo = (element, originalData) => {
    const cells = element.closest('tr').cells;
    cells[cells.length - 1].innerHTML = allButtons;
    for (let i = 0; i < cells.length - 1; i++) if(i == 3) cells[i].innerHTML = '<label class="badge badge-'+originalData[i]+'"style="width: 40%">'+originalData[i]+'</label>'; else if(i == 2) cells[i].innerHTML = '<a href="'+ originalData[i] +'" target="_blank"><code>Link</code>'; else cells[i].innerText = originalData[i];
}

/**
 * Saves the modifications made during edit mode.
 *
 * @param {HTMLElement} element - The button element triggering the action
 * @param {Array} originalData - Original data of the record
 */
const save = async (element, originalData) => {
    const cells = element.closest('tr').cells;
    const newData = [];
    for (let i = 0; i < cells.length - 1; i++) newData.push(cells[i].querySelector('input').value);
    const response = await request(backgroundUpdateUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, discord_id: newData[0], generality: newData[1], link: newData[2], type: newData[3] }, "PATCH");
    if(response.success){
        cells[cells.length - 1].innerHTML = allButtons;
        for (let i = 0; i < cells.length - 1; i++) if(i == 3) cells[i].innerHTML = '<label class="badge badge-'+newData[i]+'"style="width: 40%">'+newData[i]+'</label>'; else if(i == 2) cells[i].innerHTML = '<a href="'+ newData[i] +'" target="_blank"><code>Link</code>'; else cells[i].innerText = newData[i];
    } else undo(element, originalData);
}

/**
 * Updates the status of a background record and performs related actions.
 *
 * @param {HTMLElement} element - The button element triggering the action
 * @param {string} newStatus - The new status for the background record
 */
const updatestatus = async (element, newStatus) => {
    const row = element.closest('tr');
    const cells = row.cells;
    const data = [];
    for (let i = 0; i < row.cells.length; i++) if(i == 2) data.push(row.cells[i].querySelector('a').getAttribute('href')); else data.push(row.cells[i].innerText);
    const note = prompt("Inserisci delle note:");
    const updateResponse =  await request(backgroundUpdateUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, discord_id: data[0], generality: data[1], link: data[2], type: newStatus, note: note }, "PATCH");
    if(updateResponse.success) {
        const saveResponse = await request(backgroundDownloadUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, result: newStatus }, "POST");
        if(saveResponse.success) cells[3].innerHTML = '<label class="badge badge-'+newStatus+'"style="width: 40%">'+newStatus+'</label>';
        const resultResponse = await request(backgroundResultUrl, { background_id: element.parentNode.parentNode.dataset.backgroundid, result: newStatus }, "POST");
        if(resultResponse.success){
            updatestats(element);
            setElementHtml('buttonsgroup', finishButtons);
        }
    }
}

/**
 * Updates statistics related to background records.
 *
 * @param {HTMLElement} element - The button element triggering the action
 */
const updatestats = async (element) => {
    const response = await request(backgroundInfoUrl, { background_id:  element.parentNode.parentNode.dataset.backgroundid}, "POST");
    setElementText('bgcount_presentati', (response.success && response.data.new) ? response.data.new : "0");
    setElementText('bgcount_approvati', (response.success && response.data.approved) ? response.data.approved : "0");
    setElementText('bgcount_rifiutati', (response.success && response.data.denied) ? response.data.denied : "0");
}

/**
 * Initializes event listeners after the DOM has loaded.
 * Search in table system.
 */
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

/**
 * Sets the inner text of an HTML element.
 *
 * @param {string} elementId - The ID of the target HTML element
 * @param {string} newText - The new text to set
 */
const setElementText = (elementId, newText) => {
    document.getElementById(elementId).innerText = newText;
}

/**
 * Sets the CSS class of an HTML element.
 *
 * @param {string} elementId - The ID of the target HTML element
 * @param {string} newClass - The new CSS class to set
 */
const setElementClass = (elementId, newClass) => {
    document.getElementById(elementId).className = newClass;
}

/**
 * Sets the inner HTML of an HTML element.
 *
 * @param {string} elementId - The ID of the target HTML element
 * @param {string} newHtml - The new HTML code to set
 */
const setElementHtml = (elementId, newHtml) => {
    document.getElementById(elementId).innerHTML = newHtml;
}

/**
 * redirect to another page to display detailed information about a background record.
 *
 * @param {HTMLElement} element - The button element triggering the action
 */
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
}

/**
 * Displays a toast notification with a message.
 *
 * @param {string} message - The message to display in the toast
 * @param {boolean} error - Indicates if it's an error toast
 */
showToast = (message, error) => {
    Toastify({
        text: message,
        close: true,
        style: {
            background: `linear-gradient(to right, rgb(${error ? "255, 95, 109" : "34, 139, 34"}), rgb(${error ? "189, 89, 17" : "122, 207, 122"}))`
        }
    }).showToast();
}

/**
 * Loads and displays statistics for the dashboard after the DOM has loaded.
 */
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
            setElementText("current_month_denied_count", response.data.current_month_denied_count);
            setElementText("percentage_change_denied_count", response.data.percentage_change_denied_count);
            setElementText("current_month_approved_count", response.data.current_month_approved_count);
            setElementText("percentage_change_approved_count", response.data.percentage_change_approved_count);
            //setElementText("whitelist_users_count", response.data.whitelist_users_count);
            //setElementText("whitelist_denied_users_count", response.data.whitelist_denied_users_count);
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const searchbar = document.getElementById('searchbar');

    if (!searchbar) return; // Se non c'è una barra di ricerca, esci.

    // Funzione per identificare la pagina dei log
    const isLogsPage = window.location.pathname.includes('/logs');
    const isBackgroundsPage = window.location.pathname.includes('/backgrounds');

    if (isLogsPage) {
        searchbar.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') { // Premuto "Invio"
                event.preventDefault();

                // Ottieni il termine di ricerca
                const searchValue = searchbar.value.trim();

                // Ridirigi alla pagina dei log con il termine di ricerca
                const baseUrl = window.location.origin + window.location.pathname;
                const url = new URL(baseUrl);
                url.searchParams.set('search', searchValue);

                window.location.href = url.toString();
            }
        });
    }
    if(isBackgroundsPage){
        searchbar.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();

                // Ottieni il termine di ricerca
                const searchValue = searchbar.value.trim();

                // Ridirigi alla pagina dei log con il termine di ricerca
                const baseUrl = window.location.origin + window.location.pathname;
                const url = new URL(baseUrl);
                url.searchParams.set('search', searchValue);

                window.location.href = url.toString();
            }
        });
    }
});


let isTagsDisplayed = false; // Variabile per tenere traccia dello stato (ID o tag)
let discordTagsMap = {}; // Mappa per tenere traccia dei tag Discord (ID -> tag)
let originalContentMap = {}; // Mappa per memorizzare l'originale ID/Tag

async function replaceDiscordIdsWithTags() {
    const table = document.getElementById('SearchableTable');
    if (!table) {
        console.error('Tabella non trovata');
        return;
    }

    const discordIdRegex = /^\d{17,19}$/;
    const rows = table.querySelectorAll('tr');

    if (!isTagsDisplayed && Object.keys(discordTagsMap).length === 0) {
        const discordIds = [];
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                const text = cell.textContent.trim();
                // Se il testo corrisponde a un Discord ID, aggiungilo all'array
                if (discordIdRegex.test(text)) {
                    discordIds.push(text);
                }
            });
        });

        if (discordIds.length > 0) {
            try {
                // Chiediamo i tag Discord al backend solo se non sono già stati caricati
                discordTagsMap = await fetchDiscordTags(discordIds);
            } catch (error) {
                console.error('Errore durante il recupero dei tag Discord', error);
            }
        }
    }

    const rowsToUpdate = Array.from(rows); // Converti la NodeList in array per poterla modificare

    rowsToUpdate.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            const text = cell.textContent.trim();
            
            if (isTagsDisplayed) {
                // Quando stiamo mostrando i tag, sostituire il tag con l'ID
                const originalId = originalContentMap[text];
                if (originalId) {
                    cell.textContent = originalId; // Ripristina l'ID
                }
            } else {
                // Quando stiamo mostrando gli ID, sostituire l'ID con il tag
                const tag = discordTagsMap[text];
                if (tag) {
                    cell.textContent = tag;
                    originalContentMap[tag] = text; // Memorizza l'ID originale con il tag
                }
            }
        });
    });

    // Alterna lo stato di visualizzazione
    isTagsDisplayed = !isTagsDisplayed;
}

async function fetchDiscordTags(discordIds) {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    try {
        const response = await fetch('/discord/tags', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ discordIds }),
        });

        if (response.ok) {
            const data = await response.json();
            
            // Logga la risposta per capire la struttura dei dati
            console.log('Risposta API:', data);
            
            return data;
        } else {
            console.error(`Errore API: ${response.status} ${response.statusText}`);
            return {};
        }
    } catch (error) {
        console.error('Errore nella richiesta API:', error);
        return {};
    }
}

document.getElementById('showtag').addEventListener('click', replaceDiscordIdsWithTags);
