// ================================
// SHOW / HIDE PASSWORD
// ================================
function togglePassword(inputId, iconId){
    let input = document.getElementById(inputId);
    let icon = document.getElementById(iconId);
    if(input.type === "password"){
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

// ================================
// LOGIN FORM VALIDATION
// ================================
function validateLogin(){
    let email = document.getElementById("login_email").value;
    let password = document.getElementById("login_password").value;

    if(email === "" || password === ""){
        alert("Please fill all fields!");
        return false;  // prevent form submission
    }

    // ✅ Show success message (green)
    let msg = document.getElementById("success-msg");
    if(msg){  // check if success message div exists
        msg.innerText = "Login Successful!";
        msg.style.display = "block";
    }

    return true;  // allow form submission
}

// ================================
// REGISTER FORM VALIDATION
// ================================
function validateRegister(){
    let name = document.getElementById("name").value;
    let email = document.getElementById("register_email").value;
    let password = document.getElementById("register_password").value;

    if(name === "" || email === "" || password === ""){
        alert("All fields are required!");
        return false;  // prevent form submission
    }

    if(password.length < 6){
        alert("Password must be at least 6 characters!");
        return false;  // prevent form submission
    }

    // ✅ Show success message (green)
    let msg = document.getElementById("success-msg");
    if(msg){  // check if success message div exists
        msg.innerText = "Registration Successful!";
        msg.style.display = "block";
    }

    return true;  // allow form submission
}

// ================================
// OPTIONAL: EXAM TIMER FUNCTION
// (if you want, can integrate here later)
// ================================
function startTimer(minutes) {
    let timerDisplay = document.getElementById("timer");
    if(!timerDisplay) return;

    let time = minutes * 60;
    let interval = setInterval(function(){
        let mins = Math.floor(time/60);
        let secs = time % 60;
        timerDisplay.innerText = mins + ":" + (secs < 10 ? "0"+secs : secs);
        time--;

        if(time < 0){
            clearInterval(interval);
            timerDisplay.innerText = "Time's up!";
            // auto-submit form if needed
            let examForm = document.getElementById("examForm");
            if(examForm) examForm.submit();
        }
    }, 1000);
}