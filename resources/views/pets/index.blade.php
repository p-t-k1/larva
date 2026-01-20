<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pets by Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .status-checkbox {
            display: inline-block;
            margin-right: 20px;
        }
        .status-checkbox label {
            margin-left: 5px;
        }
        .load-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .load-btn:hover {
            background: #45a049;
        }
        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .pet-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: white;
            transition: box-shadow 0.3s;
        }
        .pet-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .pet-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .pet-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .status-available {
            background: #4CAF50;
            color: white;
        }
        .status-pending {
            background: #ff9800;
            color: white;
        }
        .status-sold {
            background: #f44336;
            color: white;
        }
        .pet-detail {
            margin: 5px 0;
            color: #666;
        }
        .pet-detail strong {
            color: #333;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .tags {
            margin-top: 10px;
        }
        .tag {
            display: inline-block;
            background: #e0e0e0;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Find Pets by Status</h1>

        <div class="filters">
            <h3>Filter by Status:</h3>
            <div class="status-checkbox">
                <input type="checkbox" id="status-available" value="available" checked>
                <label for="status-available">Available</label>
            </div>
            <div class="status-checkbox">
                <input type="checkbox" id="status-pending" value="pending">
                <label for="status-pending">Pending</label>
            </div>
            <div class="status-checkbox">
                <input type="checkbox" id="status-sold" value="sold">
                <label for="status-sold">Sold</label>
            </div>
            <div style="margin-top: 15px;">
                <button class="load-btn" onclick="loadPets()">Load Pets</button>
            </div>
        </div>

        <div id="loading" class="loading" style="display: none;">
            Loading pets...
        </div>

        <div id="error" class="error" style="display: none;"></div>

        <div id="pets-container" class="pets-grid"></div>
    </div>

    <script>
        async function loadPets() {
            const checkboxes = document.querySelectorAll('.status-checkbox input[type="checkbox"]');
            const selectedStatuses = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedStatuses.push(checkbox.value);
                }
            });

            if (selectedStatuses.length === 0) {
                showError('Please select at least one status');
                return;
            }

            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const petsContainer = document.getElementById('pets-container');

            loading.style.display = 'block';
            error.style.display = 'none';
            petsContainer.innerHTML = '';

            try {
                const queryParams = selectedStatuses.map(status => `status[]=${status}`).join('&');
                const response = await fetch(`/api/pet/findByStatus?${queryParams}`);

                const data = await response.json();

                loading.style.display = 'none';

                if (response.ok) {
                    if (data.length === 0) {
                        petsContainer.innerHTML = '<div class="empty-state">No pets found with the selected status</div>';
                    } else {
                        data.forEach(pet => {
                            const card = createPetCard(pet);
                            petsContainer.appendChild(card);
                        });
                    }
                } else if (response.status === 400) {
                    showError('Invalid status value');
                } else {
                    showError('An error occurred while fetching pets');
                }
            } catch (err) {
                loading.style.display = 'none';
                showError('Failed to fetch pets. Please try again.');
            }
        }

        function createPetCard(pet) {
            const card = document.createElement('div');
            card.className = 'pet-card';

            const statusClass = `status-${pet.status}`;
            const statusLabel = pet.status.charAt(0).toUpperCase() + pet.status.slice(1);

            let tagsHtml = '';
            if (pet.tags && pet.tags.length > 0) {
                tagsHtml = pet.tags.map(tag => `<span class="tag">${tag.name}</span>`).join('');
            }

            let photoUrlsHtml = '';
            if (pet.photo_urls && pet.photo_urls.length > 0) {
                photoUrlsHtml = `<div class="pet-detail"><strong>Photos:</strong> ${pet.photo_urls.length} photo(s)</div>`;
            }

            card.innerHTML = `
                <div class="pet-status ${statusClass}">${statusLabel}</div>
                <div class="pet-name">${pet.name}</div>
                <div class="pet-detail"><strong>ID:</strong> ${pet.id}</div>
                ${pet.category ? `<div class="pet-detail"><strong>Category:</strong> ${pet.category.name}</div>` : ''}
                ${photoUrlsHtml}
                ${tagsHtml ? `<div class="tags">${tagsHtml}</div>` : ''}
            `;

            return card;
        }

        function showError(message) {
            const error = document.getElementById('error');
            error.style.display = 'block';
            error.textContent = message;
        }

        // Load pets on page load
        document.addEventListener('DOMContentLoaded', loadPets);
    </script>
</body>
</html>
