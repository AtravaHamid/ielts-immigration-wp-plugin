window.IELTSBoard = window.IELTSBoard || {};

// Initialize board after DOM ready.
IELTSBoard.init = function () {
    var container = document.querySelector('.ielts-board');
    if (!container) {
        return;
    }
    var mode = container.dataset.mode;
    var item = container.dataset.item;
    if (mode && typeof IELTSBoard[mode] === 'function') {
        IELTSBoard[mode](container, item);
    }
};

document.addEventListener('DOMContentLoaded', IELTSBoard.init);
