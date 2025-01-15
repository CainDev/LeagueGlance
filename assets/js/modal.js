var reportModal = document.getElementById('modal');
var bugModal = document.getElementById('report-modal');

function openModal(modal){
    switch(modal){
        case 1:
            // Report Modal
            reportModal.style.display = "block";
            return true;
        case 2:
            break;
        case 3:
            break;
    }
}

function closeModal(modal){
    switch(modal){
        case 1:
            // Report Modal
            reportModal.style.display = "none";
            return true;
        case 2:
            break;
        case 3:
            break;
    }
}