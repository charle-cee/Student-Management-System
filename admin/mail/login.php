<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup Form</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a192f, #1a1a2e);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .wrapper {
            width: 90%;
            max-width: 400px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
            overflow: hidden;
            position: relative;
            padding: 20px;
            text-align: center;
        }

        .form-box {
            display: none;
            transition: transform 0.5s ease-in-out;
        }

        .form-box.active {
            display: block;
        }

        h2 {
            color: #ffd700;
            margin-bottom: 20px;
        }

        .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .input-box input {
            width: 100%;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #ffd700;
            background: transparent;
            color: #fff;
            font-size: 16px;
            outline: none;
        }

        .input-box label {
            position: absolute;
            top: 10px;
            left: 0;
            font-size: 16px;
            color: #ffd700;
            transition: 0.5s;
        }

        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: -10px;
            font-size: 12px;
            color: #ffd700;
        }

        .input-box i {
            position: absolute;
            right: 0;
            bottom: 10px;
            font-size: 20px;
            color: #ffd700;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background: #ffd700;
            border: none;
            border-radius: 5px;
            color: #0a192f;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #c0a040;
        }

        .linkTxt {
            margin-top: 10px;
            color: #fff;
        }

        .linkTxt a {
            color: #ffd700;
            text-decoration: none;
            font-weight: bold;
        }

        .linkTxt a:hover {
            text-decoration: underline;
        }

        /* Message Box */
        .message {
            display: none;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .error { background: #ff4d4d; color: white; }
        .success { background: #28a745; color: white; }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Login Form -->
    <div class="form-box login active">
        <h2>Login</h2>
        <form id="loginForm">
            <div class="input-box">
                <input type="email" id="loginEmail" required autocomplete="off">
                <label>Email</label>
                <i class='bx bxs-envelope'></i>
            </div>

            <div class="input-box">
                <input type="password" id="loginPassword" required autocomplete="off">
                <label>Password</label>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="button" class="btn" onclick="handleAuth('login')">Login</button>

            <div class="linkTxt">
                <p>Don't have an account? <a href="#" class="register-link">Sign Up</a></p>
            </div>

            <div class="message" id="loginMessage"></div>
        </form>
    </div>

    <!-- Registration Form -->
    <div class="form-box register">
        <h2>Sign Up</h2>
        <form id="registerForm">
            <div class="input-box">
                <input type="text" id="fullname" required autocomplete="off">
                <label>Full Name</label>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box">
                <input type="email" id="registerEmail" required autocomplete="off">
                <label>Email</label>
                <i class='bx bxs-envelope'></i>
            </div>

            <div class="input-box">
                <input type="password" id="registerPassword" required autocomplete="off">
                <label>Password</label>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="button" class="btn" onclick="handleAuth('register')">Sign Up</button>

            <div class="linkTxt">
                <p>Already have an account? <a href="#" class="login-link">Login</a></p>
            </div>

            <div class="message" id="registerMessage"></div>
        </form>
    </div>
</div>

<script>
    const loginForm = document.querySelector('.form-box.login');
    const registerForm = document.querySelector('.form-box.register');
    const loginLink = document.querySelector('.login-link');
    const registerLink = document.querySelector('.register-link');

    registerLink.addEventListener('click', () => {
        loginForm.classList.remove('active');
        registerForm.classList.add('active');
    });

    loginLink.addEventListener('click', () => {
        registerForm.classList.remove('active');
        loginForm.classList.add('active');
    });

    async function handleAuth(action) {
        const email = action === 'login' ? document.getElementById('loginEmail').value : document.getElementById('registerEmail').value;
        const password = action === 'login' ? document.getElementById('loginPassword').value : document.getElementById('registerPassword').value;
        const fullname = action === 'register' ? document.getElementById('fullname').value : '';

        const formData = new FormData();
        formData.append('action', action);
        formData.append('email', email);
        formData.append('password', password);
        if (fullname) formData.append('fullname', fullname);

        const response = await fetch('auth.php', { method: 'POST', body: formData });
        const result = await response.json();

        const messageBox = action === 'login' ? document.getElementById('loginMessage') : document.getElementById('registerMessage');
        messageBox.textContent = result.message;
        messageBox.className = 'message ' + result.status;
        messageBox.style.display = 'block';

        if (result.status === 'success') {
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 3000);
        }
    }
</script>

</body>
</html>
