// static/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    // This tells the browser to call the /api/data route we defined in app.py
    fetch('/api/data')
        .then(response => response.json())
        .then(data => {
            console.log("Data from MongoDB:", data);
            
            // Example: Display the data in your HTML
            const container = document.getElementById('data-container');
            container.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
        })
        .catch(error => console.error('Error fetching data:', error));
});