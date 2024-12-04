
const modal = document.getElementById('image-modal');
const modalImg = document.getElementById('modal-image');
const modalTitle = document.getElementById('modal-title');
const modalDesc = document.getElementById('modal-description');
const galleryItems = document.querySelectorAll('.gallery-item');
let currentIndex = 0;

galleryItems.forEach((item, index) => {
    item.addEventListener('click', function () {
        modal.style.display = 'flex';
        currentIndex = index;
        showImage(currentIndex);
    });
});

function showImage(index) {
    const item = galleryItems[index];
    if (item) {
        modalImg.src = item.src;
        modalTitle.textContent = item.getAttribute('data-title') || "";
        modalDesc.textContent = item.getAttribute('data-description') || "";
    }
}

document.querySelector('.close').onclick = function () {
    modal.style.display = 'none';
};

document.querySelector('.next').onclick = function () {
    currentIndex = (currentIndex + 1) % galleryItems.length;
    showImage(currentIndex);
};

document.querySelector('.prev').onclick = function () {
    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
    showImage(currentIndex);
};

window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
};
