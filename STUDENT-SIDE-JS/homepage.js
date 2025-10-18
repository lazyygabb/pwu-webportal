const toggleButton = document.getElementById('toggle-btn') 
const sidebar = document.getElementById('sidebar') 

function toggleSidebar(){ 
    sidebar.classList.toggle('close') 
    toggleButton.classList.toggle('rotate') 
    
   
    sidebar.classList.toggle('sidebar-collapsed')
    
    
    const pageContent = document.querySelector('.page-content');
    if (pageContent) {
        pageContent.classList.toggle('sidebar-collapsed');
    }
    
    closeAllSubMenus() 
} 

function toggleSubMenu(button){ 
    if(!button.nextElementSibling.classList.contains('show')){ 
        closeAllSubMenus() 
    } 
    
    button.nextElementSibling.classList.toggle('show')
    button.classList.toggle('rotate') 

} 

function closeAllSubMenus(){
    Array.from(sidebar.getElementsByClassName('show')).forEach(ul => { 
        ul.classList.remove('show') 
        ul.previousElementSibling.classList.remove('rotate') 
    }) 
}

document.addEventListener('DOMContentLoaded', function() {

    const subjectItems = document.querySelectorAll('.subject-item');
    subjectItems.forEach(item => {
        item.addEventListener('click', function() {
            const subjectName = this.querySelector('.subject-name').textContent;
            alert(`Viewing details for: ${subjectName}`);

        });
    });
    
  
    const opportunityItems = document.querySelectorAll('.opportunity-item');
    opportunityItems.forEach(item => {
        item.addEventListener('click', function() {
            const itemTitle = this.querySelector('.item-title').textContent;
            alert(`Viewing details for: ${itemTitle}`);
  
        });
    });
    
    
    const viewAllLinks = document.querySelectorAll('.view-all-link');
    viewAllLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.closest('.column').querySelector('.column-title').textContent;
            alert(`Viewing all ${section}`);
  
        });
    });
});

const express = require('express');
const mysql = require('mysql2');
const path = require('path');
const { createCanvas } = require('canvas');
const app = express();

app.use(express.json());
app.use(express.static('public'));

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'system_pwu'
});

db.connect(err => {
    if (err) throw err;
    console.log('Database connected');
});

function generateProfilePicture(name, width = 200, height = 200) {
    const canvas = createCanvas(width, height);
    const ctx = canvas.getContext('2d');

    // Background colors
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
    ];
    
    // Get initials
    const initials = name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);

    // Random color based on name (for consistency)
    const colorIndex = name.charCodeAt(0) % colors.length;
    const backgroundColor = colors[colorIndex];

    // Draw background
    ctx.fillStyle = backgroundColor;
    ctx.fillRect(0, 0, width, height);

    // Draw text
    ctx.fillStyle = '#FFFFFF';
    ctx.font = `bold ${width / 3}px Arial`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(initials, width / 2, height / 2);

    return canvas.toBuffer();
}

// API endpoint to get or generate profile picture
app.get('/api/profile-picture/:studentId', (req, res) => {
    const studentId = req.params.studentId;

    // Check if profile picture exists in database
    const query = 'SELECT profile_picture_url, first_name, last_name FROM students WHERE student_id = ?';
    
    db.query(query, [studentId], (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Database error' });
        }

        if (results.length === 0) {
            return res.status(404).json({ error: 'Student not found' });
        }

        const student = results[0];
        
        // If profile picture exists, redirect to it
        if (student.profile_picture_url) {
            return res.redirect(student.profile_picture_url);
        }

        // Generate new profile picture
        const fullName = `${student.first_name} ${student.last_name}`;
        const imageBuffer = generateProfilePicture(fullName);

        // Save to database (optional - you can save the generated image to file system)
        const profilePicUrl = `/generated-images/${studentId}.png`;
        const updateQuery = 'UPDATE students SET profile_picture_url = ? WHERE student_id = ?';
        
        db.query(updateQuery, [profilePicUrl, studentId], (err) => {
            if (err) {
                console.error('Error updating profile picture URL:', err);
            }
        });

        // Return the generated image
        res.set({
            'Content-Type': 'image/png',
            'Content-Length': imageBuffer.length,
            'Cache-Control': 'public, max-age=86400' // Cache for 24 hours
        });
        res.send(imageBuffer);
    });
});

// API to add new student
app.post('/api/students', (req, res) => {
    const { studentId, firstName, lastName, email } = req.body;

    const query = 'INSERT INTO students (student_id, first_name, last_name, email) VALUES (?, ?, ?, ?)';
    
    db.query(query, [studentId, firstName, lastName, email], (err, results) => {
        if (err) {
            return res.status(500).json({ error: 'Error adding student' });
        }
        res.json({ message: 'Student added successfully', id: results.insertId });
    });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});