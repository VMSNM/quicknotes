let color = document.getElementById('color');
let createBtn = document.getElementById('createBtn');
let list = document.getElementById('list');
let notesText = [{}];
let notes = JSON.parse(localStorage.getItem('unsaved-notes')) || [];
let notesToPaint = [];
// Run once to assign initial zIndex values
notes.forEach((n, idx) => {
    if (!n.zIndex || n.zIndex < 997) {
        n.zIndex = 997 + idx;
    }
});
localStorage.setItem('unsaved-notes', JSON.stringify(notes));

let modal = document.getElementById('modal');
let modalOverlay = document.getElementById('modal-overlay');
let modalClose = document.getElementById('modal-close');
let modalBtnConfirm = document.getElementById('modal-btn-confirm');
let modalBtnCancel = document.getElementById('modal-btn-cancel');
let modalNoteID = null;
let modalNoteParent = null;

const createNote = (noteID, noteColor, noteCont, noteX, noteY, noteZIndex, noteCreatedAt, noteUpdatedAt, noteWidth, noteHeight, fontSize = 16, isBold = false, isItalic = false, noteTextColor = '#eeeeee') => {
    let newNote = document.createElement('div');
    newNote.classList.add('note');
    
    newNote.innerHTML = `
        <div class="note-header" style="background-color: ${noteColor};">
            <div class="note-controls">
                <div class="note-palette">
                    <div class="note-palette-icon">🎨</div>
                    <input type="color" value="${noteColor}" class="note-palette-input" title="Change note color">
                </div>
                <div class="text-palette">
                    <div class="text-palette-icon" style="color:${noteTextColor}"><i class="ri-text"></i></div>
                    <input type="color" value="${noteTextColor}" class="text-palette-input text-color" title="Change text color">
                </div>
                <div class="note-settings">
                    <button class="note-settings-icon" title="Font size, Bold, Italic"><i class="ri-font-size-2"></i></button>
                    <div class="note-settings-dropdown">
                        <div class="note-style-btn font-inc" title="Increase font">A+</div>
                        <div class="note-style-btn font-dec" title="Decrease font">A-</div>
                        <div class="note-style-btn bold-toggle" title="Bold"><b>B</b></div>
                        <div class="note-style-btn italic-toggle" title="Italic" style="font-style:italic">I</div>
                    </div>
                </div>
                <button title="Bring note to Front"><i class="ri-bring-to-front bring-front"></i></button>
                <button title="Send note to Back"><i class="ri-send-to-back send-back"></i></button>
                <!-- Info button to show created and updated dates -->
                <button class="info-btn">i</button>
            </div>
            <i class="ri-close-circle-line close" title="Delete note"></i>
        </div>
        <div class="note-body">
            <textarea class="noteText"
                placeholder="Write content..."
                style="
                    color: ${noteTextColor};
                    font-size: ${fontSize}px;
                    font-weight: ${isBold ? 'bold' : 'normal'};
                    font-style: ${isItalic ? 'italic' : 'normal'};
                ">${noteCont}</textarea>
            <div class="bottom-controls">
                <div class="emoji-trigger" title="Insert emoji">😊</div>
                <div class="resize-handle" title="Resize note">↘️</div>
                <button class="mobile-expand-toggle" title="Expand note">⬇️</button>
            </div>
        </div>
    `;

    // Color input for note background
    let colorInput = newNote.querySelector('.note-palette-input');
    colorInput.addEventListener('input', (e) => {
        let newColor = e.target.value;
        newNote.style.borderColor = newColor;
        let header = newNote.querySelector('.note-header');
        if (header) {
            header.style.backgroundColor = newColor;
            header.classList.add('fade-color');
            setTimeout(() => {
                header.classList.remove('fade-color');
            }, 300);
        }

        let noteIndex = notes.findIndex(n => n.id === noteID);
        if (noteIndex !== -1) {
            notes[noteIndex].color = newColor;
            localStorage.setItem('unsaved-notes', JSON.stringify(notes));
        }
    });

    // Styling controls
    const textarea = newNote.querySelector('.noteText');
    const noteIndex = notes.findIndex(n => n.id === noteID);
    const updateNoteStyle = (key, value) => {
        if (noteIndex !== -1) {
            notes[noteIndex][key] = value;
            notes[noteIndex].updatedAt = new Date().toISOString();
            localStorage.setItem('unsaved-notes', JSON.stringify(notes));
        }
    };

    // Apply text color changes
    const textColorInput = newNote.querySelector('.text-color');
    const textArea = newNote.querySelector('.noteText');
    const textIcon = newNote.querySelector('.text-palette-icon');

    textColorInput.addEventListener('input', (e) => {
        const newColor = e.target.value;
        
        // Apply color to textarea content
        textArea.style.color = newColor;

        // Update icon color
        textIcon.style.color = newColor;

        // Save to note state
        const noteIndex = notes.findIndex(n => n.id === noteID);
        if (noteIndex !== -1) {
            notes[noteIndex].textColor = newColor;
            notes[noteIndex].updatedAt = new Date().toISOString();
            localStorage.setItem('unsaved-notes', JSON.stringify(notes));
        }
    });

    newNote.querySelector('.font-inc').addEventListener('click', () => {
        let newSize = Math.min(parseInt(getComputedStyle(textarea).fontSize) + 2, 32);
        textarea.style.fontSize = newSize + 'px';
        updateNoteStyle('fontSize', newSize);
    });

    newNote.querySelector('.font-dec').addEventListener('click', () => {
        let newSize = Math.max(parseInt(getComputedStyle(textarea).fontSize) - 2, 10);
        textarea.style.fontSize = newSize + 'px';
        updateNoteStyle('fontSize', newSize);
    });

    newNote.querySelector('.bold-toggle').addEventListener('click', () => {
        let isBoldNow = textarea.style.fontWeight !== 'bold';
        textarea.style.fontWeight = isBoldNow ? 'bold' : 'normal';
        updateNoteStyle('isBold', isBoldNow);
    });

    newNote.querySelector('.italic-toggle').addEventListener('click', () => {
        let isItalicNow = textarea.style.fontStyle !== 'italic';
        textarea.style.fontStyle = isItalicNow ? 'italic' : 'normal';
        updateNoteStyle('isItalic', isItalicNow);
    });

    newNote.querySelector('.info-btn').addEventListener('mouseenter', () => {
        const tooltip = document.createElement('div');
        tooltip.classList.add('tooltip');
        tooltip.innerHTML = `
            <strong>Created:</strong> ${new Date(noteCreatedAt).toLocaleString()} <br>
            <strong>Updated:</strong> ${new Date(noteUpdatedAt).toLocaleString()}
        `;
    
        // Position the tooltip near the info button
        const infoButton = newNote.querySelector('.info-btn');
        infoButton.appendChild(tooltip);
    
        // Show the tooltip
        tooltip.style.display = 'block';
    });
    
    newNote.querySelector('.info-btn').addEventListener('mouseleave', () => {
        const tooltip = newNote.querySelector('.tooltip');
        if (tooltip) {
            tooltip.style.display = 'none';
            tooltip.remove();
        }
    });

    const emojiList = [
        '😀', '😁', '😂', '🤣', '😊', '😎', '😍', '😘', '🤔', '😢',
        '😡', '😴', '🤯', '🤗', '😇', '😱', '🥳', '🤩', '💀', '✅',
        '💪', '🙏', '👌', '✌️', '🤞', '🤙', '👀', '🧠', '👋', '👏',
        '🔥', '💥', '✨', '⚡', '🌈', '🌟', '🎉', '🚀', '🛠️', '💡',
        '📌', '📝', '📅', '📎', '📚', '🔒', '🔓', '📢', '❤️', '💬'
    ];

    newNote.querySelector('.emoji-trigger').addEventListener('click', function (e) {
        e.stopPropagation(); // prevent immediate closing by global listener
    
        const existingPicker = newNote.querySelector('.emoji-picker');
        if (existingPicker) {
            existingPicker.remove(); // toggle off
            return;
        }
    
        // Close any other open emoji pickers before opening a new one
        document.querySelectorAll('.emoji-picker').forEach(p => p.remove());
    
        const picker = document.createElement('div');
        picker.className = 'emoji-picker';
    
        emojiList.forEach(emoji => {
            const span = document.createElement('span');
            span.textContent = emoji;
            span.addEventListener('click', () => {
                const textarea = newNote.querySelector('.noteText');
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const value = textarea.value;
    
                textarea.value = value.slice(0, start) + emoji + value.slice(end);
                textarea.focus();
                textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
    
                updateNote(noteID, textarea.value);
                picker.remove(); // hide after selection
            });
            picker.appendChild(span);
        });
    
        newNote.querySelector('.note-body').appendChild(picker);
    });
    
    // Global listener to close picker when clicking outside
    document.addEventListener('click', function (event) {
        document.querySelectorAll('.emoji-picker').forEach(picker => {
            if (!picker.contains(event.target) && !event.target.closest('.emoji-trigger')) {
                picker.remove();
            }
        });
    });

    // Mobile Toggle Expand Note
    const expandToggle = newNote.querySelector('.mobile-expand-toggle');
    /* const noteBody = newNote.querySelector('.note'); */

    let isExpanded = false;

    // Set initial state
    /* newNote.classList.add('collapsed'); */

    expandToggle.addEventListener('click', () => {
        isExpanded = !isExpanded;

        if (isExpanded) {
            /* noteBody.classList.remove('collapsed'); */
            newNote.classList.add('expanded');
            expandToggle.textContent = '⬆️';
            expandToggle.title = 'Minimize note';
        } else {
            newNote.classList.remove('expanded');
            /* noteBody.classList.add('collapsed'); */
            expandToggle.textContent = '⬇️';
            expandToggle.title = 'Expand note';
        }
    });

    newNote.style.left = noteX + 'px';
    newNote.style.top = noteY + 'px';
    newNote.style.width = noteWidth + 'px';
    newNote.style.height = noteHeight + 'px';
    newNote.style.zIndex = noteZIndex;
    newNote.id = noteID;
    list.appendChild(newNote);

    textarea.addEventListener('change', (event) => updateNote(noteID, event.target.value));
};

const rePaintNotes = () => {
    list.innerHTML = ``;
    notesToPaint = notes.slice(0);
    notesToPaint
        .sort((a,b) => (a.zIndex > b.zIndex) ? -1 : 1)
        .forEach((note, idx) => {
            createNote(
                note.id,
                note.color,
                note.noteContent,
                note.x,
                note.y,
                note.zIndex,
                note.createdAt,
                note.updatedAt,
                note.width,
                note.height,
                note.fontSize,
                note.isBold,
                note.isItalic,
                note.textColor
            );

            const noteElem = list.lastChild;
            setTimeout(() => {
                noteElem.classList.add('show');
            }, idx * 50);
        });
};

/* -------------------- */

if (notes.length > 0) rePaintNotes();

// Function for creating new note
createBtn.onclick = () => {
    let noteID = notes.length === 0 ? 1 : parseInt(notes[notes.length - 1].id) + 1;
    const now = new Date().toISOString();
    const maxZ = notes.reduce((max, n) => Math.max(max, n.zIndex || 0), 0);

    notes.push({
        id: noteID,
        color: color.value,
        x: 90,
        y: 130,
        noteContent: '',
        zIndex: maxZ + 1,
        createdAt: now,
        updatedAt: now,
        width: 250,
        height: 250
    });
    localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    rePaintNotes();
}

const updateNote = (noteID, updatedContent) => {
    // preserve data
    let findNote = notes.find(note => note.id == noteID);
    if (findNote) {
        findNote.noteContent = updatedContent;
        findNote.updatedAt = new Date().toISOString();

        localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    }
    // end preserve data
}

// Function for the close note button
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('close')) {
        const noteElem = event.target.closest('.note'); // go up to the full note container
        modalNoteID = notes.findIndex(note => note.id == noteElem.id);

        if (modalNoteID > -1) {
            modalNoteParent = noteElem;
            toggleModal();
        }
    }
});

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
    let resizingNote = null;
    if (event.target.classList.contains('resize-handle')) {
        resizingNote = event.target.closest('.note');
        const initialWidth = resizingNote.offsetWidth;
        const initialHeight = resizingNote.offsetHeight;
        const initialX = event.clientX;
        const initialY = event.clientY;
    
        document.addEventListener('mousemove', resizeNote);
        document.addEventListener('mouseup', stopResizing);
    
        function resizeNote(e) {
          const width = initialWidth + (e.clientX - initialX);
          const height = initialHeight + (e.clientY - initialY);
    
          resizingNote.style.width = `${width}px`;
          resizingNote.style.height = `${height}px`;
        }
    
        function stopResizing() {
          // Update the note's width and height in the notes array
          const noteId = resizingNote.id;
          const note = notes.find(n => n.id == noteId);
          if (note) {
            note.width = resizingNote.offsetWidth;
            note.height = resizingNote.offsetHeight;
            note.updatedAt = new Date().toISOString();
            localStorage.setItem('unsaved-notes', JSON.stringify(notes));
          }
    
          // Clean up event listeners
          document.removeEventListener('mousemove', resizeNote);
          document.removeEventListener('mouseup', stopResizing);
          resizingNote = null;
        }
    }

    if (event.target.classList.contains('note-header')) {
        cursor = {
            x: event.clientX,
            y: event.clientY
          }
          note = {
            dom: event.target.closest('.note'),
            x: event.target.closest('.note').getBoundingClientRect().left,
            y: event.target.closest('.note').getBoundingClientRect().top,
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

    // ✅ Save actual DOM position instead of mouse position
    let findNote = notes.find(n => n.id == note.dom.id);
    if (findNote) {
        findNote.x = note.dom.offsetLeft;
        findNote.y = note.dom.offsetTop;
        findNote.updatedAt = new Date().toISOString();

        localStorage.setItem('unsaved-notes', JSON.stringify(notes));
    }
    note.dom = null;
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

//BRING TO FRONT
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('bring-front')) {
        const noteElem = e.target.closest('.note');
        const noteId = noteElem.id;

        const note = notes.find(n => n.id == noteId);
        if (!note) return;

        // Sort notes by zIndex ascending
        const sorted = [...notes].sort((a, b) => a.zIndex - b.zIndex);

        // Already on top? Bail
        if (sorted[sorted.length - 1].id == noteId) return;

        // Reorder: move this note to the end (top)
        const newOrder = [...sorted.filter(n => n.id != noteId), note];

        // Apply animation before repaint
        noteElem.classList.add('bump');

        setTimeout(() => {
            // Assign new zIndexes starting from 997
            newOrder.forEach((n, idx) => {
                n.zIndex = 97 + idx;
                n.updatedAt = new Date().toISOString();
            });

            noteElem.classList.remove('bump');

            localStorage.setItem('unsaved-notes', JSON.stringify(notes));
            rePaintNotes();
        }, 200); // Matches the bump animation duration
    }
});

// SEND TO BACK
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('send-back')) {
        const noteElem = e.target.closest('.note');
        const noteId = noteElem.id;

        const note = notes.find(n => n.id == noteId);
        if (!note) return;

        const sorted = [...notes].sort((a, b) => a.zIndex - b.zIndex);

        // Already on bottom? Skip
        if (sorted[0].id == noteId) return;

        // Reorder: move this note to start of array
        const newOrder = [note, ...sorted.filter(n => n.id != noteId)];

        // Animation first
        noteElem.classList.add('fade-back');

        setTimeout(() => {
            newOrder.forEach((n, idx) => {
                n.zIndex = 997 + idx;
                n.updatedAt = new Date().toISOString();
            });

            noteElem.classList.remove('fade-back');

            localStorage.setItem('unsaved-notes', JSON.stringify(notes));
            rePaintNotes();
        }, 300); // Matches fade-back animation duration
    }
});

//GOOGLE ADS
function showFloatingAd() {
    if (window.innerWidth > 728) {
        const adBox = document.getElementById('floatingAd');
        if (adBox) {
            adBox.style.display = "flex";
        }
    }
}

function hideFloatingAd() {
    const adBox = document.getElementById('floatingAd');
    if (adBox) {
        adBox.style.display = "none";
    }
    sessionStorage.setItem("adDismissedAt", Date.now());
}

// Only show floating ad if user hasn’t recently dismissed AND screen is wide enough
const lastDismissed = sessionStorage.getItem("adDismissedAt");
const now = Date.now();
const oneMinute = 1 * 60 * 1000;

if (!lastDismissed || now - lastDismissed > oneMinute) {
    showFloatingAd();
}

// Optional: recheck periodically
setInterval(() => {
    const last = sessionStorage.getItem("adDismissedAt");
    const now = Date.now();
    if (!last || now - last > oneMinute) {
        showFloatingAd();
    }
}, 1 * 10 * 1000); // check every 10seconds

// Optional: resize event listener to hide the floating ad if the user resizes into mobile dimensions after it's been shown
window.addEventListener("resize", () => {
    if (window.innerWidth <= 728) {
        hideFloatingAd();
    }
    else { showFloatingAd(); }
});
