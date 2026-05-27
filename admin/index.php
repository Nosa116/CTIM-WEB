<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Christ Temple</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-dashboard">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <a href="../index.html">
                <img src="../assets/images/Christ Temple logo.svg" alt="Christ Temple">
            </a>
            <span>Admin</span>
        </div>
        <nav class="admin-nav">
            <a href="#" class="admin-nav-link active">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>
            <a href="#" class="admin-nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Manage Sermons
            </a>
            <a href="#" class="admin-nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
            </a>
            <a href="logout.php" class="admin-nav-link logout-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-title">
                <h1>Sermons Dashboard</h1>
                <p>Upload and manage audio sermons</p>
            </div>
            <div class="header-profile">
                <div class="profile-avatar">A</div>
                <span>Admin User</span>
            </div>
        </header>

        <div class="admin-content">
            <!-- Upload Form Card -->
            <section class="admin-card upload-section">
                <h2 class="card-title">Upload New Sermon</h2>
                <form id="sermonUploadForm" class="upload-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sermonTitle">Sermon Title</label>
                            <input type="text" id="sermonTitle" name="title" placeholder="e.g. The Original" required>
                        </div>
                        <div class="form-group">
                            <label for="sermonSpeaker">Speaker</label>
                            <input type="text" id="sermonSpeaker" name="speaker" placeholder="e.g. Apostle Emmanuel" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sermonDate">Date Preached</label>
                            <input type="date" id="sermonDate" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="sermonCategory">Category</label>
                            <select id="sermonCategory" name="category" required>
                                <option value="" disabled selected>Select category...</option>
                                <option value="Purpose">Purpose</option>
                                <option value="Salvation">Salvation</option>
                                <option value="Faith">Faith</option>
                                <option value="Grace">Grace</option>
                                <option value="Healing">Healing</option>
                                <option value="ELF">ELF</option>
                            </select>
                        </div>
                    </div>

                    <div class="file-upload-grid">
                        <!-- Cover Image Upload -->
                        <div class="file-upload-box">
                            <label>Cover Image (JPG/PNG)</label>
                            <div class="upload-area" id="imageUploadArea">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span id="imageUploadText">Click to upload image</span>
                                <input type="file" id="coverImage" name="coverImage" accept="image/png, image/jpeg" hidden required>
                            </div>
                        </div>

                        <!-- Audio File Upload -->
                        <div class="file-upload-box">
                            <label>Audio File (MP3)</label>
                            <div class="upload-area" id="audioUploadArea">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                <span id="audioUploadText">Click to upload audio</span>
                                <input type="file" id="audioFile" name="audioFile" accept="audio/mp3, audio/mpeg" hidden required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Upload Sermon</button>
                    </div>
                </form>
            </section>

            <!-- Recent Sermons Table -->
            <section class="admin-card list-section">
                <div class="list-header">
                    <h2 class="card-title">Recent Uploads</h2>
                    <div class="search-small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Search...">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Speaker</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sermonsTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Handle file input clicks and text updates
        const imageInput = document.getElementById('coverImage');
        const audioInput = document.getElementById('audioFile');
        const imageText = document.getElementById('imageUploadText');
        const audioText = document.getElementById('audioUploadText');

        document.getElementById('imageUploadArea').addEventListener('click', () => {
            imageInput.click();
        });
        document.getElementById('audioUploadArea').addEventListener('click', () => {
            audioInput.click();
        });

        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                imageText.textContent = e.target.files[0].name;
            } else {
                imageText.textContent = "Click to upload image";
            }
        });

        audioInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                audioText.textContent = e.target.files[0].name;
            } else {
                audioText.textContent = "Click to upload audio";
            }
        });

        // Handle form submission via AJAX
        document.getElementById('sermonUploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            submitBtn.textContent = 'Uploading...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            try {
                const response = await fetch('upload_sermon.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    alert('Sermon uploaded successfully!');
                    form.reset();
                    imageText.textContent = "Click to upload image";
                    audioText.textContent = "Click to upload audio";
                    fetchSermons(); // Refresh the list
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error uploading sermon:', error);
                alert('An unexpected error occurred during upload.');
            } finally {
                submitBtn.textContent = 'Upload Sermon';
                submitBtn.disabled = false;
            }
        });

        // Fetch recent sermons
        async function fetchSermons() {
            try {
                const response = await fetch('../api/get_sermons.php?limit=5');
                const result = await response.json();
                
                if (result.status === 'success') {
                    const tbody = document.getElementById('sermonsTableBody');
                    tbody.innerHTML = '';
                    
                    if (result.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No sermons found.</td></tr>';
                        return;
                    }
                    
                    result.data.forEach(sermon => {
                        tbody.innerHTML += `
                            <tr>
                                <td>
                                    <div class="sermon-name-cell">
                                        <img src="../${sermon.cover_image_path}" alt="Cover" class="tiny-cover">
                                        <span>${sermon.title}</span>
                                    </div>
                                </td>
                                <td>${sermon.speaker}</td>
                                <td><span class="badge">${sermon.category}</span></td>
                                <td>${sermon.formatted_date}</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="icon-btn edit" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button class="icon-btn delete" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
            } catch (error) {
                console.error('Error fetching sermons:', error);
            }
        }

        // Initial fetch
        document.addEventListener('DOMContentLoaded', fetchSermons);
    </script>
</body>
</html>
