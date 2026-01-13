const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');

if (hamburger) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
  });
}

let quantity = 1;

function openModal(item) {
  quantity = 1;
  document.getElementById('qty').textContent = quantity;

  document.getElementById('modalImg').src =
    item.querySelector('img').src;
  document.getElementById('modalTitle').textContent =
    item.querySelector('h3').textContent;
  document.getElementById('modalPrice').textContent =
    item.querySelector('.price').textContent;
  document.getElementById('modalDetails').textContent =
    item.querySelector('.details').textContent;

  document.getElementById('menuModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('menuModal').style.display = 'none';
}

window.onclick = (e) => {
  const modal = document.getElementById('menuModal');
  if (e.target === modal) closeModal();
};

function changeQty(val) {
  quantity += val;
  if (quantity < 1) quantity = 1;
  document.getElementById('qty').textContent = quantity;
}

function filterMenu(category) {
  document.querySelectorAll('.menu-item').forEach(item => {
    item.style.display =
      category === 'all' || item.classList.contains(category)
        ? 'block'
        : 'none';
  });
}

function searchMenu() {
  const value = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.menu-item').forEach(item => {
    item.style.display =
      item.querySelector('h3').textContent.toLowerCase().includes(value)
        ? 'block'
        : 'none';
  });
}
