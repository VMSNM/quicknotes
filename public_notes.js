let color = document.getElementById('color');
let createBtn = document.getElementById('createBtn');
let list = document.getElementById('list');
let notesText = [{}];
let notes = JSON.parse(localStorage.getItem('unsaved-notes')) || [];
let notesToPaint = [];

let modal = document.getElementById('modal');
let modalOverlay = document.getElementById('modal-overlay');
let modalClose = document.getElementById('modal-close');
let modalBtnConfirm = document.getElementById('modal-btn-confirm');
let modalBtnCancel = document.getElementById('modal-btn-cancel');
let modalNoteID = null;
let modalNoteParent = null;

const rePaintNotes = () => {
    list.innerHTML = ``;
    notesToPaint = notes.slice(0);
    notesToPaint.sort((a,b) => (a.id > b.id) ? -1 : (a.id < b.id) ? 1 : 0).map((note, idx) => createNote(note.id, note.color, note.noteContent, note.x, note.y, idx));
}

const createNote = (noteID, noteColor, noteCont, noteX, noteY, idx) => {
    let newNote = document.createElement('div');
    newNote.classList.add('note');
    newNote.innerHTML = `
        <i class="ri-delete-bin-2-fill close" title="Delete note"></i>
        <textarea class="noteText" placeholder="Write content..." rows="10" value="">${noteCont}</textarea>`;
    
    newNote.style.borderColor = noteColor;
    newNote.style.left = noteX + 'px';
    newNote.style.top = noteY + 'px';
    newNote.style.zIndex = 997 - idx;

    newNote.id = noteID;
    list.appendChild(newNote);
    newNote.addEventListener('change', (event) => updateNote(event.target.parentNode.id, event.target.value));
}

/* -------------------- */

if (notes.length > 0) rePaintNotes();

// Function for creating new note
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
    localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    rePaintNotes();
}

const updateNote = (noteID, updatedContent) => {
    // preserve data
    let findNote = notes.find(note => note.id == noteID);
    if (findNote) {
        findNote.noteContent = updatedContent;

        localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    }
    // end preserve data
}

// Function for the close note button
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('close')) {
        // preserve data
        modalNoteID = notes.findIndex(note => note.id == event.target.parentNode.id);

        if (modalNoteID > -1) {
            modalNoteParent = event.target.parentNode;
            toggleModal();
        }
        // end preserve data
    } 
})

// Move notes
let cursor = {
    x: null,
    y: null
}

let note = {
    dom: null,
    x: null,
    y: null
}

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

        localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    }
    // end preserve data
});
// End Move notes

// Modal
const toggleModal = () => modal.classList.toggle('active');

modalBtnConfirm.onclick = () => {
    notes.splice(modalNoteID, 1);
    localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    modalNoteParent.remove();
    toggleModal();
}

modalClose.onclick = () => toggleModal();
modalOverlay.onclick = () => toggleModal();
modalBtnCancel.onclick = () => toggleModal();