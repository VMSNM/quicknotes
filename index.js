let color = document.getElementById('color');
let createBtn = document.getElementById('createBtn');
let list = document.getElementById('list');
let notesText = [{}];
let notes = [];
let saveBtn = document.getElementById('save-btn');
let loadBtn = document.getElementById('load-btn');
let noteIDDB;

let modal = document.getElementById('modal');
let modalOverlay = document.getElementById('modal-overlay');
let modalClose = document.getElementById('modal-close');
let modalContent = document.getElementById('modal-content');
let modalNoteID = null;
let modalNoteParent = null;

let toast = document.getElementById('toast');
let toastContent = document.getElementById('toast-content');

let menuBtn = document.getElementById('menu-btn');
let menuItems = document.getElementById('mobile-menu-items');

const rePaintNotes = () => {
    list.innerHTML = ``;
    notesToPaint = notes.slice(0);
    notesToPaint.sort((a,b) => (a.id > b.id) ? -1 : (a.id < b.id) ? 1 : 0).map((note, idx) => createNote(note.id, note.color, note.noteContent, note.x, note.y, idx));
}

// Function to create a note component
function createNote(id, color, noteContent, x, y, idx) {
    let newNote = document.createElement('div');
    newNote.classList.add('note');
    newNote.innerHTML = `
        <i class="ri-delete-bin-2-fill close" title="Delete note"></i>
        <textarea class="noteText" placeholder="Write content..." rows="10" value="">${noteContent}</textarea>`;
    
    newNote.style.borderColor = color;
    newNote.style.left = x + 'px';
    newNote.style.top = y + 'px';
    newNote.style.zIndex = 997 - idx;

    newNote.id = id;
    list.appendChild(newNote);
    newNote.addEventListener('change', (event) => updateNote(event.target.parentNode.id, event.target.value));
}

// Function to update notes on frontend
const updateNote = (noteID, updatedContent) => {
    // preserve data
    let findNote = notes.find(note => note.id == noteID);
    if (!findNote) return;
        
    findNote.noteContent = updatedContent;
    localStorage.setItem('saved-notes', JSON.stringify(notes));
    // end preserve data
}

// Function to prepare fetched components
function loadNoteFromDashboard(noteData) {
    notes = JSON.parse(noteData.note);
    localStorage.setItem('saved-notes', JSON.stringify(notes));
    noteIDDB = noteData.id; // keep the id from database for future save

    rePaintNotes();
}

// Function to create new note
createBtn.onclick = () => {
    let noteID = notes.length === 0 ? 1 : parseInt(notes[notes.length - 1].id) + 1;
    /* createNote(noteID, color.value, '', 90, 130); */
    notes.push({
        id: noteID,
        color: color.value,
        x: 90,
        y: 130,
        noteContent: ''
    });
    localStorage.setItem('saved-notes', JSON.stringify(notes));
    rePaintNotes();
}

// Function to send the updated note to the server
function saveNotesToDatabase() {
    let updatedNote = localStorage.getItem('saved-notes');
    fetch("update-notes.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "id=" + encodeURIComponent(noteIDDB) + "&note=" + encodeURIComponent(updatedNote)
    })
    .then(response => response.text())
    .then(data => {
        console.log("Update Response:", data);
        let message = "Notes saved successfully!";
        showToast(message, 'success');
    })
    .catch(error => showToast('Some error occur. Try again.', 'error'));
}

// Function to send the updated note to the server
function downloadToLocalFile() {
    let filename = "quick-notes.xlsx";

    // Filter data to only include 'id' and 'noteContent' columns
    const filteredNotes = notes.map(({ id, noteContent }) => ({ id, noteContent }));

    // Convert filtered data to a worksheet
    let worksheet = XLSX.utils.json_to_sheet(filteredNotes);

    // Create a new workbook and append the worksheet
    let workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Notes");

    // Save the file
    XLSX.writeFile(workbook, filename);
}

// Function to save as Picture
function downloadAsPicture() {
    let element = document.body; // Capture the whole page
    // Or use: document.getElementById("notesContainer"); for specific area

    html2canvas(element).then(canvas => {
        let link = document.createElement("a");
        link.href = canvas.toDataURL("image/png");
        link.download = "quick-notes.png";
        link.click();
    });
}

// EVENT LISTENERS
// Delete note button
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('close')) {
        // preserve data
        modalNoteID = notes.findIndex(note => note.id == event.target.parentNode.id);

        if (modalNoteID > -1) {
            modalNoteParent = event.target.parentNode;
            openModal('delete-note');
        }
        // end preserve data
    } 
})

let cursor = { x: null, y: null }

let note = { dom: null, x: null, y: null }

document.addEventListener('mousedown', (event) => {
    if (event.target.classList.contains('note')) {
        cursor = {
            x: event.clientX,
            y: event.clientY
        }
        note = {
            dom: event.target,
            x: event.target.getBoundingClientRect().left,
            y: event.target.getBoundingClientRect().top,
        }
    }
})

document.addEventListener('mousemove', (event) => {
    if (note.dom == null) return;
    let currentCursor = {
        x: event.clientX,
        y: event.clientY
    }
    let distance = {
        x: currentCursor.x - cursor.x,
        y: currentCursor.y - cursor.y,
    }
    note.dom.style.left = (note.x + distance.x) + 'px';
    note.dom.style.top = (note.y + distance.y) + 'px';
    note.dom.style.cursor = 'grab';
})

document.addEventListener('mouseup', (event) => { 
    if (note.dom == null) return;
    note.dom.style.cursor = 'auto';
    note.dom = null;

    // preserve data
    let findNote = notes.find(note => note.id == event.target.id);
    if (findNote) {
        findNote.x = event.clientX;
        findNote.y = event.clientY;

        localStorage.setItem('saved-notes', JSON.stringify(notes));
    }
    // end preserve data
});
// END EVENT LISTENERS

// Modal
const openModal = (action) => {
    if (action === 'delete-note') {
        modalContent.innerHTML = `
            <i class="ri-close-circle-line modal-close" id="modal-close" onclick="closeModal();"></i>
            <p>Sure you want to delete this note?</p>
            <div class="modal-btns">
                <button class="modal-btn modal-btn-confirm" id="modal-btn-confirm" onclick="deleteNote();">Confirm</button>
                <button class="modal-btn modal-btn-cancel" id="modal-btn-cancel" onclick="closeModal();">Cancel</button>
            </div>
        `
    }
    else if (action === 'logout') {
        modalContent.innerHTML = `
            <i class="ri-close-circle-line modal-close" id="modal-close" onclick="closeModal();"></i>
            <div>
                <p style="margin-bottom:5px">Make sure to save your notes before leaving.</p>
                <p>Confirm logout?</p>
            </div>
            <div class="modal-btns">
                <button class="modal-btn modal-btn-confirm" id="modal-btn-confirm" onclick="logout();">Confirm</button>
                <button class="modal-btn modal-btn-cancel" id="modal-btn-cancel" onclick="closeModal();">Cancel</button>
            </div>
        `
    }
    
    modal.classList.add('active');
} 
const closeModal = () => {
    modalContent.innerHTML = ``;
    modal.classList.remove('active');
}

modalOverlay.onclick = () => closeModal();

const deleteNote = () => {
    notes.splice(modalNoteID, 1);
    localStorage.setItem('saved-notes', JSON.stringify(notes));
    modalNoteParent.remove();
    closeModal();
}

const logout = () => {
    // similar behavior as an HTTP redirect
    window.location.replace("logout.php");
    closeModal();
}

// Mobile Menu
menuBtn.onclick = () => menuItems.classList.toggle('active');

// Toast
const showToast = (message, status) => {
    toast.classList.add('active');

    let statusClass = 'toast-content-' + status;
    toastContent.classList.add(statusClass);
    toastContent.innerHTML = `<p>${message}</p>`

    setTimeout(() => {
        toast.classList.remove('active');
    }, 4000);
}