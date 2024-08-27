document.addEventListener("DOMContentLoaded", function() {
  const loader = document.getElementById('loader');
  const content = document.getElementById('content');

  setTimeout(function() {
    loader.style.display = 'none';
    content.style.display = 'block';
  }, 3000);
});
