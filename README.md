<p align="center"><a href="https://busipage.it" target="_blank"><img src="	https://instagram.ffco3-1.fna.fbcdn.net/v/t51.2885…9C8sPhtgA3Fm4OMuZGUMSA&oe=65BF9EC3&_nc_sid=8b3546" width="400" alt="Busipage Logo"></a></p>

---

## Chi siamo

Sviluppiamo strumenti, utili per aumentare la visibilità online, aumentare le vendite, gestire leads, e far risparmiare tempo prezioso a chi possiede un business. Con anni di esperienza nella creazione di soluzioni personalizzate per i nostri clienti, abbiamo acquisito una vasta conoscenza e competenza in molti settori digitali. Nello specifico ci occupiamo di sviluppare:

- Landing pages
- Analisi seo
- Ecommerce
- Web-App gestionali
- Web-App contenuti
- Database
- Gaming servers
- Community Discord

Tutte le soluzioni e i recapiti sono disponibili su [busipage.it](https://busipage.it)

---

## Funzionalità della Web-App
Questa applicazione offre un'interfaccia intuitiva e completa per semplificare il processo di invio, lettura e approvazione/rifiuto dei background dei personaggi presentati dagli utenti. Più nello specifico:

- **Invio dei background tramite bot Discord**: L'utente che intende inviare un background allo staff dovà compilare due semplici campi: Generalità del personaggio e link al documento google dove è stato scritto il background.

- **Verifica automatica da parte della Rest-Api**: La Web-App dispone di una Rest-Api che provvederà autonomamente a controllare se il documento google presentato è accessibile e se l'utente rispetta i parametri prestabiliti per poter inviare un background.

- **Salvataggio del background**: Se l'utente e il background soddisfano i requisiti sarà avviata la fase di registrazione nel database. Se l'utente che invia il background è whitelistato avrà una priorità sugli altri background.

- **Autenticazione tramite OAuth2 API Discord**: Il sitema di autenticazione per gli staffer è efficace e sicuro. Al momento del login l'utente verrà reindirizzato alla pagina di autenticazione ufficiale di Discord, una volta autenticato sarà reindirizzato nella dashboard della Web-App.

- **Lettura di un background**: Al momento della lettura di un background, verrà presentato allo staffer il più vecchio background con status "NEW" tenendo coda della priorità di chi è già whitelistato. Sarà possibile accedere direttamente dalla pagina alle statistiche personali dell'utente, al background presentato e ad eventuali background presentati in passato.

- **Approvazione/Rifiuto di un background**: Al momento dell'approvazione/rifiuto di un background la Web-App provvederà autonomamente a: 
    1) Scaricare in formato pdf il background tramite API Google. 
    2) Mandare nell'apposito canale Discord l'esito della valutazione taggando utente e staffer. 
    3) Aggiornare automaticamente i ruoli dell'utente in base all'esito della valutazione.

- **Gestione dei background**: E' prevista un'apposita pagina per la gestione dei background dove è possibile: 
    1) Modificare i dati del background. 
    2) Accedere direttamente alla pagina di valuazione 
    3) Eliminare un background.

NB: I permessi di accesso e modifica degli staffer sono gestiti tramite bot Discord.

---

## Specifiche sulle tipologie di dati


Lista status di un background:

| Valore | Intestazione |
| -------------- | -------------- |
| new     |Nuovo     |
| approved     | Approvato     |
| denied     | Rifiutato     |
| perma     | PermaDeath o PermaJail     |
| other     | Wipati     |

Lista permessi staffer:

| Valore | Intestazione |
| -------------- | -------------- |
| staff     |Permesso per valutare i background     |
| admin     | Permesso per modificare i background     |
| superadmin     | Permesso per registrare altri staffer     |


---

## Installazione

1. Clona questo repository sul tuo computer o scaricalo come file ZIP.

2. Apri il terminale nella cartella del progetto e installa le dipendenze utilizzando il comando:

   ```bash
   composer install
   ```

3. Copia il file `.env.example` e rinominalo in `.env`. Assicurati di configurare correttamente le variabili d'ambiente nel file `.env`, come la connessione al database.

4. Configura il file `Discord.php` con i dati appropriati (contiene i dati del server discord con cui la Web-App interagirà).

5. Genera una chiave di applicazione univoca eseguendo il comando:

   ```bash
   php artisan key:generate
   ```

6. Esegui le migrazioni del database con il comando:

   ```bash
   php artisan migrate
   ```

7. Avvia il server di sviluppo locale eseguendo il comando:

   ```bash
   php artisan serve
   ```

8. Visita il tuo sito all'indirizzo `http://localhost:8000` nel tuo browser.

---

## Contatti

Per ulteriori informazioni o supporto, contatta il nostro team di supporto all'indirizzo support@busipage.it

*Sito web: [www.busipage.it](https://busipage.it)*
