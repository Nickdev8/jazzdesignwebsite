// JavaScript for handling gallery modal display only

// Modal elements and gallery items
const modal = document.getElementById('image-modal');
const modalImg = document.getElementById('modal-image');
const modalTitle = document.getElementById('modal-title');
const modalDesc = document.getElementById('modal-description');
const galleryItems = document.querySelectorAll('.gallery-item');
let currentIndex = 0;

// Open modal and display the clicked image
galleryItems.forEach((item, index) => {
    item.addEventListener('click', function () {
        modal.style.display = 'flex';
        currentIndex = index;
        showImage(currentIndex);
    });
});

// Show image in modal based on index
function showImage(index) {
    const item = galleryItems[index];
    if (item) {
        modalImg.src = item.src;
        modalTitle.textContent = item.getAttribute('data-title') || "";
        modalDesc.textContent = item.getAttribute('data-description') || "";
    }
}

// Close modal functionality
document.querySelector('.close').onclick = function () {
    modal.style.display = 'none';
};

// Navigate to the next image
document.querySelector('.next').onclick = function () {
    currentIndex = (currentIndex + 1) % galleryItems.length;
    showImage(currentIndex);
};

// Navigate to the previous image
document.querySelector('.prev').onclick = function () {
    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
    showImage(currentIndex);
};

// Close modal if clicked outside modal content
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
};
