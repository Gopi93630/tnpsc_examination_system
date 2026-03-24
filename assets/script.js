// 👁️ SHOW / HIDE PASSWORD
function togglePassword(inputId, iconId){
    let input = document.getElementById(inputId);
    let icon = document.getElementById(iconId);

    if(!input || !icon) return;

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

//////////////////////////////////////////////////////
// 🔐 LOGIN VALIDATION
//////////////////////////////////////////////////////
function validateLogin(){
    let email = document.getElementById("login_email");
    let password = document.getElementById("login_password");
    let msg = document.getElementById("success-msg");

    if(!email || !password) return true;

    if(email.value.trim() === "" || password.value.trim() === ""){
        alert("Please fill all fields!");
        return false;
    }

    if(msg){
        msg.innerText = "Login Successful!";
        msg.style.display = "block";
    }

    return true;
}

//////////////////////////////////////////////////////
// 📝 REGISTER VALIDATION
//////////////////////////////////////////////////////
function validateRegister(){
    let name = document.getElementById("name");
    let email = document.getElementById("register_email");
    let password = document.getElementById("register_password");
    let msg = document.getElementById("success-msg");

    if(!name || !email || !password) return true;

    if(name.value.trim() === "" || email.value.trim() === "" || password.value.trim() === ""){
        alert("All fields are required!");
        return false;
    }

    if(password.value.length < 6){
        alert("Password must be at least 6 characters!");
        return false;
    }

    if(msg){
        msg.innerText = "Registration Successful!";
        msg.style.display = "block";
    }

    return true;
}

//////////////////////////////////////////////////////
// 🔁 FORGOT PASSWORD VALIDATION (INLINE ERROR)
//////////////////////////////////////////////////////
function validateReset(){
    let email = document.getElementById("email");
    let pass = document.getElementById("password");
    let confirm = document.getElementById("confirm_password");
    let error = document.getElementById("error-msg");

    if(!email || !pass || !confirm || !error) return true;

    error.style.display = "block";
    error.innerText = "";

    if(email.value.trim() === "" || pass.value.trim() === "" || confirm.value.trim() === ""){
        error.innerText = "All fields are required!";
        return false;
    }

    if(pass.value.length < 6){
        error.innerText = "Password must be at least 6 characters!";
        return false;
    }

    if(pass.value !== confirm.value){
        error.innerText = "Passwords do not match!";
        return false;
    }

    return true;
}

//////////////////////////////////////////////////////
// ⏱️ EXAM TIMER
//////////////////////////////////////////////////////
function startTimer(minutes){
    let time = minutes * 60;
    let timer = document.getElementById("timer");

    if(!timer) return;

    let interval = setInterval(function(){
        let min = Math.floor(time / 60);
        let sec = time % 60;

        sec = sec < 10 ? "0" + sec : sec;

        timer.innerHTML = "Time Left: " + min + ":" + sec;

        time--;

        if(time < 0){
            clearInterval(interval);
            alert("Time Up! Exam Submitted.");
            let form = document.getElementById("examForm");
            if(form) form.submit();
        }

    }, 1000);
}

//////////////////////////////////////////////////////
// 🗑️ DELETE CONFIRMATION (ADMIN)
//////////////////////////////////////////////////////
function confirmDelete(){
    return confirm("Are you sure you want to delete?");
}

//////////////////////////////////////////////////////
// 🌙 DARK MODE TOGGLE
//////////////////////////////////////////////////////
function toggleDarkMode(){
    document.body.classList.toggle("dark-mode");
}

//////////////////////////////////////////////////////
// ⚡ AUTO HIDE SUCCESS MESSAGE
//////////////////////////////////////////////////////
window.onload = function(){
    let msg = document.getElementById("success-msg");
    if(msg){
        setTimeout(() => {
            msg.style.display = "none";
        }, 3000);
    }
};