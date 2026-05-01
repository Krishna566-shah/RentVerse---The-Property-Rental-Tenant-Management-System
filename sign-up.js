//signUp form
function validateSignUp() {
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const Cpassword = document.getElementById("Cpassword").value;
    const role = document.getElementById("role").value;
    const terms = document.getElementById("terms").checked;


    // Email check
    if (email === "") {
        alert("Please enter your email");
        return false;
    }

    //email pattern
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email.match(emailPattern)) {
        alert("Enter a valid email address!");
        return false;
    }

    // password pattern: min 6 characters, includes uppercase, lowercase, digit, special
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;

    if (!password.match(passwordPattern)) {
        alert("Password must be at least 6 characters and include an uppercase letter, lowercase letter, number, and special character.");
        return false;
    }

    if(Cpassword !== password){
        alert("Confirm Password should be same as Password!");
        return false;
    }

    // Role selection
    if (role === "") {
        alert("Please select your role");
        return false;
    }

    // Terms checkbox
    if (!terms) {
        alert("Please accept Terms of Service and Privacy Policy");
        return false;
    }
    
    return true;
}