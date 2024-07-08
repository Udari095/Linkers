// To access the stars
let stars = document.getElementsByClassName("star");
let output = document.getElementById("output");

// Function to update rating
function gfg(n) {
    remove();
    for (let i = 0; i < n; i++) {
        if (n == 1) cls = "one";
        else if (n == 2) cls = "two";
        else if (n == 3) cls = "three";
        else if (n == 4) cls = "four";
        else if (n == 5) cls = "five";
        stars[i].className = "star " + cls;
    }
    output.innerText = "Rating is: " + n + "/5";
}

// To remove the pre-applied styling
function remove() {
    let i = 0;
    while (i < 5) {
        stars[i].className = "star";
        i++;
    }
}

// Modal functionality
const modal = document.getElementById("feedbackModal");
const span = document.getElementsByClassName("close")[0];

// Open the modal when the page loads
window.onload = function() {
    modal.style.display = "block";
}

// Close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Form submission handling
document.getElementById('feedbackForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const feedbackData = {
        experience: formData.get('experience'),
        comment: formData.get('comment'),
        area_text: formData.get('area_text'),
        feedback_text: formData.get('feedback_text'),
        rating: formData.get('rating')
    };

    console.log('Feedback received:', feedbackData);

    // Here you would normally send the data to your server.
    // For this example, we will just log it to the console.
    alert('Thank you for your feedback!');

    // Close the modal after submitting
    modal.style.display = "none";
});
